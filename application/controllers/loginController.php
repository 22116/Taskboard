<?php

class loginController extends Controller
{
	public function actionIndex()
	{
		$this->actionIn();
	}

	public function actionIn()
	{
		$login = trim(strtolower($_REQUEST['login']));
		$password = trim($_REQUEST['password']);

		authModel::login($login, $password);
	}

	public function actionOut()
	{
		authModel::logout();
	}
}