<?php


/**
 * Router class for managing routes and request handling.
 *
 * Philosophy: 3 layer routing: Middleware-> Route(Match) -> After
 * Any request type, including CLI
 * FROM MULTIPLE MODULES inside VENDOR FOLDER OR PROJECT FOLDER
 *
 * ??????
 *
 * CUM RUTAM RESURSELE CSS SI JS DIN MODULE ??
 * DACA ESTE IN VENDOR ATUNCI AVEM IMUTABLE? SAU LE INCLUDEM VERS DIN composer.json cu un filemtime?
 *
 */

class thfRouter  extends thfRequest // extends thfSingleton
{
	use thfHeader;
	use thfDirTools;
	use \Autoframe\Core\Object\AfrObjectInvoker;
	//use thfModuleTools;
	
	public static $haltOn405=true; 
	public static $haltOnInvalidControlerDefinition=true;
	
/*	protected static $requestModes=array(
		'classicSlugFullLink'=>false, //tbl pagini si global namespace
		);
*/
    public static $middlewareRoutes = []; //middleware
    public static $codeRoutes = [];
    public static $afterRoutes = []; //run after code routes
    private static $fnStack = []; //variable that keeps the anonymus route functions
	
    private static $maxRoutes = array('Middleware'=>50,'Code'=>1,'After'=>20,);//if this number is exceeded, the remaining routes will be skipped on the execution level without any warning
	
	
	private static $routePriorityOrder=array(
		'Global_RouteStatic|GX',	//this should be first and it should not contain any code routes!
		'Module_RouteStatic|B',	//each module can run static routes if module, if no Module_RouteStatic routes are found, then run default-static routes
		'class@method|B', 	//class and methodAction call
		/*
		modifyers / parameters:
		|B break after loop if a code root was previously found;    [STOP]
		|C continue before running the loop if a code root was previously found; [SKIP]
		|G run in all cases to register the all route types even if 404 will be generated because of no route validation; usfull for global procedures
		|M count middleware routes as code routes
		|A count after routes as code routes
		|X skip and purge code routes
		|Y skip and purge middleware routes
		|Z skip and purge after routes
		|  no effect
		*/
	); 
	public static function setRoutePriorityOrder($routePriorityOrder){
		return self::$routePriorityOrder=$routePriorityOrder;
	}
	
	
	private static $staticMouleRouteEmulation=array('on'=>false);
	
	public static function startStaticMouleRouteEmulation(){
		self::cleanStaticMouleRouteEmulation();
		self::$staticMouleRouteEmulation['on']=true;
	}
	public static function cleanStaticMouleRouteEmulation(){
		self::$staticMouleRouteEmulation['on']=false;
		self::$staticMouleRouteEmulation['count']=array('Code'=>0,'Middleware'=>0,'After'=>0);
		self::$staticMouleRouteEmulation['cache']=array('Code'=>[],'Middleware'=>[],'After'=>[]);
	}
	public static function commitStaticMouleRouteEmulation(){
		self::$staticMouleRouteEmulation['on']=false; //prevent second add
		foreach(self::$staticMouleRouteEmulation['cache'] as $type=>$cache){
			foreach($cache as $ci=>$params){
				$ns=self::getNamespace();
				if($ns){$ns.='\\';}
				self::invokeRoute($ns.__CLASS__.'@addRouteMethod',$params); //bind the routes for real so that they can be later executed
				//forward_static_call_array([__CLASS__, 'addRouteMethod'], $params);
			}
		}
		self::cleanStaticMouleRouteEmulation();
	}
	
    /**
     * @var object|callable The function to be executed when no route has been matched
     */
    protected static $notFound404Callback;

    /**
     * @var string Current base route, used for (sub)route mounting
     */
    private static $baseRoute = '';



    /**
     * @var string Default Controllers Namespace
     */
    private static $namespace = '';

