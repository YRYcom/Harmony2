<?php

namespace Harmony2;

use Harmony2\Http\Request;
use Harmony2\Router\Router;

class Application
{
	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var boolean
	 */
	public static $debug;

	public function __construct(Request $request)
	{
		$this->request = $request;
		TemplateFactory::init($request);
		self::$debug = (array_key_exists('INTERFACE_WEB', $_SERVER) && 'VM' === $_SERVER['INTERFACE_WEB']) ? true : false;
	}

	/**
	 * Lancement de l'application (API)
	 */
	public function run()
	{

		$routes = Router::getRoutes();

		$uri = $this->request->getBaseURI();
		$httpMethod = strtolower($this->request->getMethod());
		/** @var \Harmony2\Router\Route $r */
		foreach ($routes as $r) {
			// ******************
			// On parcourt les différentes routes enregistrées afin de trouver le bon contrôleur
			// ******************
			if (preg_match('#^' . $r->getPattern() . '$#', $uri, $params) && $httpMethod == strtolower($r->getHttpMethod())) {
				array_shift($params);

				$className = $r->getController();
				if (!class_exists($className))
					throw new \Exception("Missing controller " . $r->getController() . " for route " . $uri, 500);

				$c = new $className($this->request);
				if (!method_exists($c, $r->getAction()))
					throw new \Exception("Missing action " . $r->getAction() . " in controller " . $r->getController() . " for route " . $uri, 500);

				$this->request->setRoute($r);
				// Appel du "preController" s'il existe afin d'effectuer certaines actions (vérification prérequis, etc...)
				// avant de faire appel au contrôleur lui-même
				if (method_exists($c, 'preController')) {
					$c->preController();
				}
				$response = call_user_func_array([$c, $r->getAction()], $params);

				if (!is_array($response) && !is_object($response))
					throw new \Exception("Action " . $r->getAction() . " in controller " . $r->getController() . " should return an array, " . gettype($response) . " given", 500);

				return $response;
			}
		}

		// Si on arrive ici, c'est qu'aucune roure ne correspond
		// = erreur 400
		throw new \Exception("Unknow url " . $_SERVER['REQUEST_URI'], 404);
	}


}
