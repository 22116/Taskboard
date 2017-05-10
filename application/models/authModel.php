<?php

class authModel extends Model
{
	public static $user;
	public static $isAuth = false;

	public static function logout()
	{
		session_start();
		unset($_SESSION['id']);
		unset($_SESSION['hash']);
		header('location: /');
	}
	public static function login($name, $password)
	{
		session_start();
		$user = usersModel::findByLogin($name);
		if(md5($password) == $user->getPassword())
		{
			$hash = md5(rand());
			$_SESSION['id'] = $user->getId();
			$_SESSION['hash'] = $hash;
			$user->setHash($hash);
			$user->save();
			self::$user = $user;
		}
		header('location: /');
	}
	public static function checkCurrentUser()
	{
		session_start();
		if(isset($_SESSION['id']) && isset($_SESSION['hash']))
		{
			self::$user = usersModel::findById($_SESSION['id']);
			self::$isAuth = trim(self::$user->getHash()) == trim($_SESSION['hash']) ? true : false;
		}
		else self::$isAuth = false;
	}
}