<?php

class notfoundController extends Controller
{
	public function actionIndex()
	{
		$this->view->setTemplate('404');
		$this->view->generate('index');
	}
}