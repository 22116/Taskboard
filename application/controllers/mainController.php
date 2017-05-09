<?php

class mainController extends Controller
{
	public function actionIndex()
	{
		$this->view->generate('index', []);
	}

	public function actionTest()
	{
		$this->view->generate('test');
	}
}