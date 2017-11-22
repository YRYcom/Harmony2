<?php
namespace Harmony2;

use \Exception;

/**
 * Chargement automatique des classes en fonction du namespace
 * 
 */
class AutoLoader
{
    /**
     * Les informations sur les namespaces enregistres
     * 
     * @var array
     */
    static private $lesNamespaces = array();

    /**
     * Impossible d'instancier la classe
     * 
     */
    private function __construct()
    {
        return false;
    }
    
    /**
     * Enregistre la méthode load() dans la SPL
     */
    static public function run()
    {
        spl_autoload_register(array('self', 'load'));
    }

    /**
     * Enregistre les informations sur le chargement automatique d'un namespace
     * 
     * @param string $name Le nom du namespace
     * @param string $directory Le répertoire ou pointent le namespace
     * @param string $extension L'extension des fichiers dans le répertoire
     * @param string $prefixe
     * @param bool $matchCase
     */
    static function addNamespace($name, $directory, $extension, $prefixe = '', $matchCase=false)
    {
        AutoLoader::$lesNamespaces[$name] = array('directory' => $directory, 'extension' => $extension, 'prefixe' => $prefixe, 'matchCase' => $matchCase);
    }

    /**
     * Retourne les informations d'un namespace
     * 
     * @param string $name
     * @return array
		 * @throws Exception
     */
    public static function getNamespace($name)
    {
        if (!array_key_exists($name , AutoLoader::$lesNamespaces))
					throw new Exception('Namespace not defined : ' . $name);

        return AutoLoader::$lesNamespaces[$name];
    }

    /**
     * Inclut la classe demandée ou retourne false si erreur
     *
     * @param $namespace
     * @throws Exception
     */
    static private function load($namespace)
    {
        $splitpath = explode('\\', $namespace);
        if (count($splitpath) == 0) {
            throw new Exception('Namespace not defined for the class : ' . $namespace);
        } elseif (count($splitpath) == 1) {
            $info = AutoLoader::getNamespace($splitpath[0]);
            if(!isset($info['matchCase'])){
							throw new Exception('Namespace not defined for the class : '.$namespace);
						}
            if ($info['matchCase'])
                $path = $info['directory'] . '/'. $info['prefixe']. $splitpath[0] . '.' . $info['extension'];
            else
                $path = $info['directory'] . '/' . $info['prefixe']. strtolower($splitpath[0]) . '.' . $info['extension'];
        } else {

            $info = AutoLoader::getNamespace($splitpath[0]);

            if ($info === false)
                throw new Exception('Namespace not defined : ' . $splitpath[0]);

            $path = $info['directory'];
            //Ajout des dossiers
            $j = 1;
            for ($i = 1; $i < (count($splitpath) - 1); $i++) {
                if ($info['matchCase'])
                    $path .= '/' . $splitpath[$i];
                else
                    $path .= '/' . strtolower($splitpath[$i]);
                $j++;
            }
            //Nom de la classe
            $path .= '/' . $info['prefixe']. $splitpath[$j]. '.' . $info['extension'];

        }
        if (! file_exists($path))
            throw new Exception('File '.$path.'does not exists for the class : '.$namespace);

        /** @noinspection PhpIncludeInspection */

        include_once($path);

    }
}