	public static function setMaxRoutes($to,$type){
		$type=ucwords($type);
		if(!isset(self::$maxRoutes[$type])){ die('Invalid type="'.$type.'" for setMaxRoutes\$maxRoutes[$type]'); }
		$to=intval($to);
		if($to<0){$to=0;}//prevent negative values; no code route should run so generate a 404
		return self::$maxRoutes[$type]=$to; //sky is the limit
	}
	public static function getMaxRoutes($type){
		return isset(self::$maxRoutes[$type])?self::$maxRoutes[$type]:0;
	}
	public static function loopMaxRoutes($type){return self::setMaxRoutes(self::getMaxRoutes($type)-1,$type);}

	


	public static function getRoutes($code_dump=0){
		$out=array('Middleware'=>self::$middlewareRoutes,'Code'=>self::$codeRoutes,'After'=>self::$afterRoutes,);
		if(!$code_dump){
			return array_merge($out,array('fnStack'=>self::$fnStack));
		}
		$fnStackCode=array();
		$imax=count(self::$fnStack);
		for($i=0;$i<$imax;$i++){
			$fnStackCode[$i]=  htmlentities(  self::closure_dump( self::$fnStack[$i] )  , ENT_QUOTES | ENT_IGNORE,'UTF-8')."<hr>\r\n"; 
		}
		return array_merge($out,array('fnStack'=>$fnStackCode));
	}
	public static function listRoutes(){prea(self::getRoutes());}
	
	
	public static function closure_dump($c) {
		if($c instanceof Closure){
			$str = 'function (';
			$r = new ReflectionFunction($c);
			$params = array();
			foreach($r->getParameters() as $p) {
				$s = '';
				if($p->isArray()) {
					$s .= 'array ';
				} else if($p->getClass()) {
					$s .= $p->getClass()->name . ' ';
				}
				if($p->isPassedByReference()){
					$s .= '&';
				}
				$s .= '$' . $p->name;
				if($p->isOptional()) {
					$s .= ' = ' . var_export($p->getDefaultValue(), TRUE);
				}
				$params []= $s;
			}
			$str .= implode(', ', $params);
			$str .= '){' . PHP_EOL;
			$lines = file($r->getFileName());
			for($l = $r->getStartLine(); $l < $r->getEndLine(); $l++) {
				$str .= $lines[$l];
			}
			return $str;
		}
		else{
			ob_start();
			var_dump($c);
			$str=ob_get_contents();
			ob_end_clean();
			return $str;
		}

	}
	
    /**
     * Store a middleware middleware route and a handling function to be executed when accessed using one of the specified methods.
     *
     * @param string          $methods Allowed methods, | delimited
     * @param string          $pattern A route pattern such as /about/system
     * @param object|callable $fn      The handling function to be executed
     */
    public static function middleware($methods, $pattern, $fn, array $routeOptiones=[])
    {
        self::addRouteMethod('Middleware',$methods, $pattern, $fn, $routeOptiones);
    }

	
    /**
     * Store a route and a handling function to be executed when accessed using one of the specified methods.
     *
     * @param string          $methods Allowed methods, | delimited
     * @param string          $pattern A route pattern such as /about/system
     * @param object|callable $fn      The handling function to be executed
     */
    public static function match($methods, $pattern, $fn, array $routeOptiones=[])
    {
         self::addRouteMethod('Code',$methods, $pattern, $fn, $routeOptiones);
    }

	    public static function code($methods, $pattern, $fn, array $routeOptiones=[])
    {
         self::addRouteMethod('Code',$methods, $pattern, $fn, $routeOptiones);
    }
	
