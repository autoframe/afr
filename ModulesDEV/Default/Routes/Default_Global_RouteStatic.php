<?php

class Default_Global_RouteStatic{
	
	public static function addStaticRoutes(){
		
		
		$rx=thfRouter::registerGlobalRoutes( 
												$controllerTypes=array('Middleware'), //$controllerTypes=array('Middleware','Code','After') or $controllerTypes='Middleware'
												$requestMethods=array('GET'), // $requestMethods=array('GET','POST'), or $requestMethods='GET|POST' or $requestMethods=array('GET|POST')
												$patterns=array('/.*'), //one or more regex strting with delimiter '/'  full match  '/.*'  https://regex101.com/
												$modules=array(), //array('Default','Blog'), module directory name
												$shortClasses=array(), //array('List'), short class name, not composed like Blog_List_ControllerCode  : /blog/list
												$methods=array(), //array('index') meaning:  indexGET OR  indexAction   /blog/list/indexXXX
												function () {
			@$GLOBALS['jjj']++;
				header('Thfg'.$GLOBALS['jjj'].': Default_Global_RouteStatic MIDDLEWARE '.$GLOBALS['jjj']);
			
			});
		
	
		
	}
	
	
	
	
}

