<?php

class daysideController extends Controller
{
	public function actionIndex()
	{
		header('location: ' . APP_PATH . 'dayside');
	}
}