    public static function after($methods, $pattern, $fn, array $routeOptiones=[])
    {
         self::addRouteMethod('After',$methods, $pattern, $fn, $routeOptiones);
    }
	

	
	protected static function addRouteMethod($sType, $methods, $pattern, $fn, array $routeOptiones=[]){
		if(!$sType){
            $sType = 'Code';
        }
        if(is_array($methods)){$methods=implode('|',$methods);}
		
		$pattern = self::$baseRoute . '/' . trim($pattern, '/');
        $pattern = self::$baseRoute ? rtrim($pattern, '/') : $pattern;
		
		if(self::$staticMouleRouteEmulation['on']  ){
			if(self::patternMarch( $pattern , self::getCurrentRoute() )['positiveMatch']){
				self::$staticMouleRouteEmulation['count'][$sType]++;
				$args=func_get_args();
				if(!isset($args[4]['trace'])){
					$min_trace=array();
					$tracel=debug_backtrace();
					foreach($tracel as $it=>$trace){
						if(!$it){continue;}
						if($trace['function']=='call_user_func_array'){break;}
						$min_trace[]=(isset($trace['file'])?($trace['function'].'() line '.$trace['line'].' '.$trace['file']):$trace['class'].'@'.$trace['function']);
						if($it>2){break;}
					}
					$args[4]['trace']=implode("\r\n                                       ",$min_trace);//optimise for print_r
				}

				self::$staticMouleRouteEmulation['cache'][$sType][]=$args;
			}
		return;
		}
		
		
		self::$fnStack[]=$fn;
		$fnStack_I=count(self::$fnStack)-1;
		
        foreach (explode('|', $methods) as $method) {
			$method=strtoupper(trim($method)); //force upper case
			if(!$method){continue;}
			$routeOptiones=array_filter($routeOptiones,function($v){return $v?true:false;}); //optimise for print_r
			$routeOptiones=array_map(function($v){return is_array($v)? implode("; ",$v):$v;},$routeOptiones); //optimise for print_r
			$route=[
                'pattern' => $pattern,
            //    'fn' => $fn,				//store n times the callback function
                'fnStack_I' => $fnStack_I,	//store only once the callback function
            ]+$routeOptiones;
			//if(is_string($fn)){$route['fnCM']=$fn;}
			
			if($sType==='Middleware'){self::$middlewareRoutes[$method][] = $route;}
			elseif($sType==='After'){self::$afterRoutes[$method][] = $route;}
			else{self::$codeRoutes[$method][] = $route;}
        }
	}
	
	
	public static function redirect($from,$to,$code=302,$strip_params=false,$build_query=array()){
		if($from==$to){return false;}
		//self::match(self::$allMethods, $pattern, $fn);
		self::middleware(self::$allMethods, $from, function() use ($to,$code,$strip_params,$build_query){
			if(strtolower(substr($to,0,7))=='http://' || strtolower(substr($to,0,8)=='https://')){$to_link=$to;} //    http redirect
			elseif(substr($to,0,2)=='//'){$to_link=$to;} //  //same protocol full link
			else{$to_link=rtrim(self::getBasePath(),'/').'/'.ltrim($to,'/');}
			
			self::redirect300($code,$to_link,$strip_params,$build_query);
		});
	}
	public static function permanentRedirect($from,$to){
		if($from==$to){return false;}
		self::redirect($from,$to,301);
	}
	
	
	
