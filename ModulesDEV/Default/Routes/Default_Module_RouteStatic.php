<?php

class Default_Module_RouteStatic {
		
		public static function addStaticRoutes(){

			//thfRouter::InitRequest();
			//thfRouter::setMaxRoutes(10,'Code');

			// Custom 404 Handler
			thfRouter::set404(function () {
				header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
				echo '404, route not found!';
			});

			
			// Before Router Middleware
		/*	thfRouter::after('GET', '/.*', function () {
				echo '$db-}disconnect;';
			});*/

			/*thfRouter::middleware('GET', '/.*', function () {
				header('ThfMiddlewareRoute: r3xx');
			});*/

			// Static route: / (homepage)
			thfRouter::get('/', function () {
				@$GLOBALS['jjj']++;
				echo '<h1>bramus/router #'.$GLOBALS['jjj'].'</h1><p>Try these routes:<p><ul><li>/hello/<em>name</em></li><li>/blog</li><li>/blog/<em>year</em></li><li>/blog/<em>year</em>/<em>month</em></li><li>/blog/<em>year</em>/<em>month</em>/<em>day</em></li><li>/movies</li><li>/movies/<em>id</em></li></ul>';
			});

			// Static route: /hello
			thfRouter::get('/hello', function () {
				echo '<h1>bramus/router</h1><p>Visit <code>/hello/<em>name</em></code> to get your Hello World mojo on!</p>';
			});

			// Dynamic route: /hello/name
			thfRouter::get('/hello/(\w+)', function ($name) {
				echo 'Hello ' . htmlentities($name);
			});

			// Dynamic route: /ohai/name/in/parts
			thfRouter::get('/ohai/(.*)', function ($url) {
				echo 'Ohai ' . htmlentities($url);
			});

			// Dynamic route with (successive) optional subpatterns: /blog(/year(/month(/day(/slug))))
			thfRouter::get('/blog(/\d{4}(/\d{2}(/\d{2}(/[a-z0-9_-]+)?)?)?)?', function ($year = null, $month = null, $day = null, $slug = null) {
				if (!$year) {
					echo 'Blog overview';

					return;
				}
				if (!$month) {
					echo 'Blog year overview (' . $year . ')';

					return;
				}
				if (!$day) {
					echo 'Blog month overview (' . $year . '-' . $month . ')';

					return;
				}
				if (!$slug) {
					echo 'Blog day overview (' . $year . '-' . $month . '-' . $day . ')';

					return;
				}
				echo 'Blogpost ' . htmlentities($slug) . ' detail (' . $year . '-' . $month . '-' . $day . ')';
			});

			thfRouter::get('/blog', function () {
				echo '<h1>Blog second level/router</h1>';
			});

			// Subrouting
			thfRouter::mount('/movies', function (){

				// will result in '/movies'
				thfRouter::get('/', function () {
					echo 'movies overview';
				});

				// will result in '/movies'
				thfRouter::post('/', function () {
					echo 'add movie';
				});

				// will result in '/movies/id'
				thfRouter::get('/(\d+)', function ($id) {
					echo 'movie id ' . htmlentities($id);
				});

				// will result in '/movies/id'
				thfRouter::put('/(\d+)', function ($id) {
					echo 'Update movie id ' . htmlentities($id);
				});
			});




			thfRouter::redirect('/old','/new');

			// Thunderbirds are go!
				//print_r(thfRouter::$beforeRoutes); 		print_r(thfRouter::$afterRoutes);	die();





			
		}
}



