<?php



trait thfModuleTools{
	protected static $mvcFolders=array(
			'Controller'=>'Controllers',
			'Model'=>'Models',
			'View'=>'Views',
			'Route'=>'Routes',
			);

	protected static $controllerTypes=array('Middleware','Code','After');
	protected static $getModuleFormattingCache=array();


    public static function isMvcParameterValid(string $mvc, string $classEnding): bool
    {
        if (!isset(self::$mvcFolders[$mvc]) || !in_array($classEnding, self::$controllerTypes) && $mvc == 'Controller') {
            return false;
        }
        return true;
    }


    public static function isModuleController($moduleName,$classShortName,$controllerClassType='Code'){
		if(!in_array($controllerClassType,self::$controllerTypes)){thfRequest::e500('Invalid controller class type: "'.$controllerClassType.'"');}
		return self::isModuleClass('Controller',$moduleName,$classShortName,$controllerClassType);
	}
	public static function isModuleModel($moduleName,$classShortName){
		return self::isModuleClass('Model',$moduleName,$classShortName);
	}
	public static function isModuleView($moduleName,$classShortName){
		return self::isModuleClass('View',$moduleName,$classShortName);
	}
	public static function isModuleRouteStatic($moduleName){
		return self::isModuleClass('Route',$moduleName,'Module','Static');
	}
	public static function isModuleRouteGlobal($moduleName){
		return self::isModuleClass('Route',$moduleName,'Global','Static');
	}
	private static function isModuleClass($mvc,string $moduleName='Default',string $classShortName='Index',$classEnding=''){
		$c=self::getModuleFormatting($moduleName,$classShortName,$mvc,$classEnding);
		return $c['phpExists'.$classEnding] && ($mvc!='Route' || isset($c['functions'.$classEnding]['addStaticRoutes']));	
	}

	public static function getModuleFormatting($moduleName,$classShortName,$mvc,$classEnding){
		if(!self::isMvcParameterValid( $mvc, $classEnding)){return null;}
		$moduleName=ucwords($moduleName);
		$classShortName=ucwords($classShortName);
		
		$class=$moduleName.'_'.$classShortName.'_'.$mvc.$classEnding;
		$classFile=MODULES_DIR.DS.$moduleName.DS.self::$mvcFolders[$mvc].DS.$class.'.php';
		if(isset(self::$getModuleFormattingCache[$classFile])){
			return self::$getModuleFormattingCache[$classFile]; //use memory cache
		}
		
		$out=array(
			'className'.$classEnding=> $class,
			'classFile'.$classEnding=> $classFile,
			'phpExists'.$classEnding=> ( is_file($classFile) && is_readable($classFile)),
			'functions'.$classEnding=> null,
		);
		if($out['phpExists'.$classEnding]){
			$out['functions'.$classEnding] = self::listPublicMethods($class);
			if($out['functions'.$classEnding]===false){
				$out['phpExists'.$classEnding]=0;
				if(thfRouter::$haltOnInvalidControlerDefinition && thfConfig::getInstance()->is_debug()){
					$cf=implode("', '",array_keys(self::getModuleClassFileFunctionList($classFile)));
					thfRequest::e500("<hr>\r\nError when checking file: $classFile <br>\r\nClass '$class' was expected but we found: '<strong>".$cf."</strong>' ");
				}
			}
			elseif(!$out['functions'.$classEnding]){ //blank funnction list
				$out['phpExists'.$classEnding]=0; //invalidate class file
			}
			
		}
		return self::$getModuleFormattingCache[$classFile]=$out;
	}

	public static function classMethodExists($className, $method){
		if(!class_exists($className,true)){ return false;}
		$refObj = new \ReflectionClass($className);
		return $refObj->hasMethod($method);
	}
	
	public static function listPublicMethods($className){
		if(!class_exists($className,true)){ return false;}
		$refObj = new \ReflectionClass($className);
		$out=array();
		
		foreach($refObj->getMethods( \ReflectionMethod::IS_PUBLIC ) as $method){
			$out[$method->name]=$method->name;
		}
		return $out;
	}
	
	public static function getModuleClassFileFunctionList($file) { //used to parse what classes are found into a file for debugging
		$source = file_get_contents($file);
		$tokens = token_get_all($source);
		$functions = array();
		$nextStringIsFunc = $nextStringIsClass = false;
		$className='\\';
		$inClass = false;
		$bracesCount = 0;

		foreach($tokens as $token) {
			switch($token[0]) {
				case T_CLASS:
					$inClass = true;
					$nextStringIsClass=true;
					break;
				case T_FUNCTION:
					$nextStringIsFunc = true;
					break;

				case T_STRING:
					if($nextStringIsFunc) {
						$nextStringIsFunc = false;
						$functions[$className][] = $token[1];
					}
					if($nextStringIsClass) {
						$nextStringIsClass = false;
						$className = $token[1];
					}
					break;

				// Anonymous functions
				case '(':
				case ';':
					$nextStringIsFunc = false;
					break;

				// Exclude Classes
				case '{':
					if($inClass) $bracesCount++;
					break;

				case '}':
					if($inClass) {
						$bracesCount--;
						if($bracesCount === 0){
							$inClass = false;
							$className='\\';
						}
					}
					break;
			}
		}

		return $functions;
	}
	
	
	public static function validateRequestModules($requestModuleAccept,$controllers=array()){
		//echo '<h1>~~~~~~~~~~~~~~~~~~~~~~</h1>'; prea($requestModuleAccept);		echo '<h1>~~~~~~~~~~~~~~~~~~~~~~</h1>';
		
		foreach(array('ModuleRoute','DefaultRoute') as $modDir){
			$m = $requestModuleAccept['Module'][$modDir];
			if($m && $m['phpExistsCode'] && (isset($m['functionsCode'][$m['methodAction']]) || isset($m['functionsCode'][$m['methodAnyAction']]))){ // try module route / default route
				foreach(self::$controllerTypes as $ct){
					$action = isset($m['functions'.$ct][$m['methodAction']])?$m['methodAction']:null;
					if(!$action){
						$action = isset($m['functions'.$ct][$m['methodAnyAction']])?$m['methodAnyAction']:null;
					}
					if($m['phpExists'.$ct] && $action){
						$controllers[$ct]=array(
							'modul'=>$m['modul'],
							'cl'=>$m['cl'],
							'method'=>$m['method'],
							'requestMethod'=>$m['requestMethod'],
							'invokeMethod'=>$action, //this method will be called from this class
							'params'=>$m['params'],
							'className'=>$m['className'.$ct],
							'classFile'=>$m['classFile'.$ct],
							'invoke'=>$m['className'.$ct].'@'.$action,
							//'namespace'=>thfRouter::getNamespace(), //??? ar putea fi mutat direct in invoke
							//'methodAction'=>$m['methodAction'],
							//'methodAnyAction'=>$m['methodAnyAction'],
							//'functions'=>$m['functions'.$ct],
							
						);
					}

				}
				break;
			}
			
		}
	
		return $controllers;
		
	}



}
