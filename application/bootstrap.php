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