<?php
namespace Harmony2\Datatable;

/**
 * Gestion des élèments TD
 */
class ElementTD
{
    /**
     * Les données du TD
     *
     * @var array
     */
    private $data = array();

    /**
     * Merge le tableau $data avec le tableau passé en paramètre
     * 
     * @param array $options
     */
    public function __construct($options = array())
    {
        $this->data = array_merge($this->data, $options); 
    }

    /**
     * Stocke une donnée dans le tableau de données
     * 
     * @param string $name
     * @param mixed $value
     */
    public function setOption($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Retourne la donnée associée au nom donné
     * 
     * @param string $name
     * @return mixed
     */
    public function getOption($name)
    {
        return $this->data[$name];
    }
    
    /**
     * Set la valeur du TD
     *
     * @param string $value la valeur à afficher dans le TD
     * @return ElementTD
     */
    public function value($value)
    {
        $this->data['value'] = $value;

        return $this;
    }

    /**
     * Permet de définir une fonction à appliquer sur un champ spécifique
     *
     * @param string $func Le nom de la fonction à appliquer
     * @return ElementTD
     */
    public function func($func)
    {
        $this->data['func'] = $func;

        return $this;
    }

    /**
     * Retour les données associées à la TD
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->data;
    }

    /**
     * Efface les données liées à la TD
     */
    public function clean()
    {
        $this->data = array();
    }
}