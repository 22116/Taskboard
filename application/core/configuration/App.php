<?php

class App
{
	private static $params = [];
	public static $db;

	private function __construct() { }
	protected function __clone() { }

	public static function setParam(string $key, mixed $value)
	{
		self::$params[$key] = $value;
	}
	public static function setParams(array $params)
	{
		self::$params = array_merge(self::$params, $params);
	}
	public static function getParam(string $key)
	{
		if(!isset(self::$params[$key])) throw new Exception('Undefined config parameter.', 10);
		return self::$params[$key];
	}
}