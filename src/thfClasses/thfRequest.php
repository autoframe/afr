<?php
class thfRequest{
	use thfHeader;
	use thfModuleTools;
	
	protected static $allMethods='GET|POST|PUT|DELETE|OPTIONS|PATCH|HEAD';
//	public static $allowedMethods='GET|POST|PUT|DELETE|OPTIONS|PATCH|HEAD';

	protected static $http=array(
		'allowedRequestMethods'=>array('GET','POST','HEAD','PUT','DELETE','OPTIONS','PATCH','CLI'),
		'basePath'=>false,
		);
	public static function getRequestConfig(){	return self::$http;	}

	protected static $internalStats=array();
	public static function getInternalStats(){	return self::$internalStats;	} //debug only

	private static $correctRequestMethodRun=0; //used for head method

	
	public static function initRequest($custom_prams=array()){

		if(is_array($custom_prams) && count($custom_prams)){
			foreach($custom_prams as $cpid=>$cp){
				if(isset(self::$http[$cpid])){
					if(is_array($cp)){//array smooth merge
						foreach($cp as $cpii=>$cpvv){
							self::$http[$cpid][$cpii]=$cpvv;
						}
					}
					else{self::$http[$cpid]=$cp;}
				}
			}
		}
		
		self::$http['autoDetectBasePath']=self::autoDetectBasePath();
		if(!self::$http['basePath']===false){
			self::setBasePath(self::$http['autoDetectBasePath']);
		}
		
		self::$http['autoDetectRequestMethod'] = self::autoDetectRequestMethod();	
		if(!isset(self::$http['requestMethod']) || !self::$http['requestMethod']){
			self::setRequestMethod(self::$http['autoDetectRequestMethod']);
		}
		
		self::$http['autoDetectRequestURI']=self::autoDetectRequestURI();
		if(!isset(self::$http['requestRouteURI']) || !self::$http['requestRouteURI']){
			self::setRequestRouteURI(self::$http['autoDetectRequestURI']);
		}
		
		self::buildRouteInfo();
		self::buildRouteModuleInfo();

		return self::$http;
	}

	private static function buildRouteInfo(){		
		self::$http['requestRouteInfo']=parse_url(self::getRequestRouteURI());
		self::$http['requestRouteInfo']['currentRoute'] ='/' .trim(self::$http['requestRouteInfo']['path'] ,'/');// Remove trailing slash + enforce a slash at the start	
		
		$query=array();
		if(isset(self::$http['requestRouteInfo']['query']) && self::$http['requestRouteInfo']['query']){//parse to array queryed string
			parse_str(self::$http['requestRouteInfo']['query'],$query);
		}
		self::$http['requestRouteInfo']['queryData'] = $query;
	}
	
