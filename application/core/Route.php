<?php

class Route
{
	static public function start()
	{
		$path = preg_replace('/\?.*/','', $_SERVER['REQUEST_URI']);
		$parts = preg_split('/[\/?&\*#]/', $path);

		$controllerString = isset($parts[1]) && !empty($parts[1])? trim(strtolower($parts[1])) : App::getParam('defaultController');
		$actionString = isset($parts[2]) && !empty($parts[2]) ? trim(strtolower($parts[2])) : App::getParam('defaultAction');

		if(file_exists('application/controllers/' . $controllerString . 'Controller.php'))
		{
			$controllerString = $controllerString . 'Controller';
			$controller = new $controllerString;
		}
		else
		{
			self::routeToNotFound();
			return;
		}

		$action = 'action' . $actionString;
		if(method_exists($controller, 'action' . $actionString))
		{
			$controller->behaviorBefore();
			$controller->$action();
			$controller->behaviorAfter();
		}
		else
		{
			self::routeToNotFound();
			return;
		}
	}

	static public function routeToNotFound()
	{
		header( $_SERVER['SERVER_PROTOCOL']." 404 Not Found", true );
		(new notfoundController())->actionIndex();
	}
}