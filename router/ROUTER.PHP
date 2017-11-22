<?php

namespace Harmony2\Router;

/**
 * Gestion des routes de l'API
 * -------
 */
abstract class Router
{

	const METHOD_POST = 'POST';
	const METHOD_GET = 'GET';

	/**
	 * Liste des routes qui ont été définies
	 *
	 * @var array
	 */
	private static $routes = [];

	private static $url = "";

	/**
	 * Permet d'enregistrer la correspondance entre une route et un contrôleur
	 *
	 * @param string $pattern Pattern à utiliser en tant que REGEXP sur l'URL
	 * @param string $controller La classe contrôleur à utiliser
	 * @param string $action La méthode du contrôleur à appeler
	 * @param string $method La méthode HTTP associée à la route
	 *
	 * @return void
	 */
	public static function register($pattern, $controller, $action, $method = 'GET')
	{
		self::$routes[] = new Route($pattern, $controller, $action, $method);
	}

	/**
	 * Retourne toutes les routes définies
	 *
	 * @return array
	 */
	public static function getRoutes()
	{
		return self::$routes;
	}

	/**
	 * @param string $pattern
	 * @return bool|Route
	 */
	public static function getRoute(string $pattern, string $method)
	{
		/** @var Route $route */
		foreach (self::$routes as $route) {
			if (($route->getPattern() == $pattern) && ($route->getHttpMethod() == $method))
				return $route;
		}
		return false;
	}

	/**
	 * @param $url
	 */
	public static function setUrl($url)
	{
		self::$url = $url;
	}

	/**
	 * @return string
	 */
	public static function getUrl()
	{
		return self::$url;
	}

	/**
	 * Retourne l'url de la route correspondante
	 *
	 * @param string $controller
	 * @param string $action
	 * @param array $params
	 * @return string
	 *
	 * @throws \Exception
	 */
	public static function action($controller, $action, $params = array())
	{
		if (!is_array($params)) {
			throw new \Exception("Param not is array");
		}
		$param_url = '';
		if (count($params) > 0) {
			$param_url = http_build_query($params);
			if ($param_url != '')
				$param_url = '?' . $param_url;
		}
		/**
		 * @var $route Route
		 */
		foreach (self::$routes as $route) {
			if (($route->getController() == $controller) and ($route->getAction() == $action))
				return self::getUrl() . $route->getPattern() . $param_url;
		}
		throw new \Exception("Route $controller:$action not defined");
	}
}