    /**
     * Shorthand for a route accessed using any method.
     *
     * @param string          $pattern A route pattern such as /about/system
     * @param object|callable $fn      The handling function to be executed
     */
    public static function all($pattern, $fn)
    {
        self(self::$allMethods, $pattern, $fn);
    }
	public static function any($pattern, $fn){ //alias for self::all
		self(self::$allMethods, $pattern, $fn);
	}
    /**
     * Shorthand for a route accessed using GET.
     *
     * @param string          $pattern A route pattern such as /about/system
     * @param object|callable $fn      The handling function to be executed
     */
    public static function get($pattern, $fn)
    {
        self::match('GET', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using POST.
     *
     * @param string          $pattern A route pattern such as /about/system
     * @param object|callable $fn      The handling function to be executed
     */
    public static function post($pattern, $fn)
    {
        self::match('POST', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using PATCH.
     *
     * @param string          $pattern A route pattern such as /about/system
     * @param object|callable $fn      The handling function to be executed
     */
    public static function patch($pattern, $fn)
    {
        self::match('PATCH', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using DELETE.
     *
     * @param string          $pattern A route pattern such as /about/system
     * @param object|callable $fn      The handling function to be executed
     */
    public static function delete($pattern, $fn)
    {
        self::match('DELETE', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using PUT.
     *
     * @param string          $pattern A route pattern such as /about/system
     * @param object|callable $fn      The handling function to be executed
     */
    public static function put($pattern, $fn)
    {
        self::match('PUT', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using OPTIONS.
     *
     * @param string          $pattern A route pattern such as /about/system
     * @param object|callable $fn      The handling function to be executed
     */
    public static function options($pattern, $fn)
    {
        self::match('OPTIONS', $pattern, $fn);
    }

    /**
     * Mounts a collection of callbacks onto a base route.
     *
     * @param string   $baseRoute The route sub pattern to mount the callbacks on
     * @param callable $fn        The callback method
     */
    public static function mount($baseRoute, $fn)
    {
        // Track current base route
        $curBaseRoute = self::$baseRoute;

        // Build new base route string
        self::$baseRoute .= $baseRoute;

        // Call the callable
        call_user_func($fn);

        // Restore original base route
        self::$baseRoute = $curBaseRoute;
    }



    /**
     * Set a Default Lookup Namespace for Callable methods.
     *
     * @param string $namespace A given namespace
     */
    public static function setNamespace($namespace)
    {
        if (is_string($namespace)) {
            self::$namespace = $namespace;
        }
    }

    /**
     * Get the given Namespace middleware.
     *
     * @return string The given Namespace if exists
     */
    public static function getNamespace()
    {
        return self::$namespace;
    }
	
	
	
	public static function runApp($route=null,$setRequestMethod=null,$callback=null){
		//2do: to improve the CLI console script call because the request method can be set twice from the InitRequest() and by parameter. This muste be tested!!!
		//2do: to improve the CLI console script call because the request method can be set twice from the InitRequest() and by parameter. This muste be tested!!!
		//2do: to improve the CLI console script call because the request method can be set twice from the InitRequest() and by parameter. This muste be tested!!!
		//2do: to improve the CLI console script call because the request method can be set twice from the InitRequest() and by parameter. This muste be tested!!!
		//2do: to improve the CLI console script call because the request method can be set twice from the InitRequest() and by parameter. This muste be tested!!!
		//2do: to improve the CLI console script call because the request method can be set twice from the InitRequest() and by parameter. This muste be tested!!!
		//	echo '<pre>'.print_r(thfRouter::getRequestConfig(),true).'</pre>';

		
		
		if(!is_array(self::$http) || count(self::$http)<9){ //auto init if request info is missing
			self::InitRequest();
		}
		return self::dispatch($callback , $route, $setRequestMethod );
	}
		
	
	
    /**
     * Execute the router: Loop all defined middleware middleware's and routes, and execute the handling function if a match was found.
     *
     * @param object|callable $callback Function to be executed after a matching route was handled (= after router middleware)
     *
     * @return bool
     */
    public static function dispatch($callback = null, $setRequestRouteURI=null, $setRequestMethod=null )
    {
		
		self::getCurrentRoute(); //call self::initRequest() if there is the case
		
		$runMethod=self::setRequestMethod($setRequestMethod); //if blank it will load the $http['requestMethod'], and if this is also blank it will autoDetect.
		$runMethod=self::correctRequestMethod(); //correction for HEAD to GET and POST to X-HTTP-Method-Override into ['PUT', 'DELETE', 'PATCH']
		if(!$runMethod){http_response_code(405);self::h405('HTTP/1.1 405 Method Not Allowed. No request method validated.',self::$haltOn405);}
		
		if($setRequestRouteURI){
			$runRoute=self::setRequestRouteURI($setRequestRouteURI);
			self::initRequest();
		}
		else{
			$runRoute=self::getRequestRouteURI();
		}
		//$runRoute;		$runMethod;
		//$runRoute;		$runMethod;
		//$runRoute;		$runMethod;
		//$runRoute;		$runMethod;
		//$runRoute;		$runMethod;
		//$runRoute;		$runMethod;
		//$runRoute;		$runMethod;
		//$runRoute;		$runMethod;
		//$runRoute;		$runMethod;
		//$runRoute;		$runMethod;
		//$runRoute;		$runMethod;
		//$runRoute;		$runMethod;
		//$runRoute;		$runMethod;
		//$runRoute;		$runMethod;
		//$runRoute;		$runMethod;
		//$runRoute;?????		$runMethod;

		
		$totalFoundToHandle=self::storyLine();
		
        // Handle all middleware middlewares
        if (isset(self::$middlewareRoutes[$runMethod])) {
            self::handle(self::$middlewareRoutes[$runMethod],'Middleware');
        }

        // Handle all routes
        $numHandled = 0;
        if (isset(self::$codeRoutes[$runMethod])) {
            $numHandled = self::handle(self::$codeRoutes[$runMethod], 'Code');
			//echo "<h1>RHHH:$numHandled</h1>"; 	prea(self::$codeRoutes[$runMethod]);
        }
		elseif($runMethod!=='GET'){
			http_response_code(405);
			self::h405('HTTP/1.1 405 Method Not Allowed',self::$haltOn405); 
			}
        // If no route was handled, trigger the 404 (if any)
        if ($numHandled === 0) {
            if (self::$notFound404Callback) {
                self::invokeRoute(self::$notFound404Callback);
            } else {
				http_response_code(404);
                //header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found',true,404);
		        echo '<h1>HTTP status 404</h1><h3>Not Found</h3>';

            }
        } // If a route was handled, perform the finish callback (if any)
        else {
			
			// Handle all after route comands
			if (isset(self::$afterRoutes[$runMethod])) {
				self::handle(self::$afterRoutes[$runMethod],'After');
			}
			
            if ($callback && is_callable($callback)) {
                $callback();
            }
        }
		
		self::correctRequestMethod();//second run ob_end_clean() for head

        // Return true if a route was handled, false otherwise
        return $numHandled !== 0;
    }

    /**
     * Set the 404 handling function.
     *
     * @param object|callable $fn The function to be executed
     */
    public static function set404($fn)
    {
        self::$notFound404Callback = $fn;
    }

    /**
     * Handle a a set of routes: if a match is found, execute the relating handling function.
     *
     * @param array $routes       Collection of route patterns and their handling functions
     * @param bool  $quitAfterRun Does the handle function need to quit after one route was matched?
     *
     * @return int The number of routes handled
     */
	
/*	private static function readModulePatterns(){
		if(!self::hasSubdirs($moduleBaseDir)){return array();}
		self::getAllSubdirs();
		foreach($moduleDirs as $moduleBaseDir){
			
			//getAllSubdirs
		}
	}*/
	
    private static function handle($routes, $type)
    {
		//$maxRoutes = self::getMaxRoutes($type);
		
        // Counter to keep track of the number of routes we've handled
        $numHandled = 0;

        // The current page URL
        $url = self::getCurrentRoute();

        // Loop all routes
        foreach ($routes as $route) {
			$matchResult=self::patternMarch($route['pattern'],$url);		//prea($matchResult);
            // we have a match!
            if ($matchResult['positiveMatch']) {
               $maxRoutes=self::loopMaxRoutes($type); //decrease with one $maxRoutes--;
				if(self::invokeRoute($route, $matchResult['params'])){
					++$numHandled;
				}
               // self::invoke($route, $matchResult['params']); ++$numHandled; //original code

				

                // $maxRoutes of type has been reached
                if (!$maxRoutes) {
                    break;
                }
            }
        }

        // Return the number of routes handled
        return $numHandled;
    }
	
	
	
	
	
	public static function registerGlobalRoutes(
												$controllerTypes=array('Middleware'), //$controllerTypes=array('Middleware','Code','After') or $controllerTypes='Middleware'
												$requestMethods=array('GET'), // $requestMethods=array('GET','POST'), or $requestMethods='GET|POST' or $requestMethods=array('GET|POST')
												$patterns=array('/.*'), //one or more regex strting with delimiter '/'  full match  '/.*'  https://regex101.com/
												$modules=array(), //array('Default','Blog'), module directory name
												$shortClasses=array(), //array('List'), short class name, not composed like Blog_List_ControllerCode  : /blog/list
												$methods=array(), //array('index') meaning:  indexGET OR  indexAction   /blog/list/indexXXX
												Closure $fn //clousure function(){} or string like class@method
											   ){
		///!!!!!! pun filtru direct din partea de add in $routeOptiones : addRouteMethod($type='Code',$methods, $pattern, $fn, array $routeOptiones=[]){
		///!!!!!!!!!!! pun inca un filtru de validare in addRouteMethod($type='Code',$methods, $pattern, $fn, array $routeOptiones=[]) sau in handle() sau invoke() sa filtreze functile care au bind la $config:
		$validate_config=$validate_errors=array();
		
		if(!is_array($controllerTypes)){$controllerTypes=array($controllerTypes);}//force to array
		foreach($controllerTypes as $controllerType){
			if(!isset(self::$maxRoutes[$controllerType]) || !self::$maxRoutes[$controllerType]){
				@$validate_errors['controllerTypes'].='invalid $controllerType: "'.$controllerType.'" or $maxRoutes[$controllerType] = 0; ';
			}
			else{
				$validate_config['controllerTypes'][]=$controllerType;
			}
		}
		if(isset($validate_config['controllerTypes'])){
			$controllerTypes=$validate_config['controllerTypes'];
		}
		else{//full error mode
			@$validate_errors['controllerTypes'].=' NO $validate_config[controllerTypes] FOUND! ';
			return $validate_errors;
		} 
		
		
		if($requestMethods && !is_array($requestMethods)){$requestMethods=explode('|',$requestMethods);}//force to array
		if(!$requestMethods || !$requestMethods[0]){$requestMethods=self::$http['allowedRequestMethods'];}
		
		if(in_array(self::$http['requestMethod'],$requestMethods)){
			$validate_config['requestMethods']=$requestMethods;
			$requestMethod=self::$http['requestMethod']; //this is the right method so add only one to the routing table
		}
		else{
			@$validate_errors['requestMethods'].='this request methods "'.implode('|',$requestMethods).'" do not apply to current request method="" ; ';
			return $validate_errors;
		}
		
		
		if(!is_array($patterns)){$patterns=array($patterns);}//force to array
		if(!$patterns[0]){$patterns[0]='/.*';}//run everywhere if null is provided
		$url=self::getCurrentRoute();
		foreach($patterns as $pattern){
			if(self::patternMarch( $pattern , $url )['positiveMatch']){
				$validate_config['patterns'][]=$pattern;
			}
			else{
				@$validate_errors['patterns'].='matching pattern: '.$pattern." is failed; ";
			}
		}
		if(!isset($validate_config['patterns'][0])){
			return $validate_errors; //no patern was validated so pointless to add here
		}
		else{$pattern=$validate_config['patterns'][0];}
		
		
		
		if(!is_array($modules) && $modules){$modules=array($modules);}//force to array
		if(!is_array($shortClasses) && $shortClasses){$shortClasses=array($shortClasses);}//force to array
		if(!is_array($methods) && $methods){$methods=array($methods);}//force to array
		
		
		
		foreach($controllerTypes as $controllerType){
			$tmp=isset(self::$http['requestModule']['Code']['modul'])? self::$http['requestModule']['Code']:null; //Code is always present as a example
			
			if($modules && (!$tmp || !in_array($tmp['modul'],$modules))){break;} //module filtration is on
			if($shortClasses && (!$tmp || !in_array($tmp['cl'],$shortClasses))){break;} //module filtration is on
			if($methods && (!$tmp || !in_array(strtolower($tmp['method']),array_map(function($v){return strtolower($v);},$methods)))){break;} //module filtration is on

			$validate_config['modules']=$modules;
			$validate_config['shortClasses']=$shortClasses;
			$validate_config['methods']=$methods;
			
			self::addRouteMethod($controllerType,$requestMethod, $pattern , $fn, $validate_config); //echo "addRouteMethod($controllerType,$requestMethod, $pattern , fn, $validate_config);";
		}
		//prea($validate_errors);
		return $validate_errors;
	}
	
	private static function storyLine(){
		/*
		modifyers / parameters:
		|B break after loop if a code root was previously found;    [STOP]
		|C continue before running the loop if a code root was previously found; [SKIP]
		|G run in all cases to register the all route types even if 404 will be generated because of no route validation; useful for global procedures
		|M count middleware routes as code routes
		|A count after routes as code routes
		|X skip and purge code routes
		|Y skip and purge middleware routes
		|Z skip and purge after routes
		|  no effect
		*/
		/*
		self::$routePriorityOrder=array(
			'Global_RouteStatic|GX',	//this should be first and it should not contain any code routes!
			'Module_RouteStatic|B',	//each module can run static routes if module, if no Module_RouteStatic routes are found, then run default-static routes
			'class@method|B', 	//class and methodAction call
		); */
			

		$url = self::getCurrentRoute();
		$ns=self::getNamespace();
		if($ns){$ns.='\\';}
		
		$matchRoutesFound=0;
		$break=false;
		
		foreach(self::$routePriorityOrder as $order=>$routePriorityX){
			$routePriority=explode('|',$routePriorityX)[0];
			$modifyers=explode('|',$routePriorityX)[1];
						
			if(substr_count($modifyers,'G')){} //run in all cases to register the all route types even if 404 will be generated because of no route validation; usfull for global procedures
			elseif($matchRoutesFound && $break){continue;} //break after loop if a Code root was previously found;    [STOP] but keep on searching for $modifyers = G 
			elseif($matchRoutesFound && substr_count($modifyers,'C')){continue;} //continue before running the loop if a Code root was previously found; [SKIP]
						
			
			if($routePriority=='Global_RouteStatic'){
				foreach(self::$http['requestGlobalRouteStaticFrom'] as $availableModule){
					if(self::isModuleRouteGlobal($availableModule)){
						self::startStaticMouleRouteEmulation();
						self::invokeRoute($ns.$availableModule.'_Global_RouteStatic@addStaticRoutes');
						//die('de adaugat conditii si filtre globale aici!!');
						if(substr_count($modifyers,'X')){ //skip and purge Code routes
							self::$staticMouleRouteEmulation['cache']['Code']=[];
							self::$staticMouleRouteEmulation['count']['Code']=0;
						}
						if(substr_count($modifyers,'Y')){ //skip and purge middleware routes
							self::$staticMouleRouteEmulation['cache']['Middleware']=[];
							self::$staticMouleRouteEmulation['count']['Middleware']=0;
						}
						if(substr_count($modifyers,'Z')){ //skip and purge After routes
							self::$staticMouleRouteEmulation['cache']['After']=[];
							self::$staticMouleRouteEmulation['count']['After']=0;
						}
						if(
							self::$staticMouleRouteEmulation['count']['Code'] || 
							self::$staticMouleRouteEmulation['count']['Middleware'] && substr_count($modifyers,'M') || //count middleware routes as Code routes
							self::$staticMouleRouteEmulation['count']['After'] && substr_count($modifyers,'A') //count after routes as Code routes
						  ){//at least a Code route was found
							$matchRoutesFound++;
						}
						//$cache=self::$staticMouleRouteEmulation['cache']; //=array('Code'=>[],'Middleware'=>[],'After'=>[]); //keep cache but why?
						
						self::commitStaticMouleRouteEmulation(); //run self::addRouteMethod
						//self::cleanStaticMouleRouteEmulation();

					}
					//prea(self::getModuleFormatting($availableModule,'Global','Route','Static')); //debug
				}
				
				
				//load all global routes
			}
			if($routePriority=='Module_RouteStatic'){
				//register all module static routes
				foreach(self::$http['requestModuleRouteStaticFrom'] as $availableModule){
					if(self::isModuleRouteStatic($availableModule)){
						self::startStaticMouleRouteEmulation();
						self::invokeRoute($ns.$availableModule.'_Module_RouteStatic@addStaticRoutes');
						if(substr_count($modifyers,'X')){ //skip and purge Code routes
							self::$staticMouleRouteEmulation['cache']['Code']=[];
							self::$staticMouleRouteEmulation['count']['Code']=0;
						}
						if(substr_count($modifyers,'Y')){ //skip and purge Middleware routes
							self::$staticMouleRouteEmulation['cache']['Middleware']=[];
							self::$staticMouleRouteEmulation['count']['Middleware']=0;
						}
						if(substr_count($modifyers,'Z')){ //skip and purge After routes
							self::$staticMouleRouteEmulation['cache']['After']=[];
							self::$staticMouleRouteEmulation['count']['After']=0;
						}
						if(
							self::$staticMouleRouteEmulation['count']['Code'] || 
							self::$staticMouleRouteEmulation['count']['Middleware'] && substr_count($modifyers,'M') || //count middleware routes as Code routes
							self::$staticMouleRouteEmulation['count']['After'] && substr_count($modifyers,'A') //count after routes as Code routes
						  ){//at least a Code route was found
							$matchRoutesFound++;
						}
						//$cache=self::$staticMouleRouteEmulation['cache']; //=array('Code'=>[],'Middleware'=>[],'After'=>[]); //keep cache but why?
						
						self::commitStaticMouleRouteEmulation(); //run self::addRouteMethod
						//self::cleanStaticMouleRouteEmulation();

					}
					//prea(self::getModuleFormatting($availableModule,'Module','Route','Static'); //debug

				}
				
			}
			if($routePriority=='class@method'){ //invoke className@method
				if(self::$http['requestModule']){
					foreach(self::$http['requestModule'] as $controllerType=>$route){
						if(
							$controllerType=='Code' && !substr_count($modifyers,'X') ||  //skip and purge Code routes
							$controllerType=='Middleware'  && !substr_count($modifyers,'Y') || //skip and purge Middleware routes
							$controllerType=='After' && !substr_count($modifyers,'Z') //skip and purge After routes
						){}   
						else{continue;}
						if($route['invoke']){
							if(
								$controllerType=='Code' ||
								$controllerType=='Middleware' && substr_count($modifyers,'M') || //count middleware routes as Code routes
								$controllerType=='After' && substr_count($modifyers,'A') //count after routes as Code routes
							){$matchRoutesFound++;}
							self::addRouteMethod($controllerType,$route['requestMethod'], '/.*', $route['invoke'], $route['params']); 
						}
					}
				}
			}
			
		if($matchRoutesFound && substr_count($modifyers,'B')){$break=true;} //break after loop if a Code root was previously found;    [STOP]
		}
	return $matchRoutesFound;
	}

    /**
     * @param $fn
     * @param array $params
     * @return bool|mixed
     */
    private static function invokeRoute($fn, array $params = [])
    {
		$route=null;
		//route array with parametrization options
		
		if(is_array($fn) && isset($fn['fnStack_I']) ){
			$route=$fn;
			$fn=self::$fnStack[ $fn['fnStack_I'] ];
		}elseif(is_array($fn) && isset($fn['fn']) ){
			$route=$fn;
			$fn=$fn['fn'];
		}
		if($route && !empty($route['redirect'])){
            die('redirect  neimplementat in router');
        }

		return self::invokeMethod($fn, $params, self::getNamespace(), true);

    }

    /*
   /**
     * @param mixed $fn 'ns\class@method'|closure|function
     * @param array $params
     * @param string $namespace
     * @param bool $bForceBoolReturn
     * @return bool|mixed
     */
/*
    protected static function invokeMethod($fn, array $params = [], string $namespace = '', bool $bForceBoolReturn = true)
    {
        if (is_callable($fn)) {
            $r = call_user_func_array($fn, $params); // Returns the return value of the callback, or FALSE on error.
            //var_dump($r);
            if ($bForceBoolReturn && !$r && $r !== false) {
                $r = true; //fix no blank return for functions to avoid 404
            }
            return $r;
        } // If not, check the existence of special parameters
        elseif (is_string($fn) && stripos($fn, '@') !== false) {
            // Explode segments of given route
            list($controller, $method) = explode('@', $fn);
            // Adjust controller class if namespace has been set
            if ($namespace !== '' && stripos($fn, '\\') === false) {
                $controller = $namespace . '\\' . $controller;
            }
            // Check if class exists, if not just ignore and check if the class exists on the default namespace
            if (class_exists($controller)) {
                // First check if is a static method, directly trying to invoke it.
                // If isn't a valid static method, we will try as a normal method invocation.
                $call_user_func_array = call_user_func_array([new $controller(), $method], $params);
                if ($call_user_func_array === false) {
                    // Try to call the method as an non-static method. (the if does nothing, only avoids the notice)
                    $forward_static_call_array = forward_static_call_array([$controller, $method], $params);
                    if ($forward_static_call_array === false) {
                        return false; //class exists but method not found
                    } else {
                        return $bForceBoolReturn ? true : $forward_static_call_array;
                    }//static method was called
                } else {
                    return $bForceBoolReturn ? true : $call_user_func_array;
                }//method was called
            } else {
                return false;
            }//class does not exist
        }
        return false;//general exception
    }

*/
}
