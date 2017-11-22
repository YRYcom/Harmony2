<?php

namespace Harmony2;

use Harmony2\Http\Request;

/**
 * Gestion des templates
 */
class TemplateFactory
{

	/** @var  Request $_request */
	private static $_request;
	/** @var  array $_provider */
	private static $_provider;
	/** @var array $_call */
	private static $_call;
	/** @var bool $_init */
	private static $_init = false;

	public static function init(Request $request)
	{
		self::$_request = $request;
		self::$_call = [];
		self::$_init=true;
	}

	public static function register($pattern, $classname)
	{
		self::$_provider[$pattern][] = $classname;
	}

	private static function getpregPattern($pattern)
	{
		return '#^' . str_replace('*', '(.*)', $pattern) . '$#';
	}

	private static function check($pattern, $filename)
	{
		return (preg_match(self::getpregPattern($pattern), $filename) == 1);
	}


	public static function call($filename, Template $template)
	{
		if(self::$_init == false)
			return true;

		foreach (self::$_provider as $pattern => $classnames) {
			if (self::check($pattern, $filename)) {
				foreach ($classnames as $classname) {
					/** @var TemplateProvider $provider */
					if (!in_array($classname, self::$_call)) {
						self::$_call[] = $classname;
						//On desactive la securité de l'entité car on inclue le call est fait pendant le parse d'un fichier
						Entity::disableParse();
						$provider = new $classname();
						$provider->compose($template, self::$_request);
						Entity::enableParse();
					}
				}
			}
		}

		return true;
	}

}