<?php

namespace Harmony2\Router;

class Route
{
	private $pattern;
	private $controller;
	private $action;
	private $httpMethod;

	public function __construct($pattern, $controller, $action, $httpMethod)
	{
		$this->pattern = (string)$pattern;
		$this->controller = (string)$controller;
		$this->action = (string)$action;
		$this->httpMethod = (string)$httpMethod;
	}

	public function getPattern()
	{
		return $this->pattern;
	}

	public function getController()
	{
		return $this->controller;

	}

	public function getAction()
	{
		return $this->action;
	}

	public function getHttpMethod()
	{
		return $this->httpMethod;
	}
}