	private static function buildRouteModuleInfo(){
		$requestModuleRoute = $requestDefaultRoute = null; //$requestGlobalRoute = $requestGlobalRouteMethod = null;
		$requestModuleRouteStaticFrom=[];
		
		if(!isset(self::$http['requestRouteInfo']['currentRoute'])){
			self::buildRouteInfo();
		}
		//echo '<hr>'.PHP_EOL;debug_print_backtrace(); echo '<hr>'.PHP_EOL;
		$parts=explode('/',self::$http['requestRouteInfo']['currentRoute']); 		

		if(count($parts)>1 && strlen($parts[0])<1){
			array_shift($parts); //extract first always blank route part
		}
		//prea($parts);
		$parts_unsafe=array_map('trim',$parts);
		$parts=self::sanitizeClassModuleString($parts);
		$partsCount=$parts;
		$requestMethod=self::getRequestMethod();
		
		for($i=0;$i<3;$i++){
			$parts[$i]=(!isset($parts[$i]) || strlen($parts[$i])===0? '':$parts[$i]);
		}
		
		//custom module routes
		if(count($partsCount)>=1 && strlen($parts[0])){
			$modul=$parts[0];
			$class=($parts[1]?$parts[1]:'Index');
			$method=($parts[1] && $parts[2]?$parts[2]:'index');
			$methodAction=$method.self::getRequestMethod();
			$methodAnyAction=$method.'Action';
			
			$tmp=array( 'modul'=>ucwords($modul),'cl'=>ucwords($class),	);
			$requestModuleRouteStaticFrom[]=$tmp['modul'];
			foreach(self::$controllerTypes as $ct){
				$tmp=array_merge($tmp,self::getModuleFormatting($tmp['modul'],$tmp['cl'],'Controller',$ct));
			}
			$requestModuleRoute=array_merge($tmp,array(
				'method'=>$method,
				'requestMethod'=>$requestMethod,
				'methodAction'=>$methodAction,
				'methodAnyAction'=>$methodAnyAction,
				'params'=>array_slice($parts_unsafe,3+($class==='Index'?-1:0)+($method==='index'?-1:0)),
				));
		}
		
		//deafault module routes
		if(count($partsCount)>=1){
			$modul='Default';
			if($parts[0]){	$class=$parts[0]; }
			else{			$class='Index'; }
			
			$method=($parts[1]?$parts[1]:'index');
			$methodAction=$method.self::getRequestMethod();
			$methodAnyAction=$method.'Action';
			
			$tmp=array( 'modul'=>ucwords($modul),'cl'=>ucwords($class),	);
			$requestModuleRouteStaticFrom[]=$tmp['modul'];
			foreach(self::$controllerTypes as $ct){
				$tmp=array_merge($tmp,self::getModuleFormatting($tmp['modul'],$tmp['cl'],'Controller',$ct));
			}
			$requestDefaultRoute=array_merge($tmp,array(
				'method'=>$method,
				'requestMethod'=>$requestMethod,
				'methodAction'=>$methodAction,
				'methodAnyAction'=>$methodAnyAction,
				'params'=>array_slice($parts_unsafe,2+($method==='index'?-1:0)),
				));

		}
	
		/*$method='global';
		$methodAction=$method.self::getRequestMethod();
		$methodAnyAction=$method.'Action';
		$modul=$class='Global';
		
		$tmp=array( 'modul'=>ucwords($modul),'cl'=>ucwords($class),	);
		foreach(self::$controllerTypes as $ct){
			$tmp=array_merge($tmp,self::getModuleFormatting($tmp['modul'],$tmp['cl'],'Controller',$ct));
		}
		$requestGlobalRoute=array_merge($tmp,array(
				'method'=>$method,
				'requestMethod'=>$requestMethod,
				'methodAction'=>$methodAction,
				'methodAnyAction'=>$methodAnyAction,
				'params'=>$parts_unsafe,
				));
		
		if($parts[0]){
			$method=$parts[0];
			$methodAction=$method.self::getRequestMethod();
			$methodAnyAction=$method.'Action';
			$modul=$class='Global';
			
			$tmp=array( 'modul'=>ucwords($modul),'cl'=>ucwords($class),	);
			foreach(self::$controllerTypes as $ct){
				$tmp=array_merge($tmp,self::getModuleFormatting($tmp['modul'],$tmp['cl'],'Controller',$ct));
			}
			
			$requestGlobalRouteMethod=array_merge($tmp,array(
					'method'=>$method,
					'requestMethod'=>$requestMethod,
					'methodAction'=>$methodAction,
					'methodAnyAction'=>$methodAnyAction,
					'params'=>array_slice($parts_unsafe,1),
					));
			
		}*/
		
		//logical order to execute
		$requestModuleAccept=array();
		$requestModuleAccept['Module']['ModuleRoute']=$requestModuleRoute;
		$requestModuleAccept['Module']['DefaultRoute']=$requestDefaultRoute;
		//$requestModuleAccept['Global']['GlobalRouteMethod']=$requestGlobalRouteMethod;
		//$requestModuleAccept['Global']['GlobalRoute']=$requestGlobalRoute;
		
		self::$internalStats['requestModuleAcceptXXXXX']='GLOBAL: MODUL | CLASA | METODA | REQUEST_METHOD | x3 middleware code after + rute';
		//self::$internalStats['requestModuleAcceptXdX']=$_SERVER;
		self::$internalStats['requestModuleAccept']=$requestModuleAccept;
		
		self::$http['requestModule']=self::validateRequestModules($requestModuleAccept);		
		self::$http['requestModuleRouteStaticFrom']=$requestModuleRouteStaticFrom;
		self::$http['requestGlobalRouteStaticFrom']=unserialize(MODULES);

		
	
	}

	
	
	
	
	
    public static function patternMarch($pattern, $url=null){
		if(!$url){$url = self::getCurrentRoute();  }
        $matches=$params=false; //init
		
		// Replace all curly braces matches {} into word patterns (like Laravel)
		$pattern = preg_replace('/\/{(.*?)}/', '/(.*?)', $pattern);
		
		$positiveMatch=preg_match_all('#^' . $pattern . '$#', $url, $matches, PREG_OFFSET_CAPTURE);
		
		if ($positiveMatch) {
			// Rework matches to only contain the matches, not the orig string
			$matches = array_slice($matches, 1);

			// Extract the matched URL parameters (and only the parameters)
			$params = array_map(function ($match, $index) use ($matches) {

				// We have a following parameter: take the substring from the current param position until the next one's position (thank you PREG_OFFSET_CAPTURE)
				if (isset($matches[$index + 1]) && isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
					return trim(substr($match[0][0], 0, $matches[$index + 1][0][1] - $match[0][1]), '/');
				} // We have no following parameters: return the whole lot

				return isset($match[0][0]) ? trim($match[0][0], '/') : null;
			}, $matches, array_keys($matches));
		}
        
			return array(
				'pattern'=>$pattern,
				'positiveMatch'=>$positiveMatch,
				'params'=>$params,
				//'matches'=>$matches,
			);
        
    }
	
	
	
	
	public static function autoDetectRequestMethod(){
		return strtoupper(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : php_sapi_name());
	}
    public static function getRequestMethod(){return self::$http['requestMethod'];   }

	
	public static function setRequestMethod($requestMethod){
		if(!$requestMethod){//missing so use autoDetect in it was not previously initialized
			$requestMethod= self::$http['requestMethod']?self::$http['requestMethod']: self::autoDetectRequestMethod();
		}
		$requestMethod=strtoupper($requestMethod);

		if(in_array($requestMethod,self::$http['allowedRequestMethods'])){
			self::$http['requestMethod'] = $requestMethod;
		}
		else{ self::$http['requestMethod'] = false; }
		
		return self::$http['requestMethod'];
	}
	
