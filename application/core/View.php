<?php

class View
{
	private $template;
	private $path;
	private $content;
	private $data;

	public function __construct()
	{
		$this->template = App::getParam('template');
	}

	public function generate($content, $data = null)
	{
		$this->data = $data;
		if(is_array($data))
		{
			extract($data);
		}

		$this->content = $content;
		include_once 'application/views/template/' . $this->template . '.php';
	}

	public function getTemplate() : string
	{
		return $this->template;
	}

	public function setTemplate($template)
	{
		$this->template = $template;
	}

	public function getPath() : string
	{
		return $this->path;
	}

	public function setPath($path)
	{
		$this->path = $path;
	}

	private function generateContent()
	{
		if(is_array($this->data))
		{
			extract($this->data);
		}

		include_once $this->path . $this->content . '.php';
	}
}