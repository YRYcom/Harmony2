<?php
namespace Harmony2\Datatable;

/**
 * Gestion des élèments TR du tableau
 */
class ElementTR extends ElementHTML
{
    /**
     *
     * @var array
     */
    private $listTD = array();

    /**
     * Bloque le constructeur parent
     * 
     * @return boolean
     */
    public function __construct()
    {
        return true;
    }

    /**
     * Ajoute un élèment TD à la ligne
     * 
     * @param ElementTD $td
     */
    public function addTD($td)
    {
        $this->listTD[] = $td;
    }

    /**
     * Retour la liste de tous les TD de la ligne
     * 
     * @return array
     */
    public function getListTD()
    {
        return $this->listTD;
    }

    /**
     * Réinitialise la liste des TD
     *
     */
    public function clean() 
    {
        $this->listTD = array();
    }
}