	public static function sanitizeClassModuleString( $class, $fallback = '' ) {
		if(is_array($class)){
			foreach($class as $i=>$v){
				$class[$i]=self::sanitizeClassModuleString($v);
			}
			return $class;
		}
		elseif(is_string($class) && $class){

			// Strip out any %-encoded octets.
			$sanitized = preg_replace( '|%[a-fA-F0-9][a-fA-F0-9]|', '', $class );

			
			//$sanitized = preg_replace( '/[^A-Za-z0-9_-]/', '', $sanitized ); // Limit to A-Z, a-z, 0-9, '_', '-'.
			$sanitized = preg_replace( '/[^A-Za-z0-9]/', '', $sanitized );     // Limit to A-Z, a-z, 0-9, 

			$sanitized=ltrim($sanitized,'0123456789');//strip leading numbers if any
			$sanitized=trim($sanitized); //strip spaces/blanks
			if(strlen($sanitized)>0){//force first letter to lower
				$sanitized=strtolower(substr($sanitized,0,1)).substr($sanitized,1);
			}

		}
		else{
			$sanitized=''; //invalid parameter type
		}
		
		if ( '' === $sanitized && $fallback ) {
			return self::sanitizeClassModuleString( $fallback );
		}
		return $sanitized;
	}
	
	
	public static function autoDetectRequestURI(){
		if(!$_SERVER['REQUEST_URI']){ // php_sapi_name()=='cli'
			$requestRouteURI='/@'.strtoupper(php_sapi_name()). (count($_SERVER['argv'])?'?'.http_build_query($_SERVER['argv']):'');
		}
		else{
			$requestRouteURI=substr(rawurldecode($_SERVER['REQUEST_URI']),strlen(self::getBasePath()));
		}
		return $requestRouteURI;
	}
    public static function getRequestRouteURI(){	return self::$http['requestRouteURI'];   }

	public static function setRequestRouteURI($requestRouteURI){
		$autoDetectRequestURI=self::autoDetectRequestURI();
		if(!$requestRouteURI){ $requestRouteURI=$autoDetectRequestURI; }
		self::$http['requestRouteURI'] = $requestRouteURI;
		return self::$http['requestRouteURI'];
	}
	
	


	
	
