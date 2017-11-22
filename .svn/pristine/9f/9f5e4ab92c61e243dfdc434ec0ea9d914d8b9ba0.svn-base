<?php

namespace Harmony2;

use \Exception as Exception;

/**
 * Gestion des exceptions
 */
class ExceptionManager
{
    /**
     * Si on retourne ou pas les exceptions
     *
     * @var boolean
     */
    static private $returnException = false;
    static private $msgException = '';

    /**
     * Active l'affichage d'exception pour l'ensemble des instances
     * 
     */
    public static function activeException()
    {
        self::$returnException = true;
    }

    /**
     * Désactive l'affichage d'exception pour l'ensemble des instances
     * 
     */
    public static function desactiveException()
    {
        self::$returnException = false;
    }

    /**
     * Récupère le dernier message d'exception
     * 
     * @return string
     */
    public static function getError()
    {
        return self::$msgException;
    }

    /**
     * Renvoi une exception si activé, false sinon
     * 
     * @param string $message
     * @return boolean
     * @throws \exception
     */
    public static function generateException($message)
    {
        self::$msgException = $message;
        if (self::$returnException)
            throw new Exception($message);
        else
            return false;
    }
}