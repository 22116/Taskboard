<?php

class Controller
{
	protected $view;
	protected $model;

	public function __construct()
	{
		$this->view = new View();
		$this->view->setPath('application/views/' . substr(get_class($this), 0, -10) . '/');
		$this->model = new Model();
	}

	public function behaviorBefore() { }
	public function behaviorAfter() { }

	public function actionIndex() { }
}