	public static function correctRequestMethod(){
		self::$correctRequestMethodRun++;//increment loop
		
		if(self::$correctRequestMethodRun===1){ //first run

			// If it's a HEAD request override it to being GET and prevent any output, as per HTTP Specification
			// @url http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.4
			if (self::autoDetectRequestMethod() == 'HEAD') {
				ob_start();
				return self::setRequestMethod('GET');
			}
			// If it's a POST request, check for a method override header
			elseif (self::autoDetectRequestMethod() == 'POST') {
				$headers = self::getRequestHeaders();
				if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], ['PUT', 'DELETE', 'PATCH'])) {
					return self::setRequestMethod($headers['X-HTTP-Method-Override']);
				}
			}
		}
		elseif(self::$correctRequestMethodRun===2){ //second run
	        // If it originally was a HEAD request, clean up after ourselves by emptying the output buffer
			if (self::autoDetectRequestMethod() == 'HEAD') {
				ob_end_clean();
			}
		}
		return self::getRequestMethod();
	}

    public static function getRequestHeaders(){
        $headers = [];

        // If getallheaders() is available, use that
        if (function_exists('getallheaders')) {
            $headers = getallheaders();

            // getallheaders() can return false if something went wrong
            if ($headers !== false) {
                return $headers;
            }
        }

        // Method getallheaders() not available or went wrong: manually extract 'm
        foreach ($_SERVER as $name => $value) {
            if ((substr($name, 0, 5) == 'HTTP_') || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH')) {
                $headers[str_replace([' ', 'Http'], ['-', 'HTTP'], ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;
    }


	

	public static function getQueryData(){
		if(!isset(self::$http['requestRouteInfo']['queryData'])){
			self::initRequest();
		}
		return self::$http['requestRouteInfo']['queryData'];
	}
	
    public static function getCurrentRoute(){
		if(!isset(self::$http['requestRouteInfo']['currentRoute'])){
			self::initRequest();
		}
		return self::$http['requestRouteInfo']['currentRoute'];
    }

	
	public static function autoDetectBasePath(){
		return implode('/', array_slice(explode('/', str_replace('\\','/',$_SERVER['SCRIPT_NAME']) ), 0, -1)) ;
	}
	
    public static function getBasePath(){
        // Check if server base path is defined, if not define it.
        if (self::$http['basePath'] === false) {
            self::$http['basePath'] = self::autoDetectBasePath();
        }
        return self::$http['basePath'];
    }

    public static function setBasePath($basePath){ //eg:   /subdir
		if(substr($basePath,-1)==='/'){$basePath=substr($basePath,0,-1);} //remove end /	eg:   /subdir
        return self::$http['basePath'] = $basePath;
    }
	
	
	
	
	
	
	
	

}


/*
AuthType none
Require all granted
<FilesMatch "\.(ttf|otf|eot|woff|woff2)$">
    <IfModule mod_headers.c>
        #SetEnvIf Origin "http(s)?://(www\.)?(autoframe.ro|cdn.anotherwebsite.com|blahblah.anotherwebsite.com)$" AccessControlAllowOrigin=$0
        #Header add Access-Control-Allow-Origin %{AccessControlAllowOrigin}e env=AccessControlAllowOrigin
		Header add Access-Control-Allow-Origin "*"
		Header add Access-Control-Allow-Headers "origin, x-requested-with, content-type"
		Header add Access-Control-Allow-Methods "GET"
		#Header add Access-Control-Allow-Methods "PUT, GET, POST, DELETE, OPTIONS"
    </IfModule>
</FilesMatch>


Options -Indexes

<IfModule mod_rewrite.c>
	RewriteEngine On
	#RewriteBase /  #nu este necesara daca este in subfolder. Este necesara numai daca este un sinhur htaccess il radacina superioara
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteCond $0#%{REQUEST_URI} ([^#]*)#(.*)\1$
	#RewriteRule ^.*$ %2index.php?requestRouteURI=/$0 [QSA,L]
	RewriteRule ^.*$ %2index.php [QSA,L]
</IfModule>
*/


/*
D:\xampp\php>php D:\xampp\htdocs\administration\quiz_control\index.php
cli<pre>Array
(
    [basePath] => D:\xampp\htdocs\administration\quiz_control\
    [requestRouteURI] =>
)
</pre><pre>Array
(
    [ALLUSERSPROFILE] => C:\ProgramData
    [APPDATA] => C:\Users\a.nistor\AppData\Roaming
    [CommonProgramFiles] => C:\Program Files (x86)\Common Files
    [CommonProgramFiles(x86)] => C:\Program Files (x86)\Common Files
    [CommonProgramW6432] => C:\Program Files\Common Files
    [COMPUTERNAME] => ADRIANA-BV
    [ComSpec] => C:\Windows\system32\cmd.exe
    [FP_NO_HOST_CHECK] => NO
    [HOMEDRIVE] => C:
    [HOMEPATH] => \Users\a.nistor
    [LOCALAPPDATA] => C:\Users\a.nistor\AppData\Local
    [LOGONSERVER] => \\ARES
    [MOZ_PLUGIN_PATH] => C:\PROGRAM FILES (X86)\FOXIT SOFTWARE\FOXIT READER\plug
ins\
    [NUMBER_OF_PROCESSORS] => 8
    [OS] => Windows_NT
    [Path] => C:\Program Files\nodejs;C:\sdk;C:\Program Files\Java\jdk1.8.0_181\
bin;C:\Program Files (x86)\Common Files\Oracle\Java\javapath;C:\ProgramData\Orac
le\Java\javapath;C:\Program Files\ImageMagick-6.9.1-Q16;C:\Windows\system32;C:\W
indows;C:\Windows\System32\Wbem;C:\Windows\System32\WindowsPowerShell\v1.0\;C:\P
rogram Files (x86)\AMD\ATI.ACE\Core-Static;C:\Program Files (x86)\MySQL\MySQL Fa
bric 1.5 & MySQL Utilities 1.5\;C:\Program Files (x86)\MySQL\MySQL Fabric 1.5 &
MySQL Utilities 1.5\Doctrine extensions for PHP\;C:\Program Files\nodejs\;C:\Pro
gram Files\Liquid Technologies\Liquid Studio 2019\XmlDataBinder17\Redist17\cpp\w
in32\bin;C:\Program Files\Liquid Technologies\Liquid Studio 2019\XmlDataBinder17
\Redist17\cpp\win64\bin;C:\Program Files (x86)\OpenVPN\bin;C:\Users\a.nistor\App
Data\Roaming\npm
    [PATHEXT] => .COM;.EXE;.BAT;.CMD;.VBS;.VBE;.JS;.JSE;.WSF;.WSH;.MSC
    [PROCESSOR_ARCHITECTURE] => x86
    [PROCESSOR_ARCHITEW6432] => AMD64
    [PROCESSOR_IDENTIFIER] => Intel64 Family 6 Model 30 Stepping 5, GenuineIntel

    [PROCESSOR_LEVEL] => 6
    [PROCESSOR_REVISION] => 1e05
    [ProgramData] => C:\ProgramData
    [ProgramFiles] => C:\Program Files (x86)
    [ProgramFiles(x86)] => C:\Program Files (x86)
    [ProgramW6432] => C:\Program Files
    [PROMPT] => $P$G
    [PSModulePath] => C:\Windows\system32\WindowsPowerShell\v1.0\Modules\
    [PUBLIC] => C:\Users\Public
    [SESSIONNAME] => Console
    [SystemDrive] => C:
    [SystemRoot] => C:\Windows
    [TEMP] => C:\Users\AAC61~1.NIS\AppData\Local\Temp
    [TMP] => C:\Users\AAC61~1.NIS\AppData\Local\Temp
    [USERDNSDOMAIN] => BPG.LOCAL
    [USERDOMAIN] => BPG
    [USERNAME] => a.nistor
    [USERPROFILE] => C:\Users\a.nistor
    [windir] => C:\Windows
    [PHP_SELF] => D:\xampp\htdocs\administration\quiz_control\index.php
    [SCRIPT_NAME] => D:\xampp\htdocs\administration\quiz_control\index.php
    [SCRIPT_FILENAME] => D:\xampp\htdocs\administration\quiz_control\index.php
    [PATH_TRANSLATED] => D:\xampp\htdocs\administration\quiz_control\index.php
    [DOCUMENT_ROOT] =>
    [REQUEST_TIME_FLOAT] => 1595414012.4698
    [REQUEST_TIME] => 1595414012
    [argv] => Array
        (
            [0] => D:\xampp\htdocs\administration\quiz_control\index.php
        )

    [argc] => 1
)
</pre><pre>Array
(
)
</pre>PHP Warning:  Missing argument 2 for BaseController::__construct(), called
 in D:\xampp\htdocs\administration\quiz_control\Controllers\Home.php on line 17
and defined in D:\xampp\htdocs\administration\quiz_control\Controllers\BaseContr
oller.php on line 18

Warning: Missing argument 2 for BaseController::__construct(), called in D:\xamp
p\htdocs\administration\quiz_control\Controllers\Home.php on line 17 and defined
 in D:\xampp\htdocs\administration\quiz_control\Controllers\BaseController.php o
n line 18
PHP Fatal error:  Call to undefined method home::indexHead() in D:\xampp\htdocs\
administration\quiz_control\Controllers\Home.php on line 18

Fatal error: Call to undefined method home::indexHead() in D:\xampp\htdocs\admin
istration\quiz_control\Controllers\Home.php on line 18
*/