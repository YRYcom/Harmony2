<?php
/**
 * Created by Yannick LALLEAU.
 * Date: 13/12/2016
 * Time: 12:03
 */

namespace Harmony2;


class Config
{

	private static $_config = [];

	public static function get($name){
		if(self::defined($name))
			return self::$_config[$name];
		else
			return false;
	}

	public static function set($name, $value =null){
		self::$_config[$name] = $value;
	}

	public static function defined($name){
		return isset(self::$_config[$name]);
	}

}