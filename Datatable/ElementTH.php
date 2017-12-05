<?php
namespace Harmony2\Datatable;

/**
 * Gestion des élèments TH
 */
class ElementTH
{
    /**
     *
     * @var array
     */
    private $data = array();

    /**
     * Merge le tableau $data avec le tableau passé en paramètre
     * 
     * @param array $options
     */
    public function __construct(Array $options = array())
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
    
    public function setValues($data)
    {
        $this->data = array_merge($this->data, $data);
    }

    /**
     * Retourne la donnée associée au nom donné
     * 
     * @param string $name
     * @return mixed
     */
    public function getOption($name)
    {
        if (isset($this->data[$name]))
            return $this->data[$name];
        else
            return null;
    }

    /**
     * Retourne si oui ou non une valeur existe dans le tableau de données
     * 
     * @param string $name
     * @return boolean
     */
    public function defined($name)
    {
        return array_key_exists($name, $this->data);
    }
    
    public function clean()
    {
        $this->data = array();
    }
}
