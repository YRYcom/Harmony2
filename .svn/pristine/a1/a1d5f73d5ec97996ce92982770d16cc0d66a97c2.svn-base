<?php

namespace Harmony2;

class Locale
{

    public static function start($leslangues = []) {
        include_once __DIR__.'/../../config/locale.php';

        //chargement des fonctions nécessaires aux traductions
        include_once __DIR__.'/locales/functions.php';

        $locale= LOCALE.".UTF-8";
        putenv("LANGUAGE=");
        putenv("LANG=".$locale);
        setlocale(LC_ALL, $locale);
				setlocale(LC_NUMERIC,'C');

        if(defined("LOCALE") and in_array(strtolower(LOCALE), $leslangues)) {
            if (($value = bindtextdomain(LOCALE_DOMAIN, LOCALE_DIR)) != LOCALE_DIR) {
                die('erreur dico bindtextdomain : ' . $value);
            }
            if (($value = textdomain(LOCALE_DOMAIN)) != LOCALE_DOMAIN) {
                die('erreur dico : textdomain : ' . $value);
            }
        }
    }

    public static function getLanguage() {
        return explode('_',LOCALE)[0];
    }
}