<?php

namespace Harmony2;

class Locale
{

	public static $LOCALE = '';
	public static $LOCALE_DATE_FORMAT = '';
	public static $LOCALE_DATETIME_FORMAT= '';

	public static $LOCALE_DIR = '';
	public static $LOCALE_DOMAIN = '';

	public static function start($leslangues = [])
	{

		if (file_exists(__DIR__ . '/../../config/locale.php'))
			include_once __DIR__ . '/../../config/locale.php';

		if(defined('LOCALE'))
			self::$LOCALE = LOCALE;
		if(defined('LOCALE_DATE_FORMAT'))
			self::$LOCALE_DATE_FORMAT = LOCALE_DATE_FORMAT;
		if(defined('LOCALE'))
			self::$LOCALE_DATETIME_FORMAT = LOCALE_DATETIME_FORMAT;

		if(defined('LOCALE'))
			self::$LOCALE_DIR = LOCALE_DIR;
		if(defined('LOCALE'))
			self::$LOCALE_DOMAIN = LOCALE_DOMAIN;

		//chargement des fonctions nécessaires aux traductions
		include_once __DIR__ . '/locales/functions.php';

		$locale = self::$LOCALE . ".UTF-8";
		putenv("LANGUAGE=");
		putenv("LANG=" . $locale);
		setlocale(LC_ALL, $locale);
		setlocale(LC_NUMERIC, 'C');

		if (defined("LOCALE") and in_array(strtolower(self::$LOCALE), $leslangues)) {
			if (($value = bindtextdomain(self::$LOCALE_DOMAIN, self::$LOCALE_DIR)) != self::$LOCALE_DIR) {
				die('erreur dico bindtextdomain : ' . $value);
			}
			if (($value = textdomain(self::$LOCALE_DOMAIN)) != self::$LOCALE_DOMAIN) {
				die('erreur dico : textdomain : ' . $value);
			}
		}
	}

	public static function getLanguage()
	{
		return explode('_', self::$LOCALE)[0];
	}
}