<?php
set_include_path(APP_PATH);

spl_autoload_register(function ($className)
{
	includeIfExist('application/core/configuration/', $className);
	includeIfExist('application/core/', $className);
	includeIfExist('application/views/widgets/', $className);
	includeIfExist('application/controllers/', $className);
	includeIfExist('application/models/', $className);
});

function includeIfExist($dir, $className)
{
	if(file_exists($dir . $className . '.php'))
	{
		include_once $dir . $className . '.php';
	}
}

$dbData = parse_ini_file('application/core/configuration/db.ini');
try
{
	App::$db = new PDO('mysql:host=' . $dbData['host'] . ';dbname=' . $dbData['db'] . ';charset=' . $dbData['charset'],
		$dbData['user'], $dbData['password'], [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES => false,
		]);
}
catch (Exception $exception)
{
	echo 'Can\'t connect to database: ' . $exception->getMessage();
	die();
}
