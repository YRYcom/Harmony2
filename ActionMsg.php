<?php

namespace Harmony2;

/**
 * Gestion des messages d'actions
 */
class ActionMsg
{
    /**
     * Les différents type de message
     */

    const Erreur = 1;
    const Confirmation = 2;
    const Alert = 3;

    /**
     * Par défaut, c'est un message d'alerte
     * 
     * @var integer 
     */
    private $type_msg = 3;

    /**
     *
     * @var array 
     */
    private $lesMsgsStr = array();

    /**
     *
     * @var array 
     */
    private $lesMsgs = array();

    /**
     * Retourne le nombre de message dans le type désiré
     * 
     * @param integer $type
     * @return integer
     */
    public function count($type)
    {
        return count($this->lesMsgs[$type]);
    }

    /**
     * Retourne tous les types de message disponibles
     * 
     * @return array
     */
    public function getDefineType()
    {
        return array(self::Erreur, self::Confirmation, self::Alert);
    }

    /**
     * Retourne le nombre de message total (tous les types)
     * 
     * @return integer
     */
    public function countAll()
    {
        $countAll = 0;
        foreach ($this->lesMsgs as $v)
            $countAll += count($v);

        return $countAll;
    }

    /**
     * Donne le type voulu au message
     * 
     * @param integer $type
     */
    public function setTypeMsg($type)
    {
        $this->type_msg = $type;
    }


  /**
   * Renvoi le type de mùessage
   *
   * @return int
   */
    public function getTypeMsg()
    {
        return $this->type_msg;
    }

    /**
     * Retourne tous les messages d'un type donné
     * 
     * @param integer $type
     * @return array
     */
    public function getMsg($type)
    {
        if ($this->lesMsgs[$type])
            return $this->lesMsgs[$type];
        else
            return array();
    }

    /**
     * Ajoute une clé d'un certain type donné en paramètre
     * 
     * @param string $cle
     * @param integer $type
     * @param array $params
     */
    public function addKey($cle, $type, $params = array())
    {
        $this->lesMsgs[$type][$cle] = true;
        if ($params)
        {
            $this->lesMsgs[$type][$cle] = $params;
        }
    }

    /**
     * Définit le message de la clé donné en paramètre
     * 
     * @param string $cle
     * @param string $msg
     */
    public function setKeyMsg($cle, $msg)
    {
        $this->lesMsgsStr[$cle] = $msg;
    }

    /**
     * Retourne le message en fonction de la clé donnée
     * 
     * @param string $cle
     * @return string
     */
    public function getKeyMsg($cle)
    {
        return $this->lesMsgsStr[$cle];
    }

    /**
     * Indique si une clé existe dans le tableau de données
     * 
     * @param string $cle
     * @return boolean
     */
    public function isdefined($cle)
    {
        $found = FALSE;
        foreach ($this->lesMsgs as $array)
        {
            if (array_key_exists($cle, $array))
                $found = TRUE;
        }
        return $found;
    }

    /**
     * Supprime une clé existante
     * 
     * @param string $cle
     * @return boolean
     */
    public function unsetKey($cle)
    {
        $found = FALSE;
        foreach ($this->lesMsgs as $key => $array)
        {
            if (array_key_exists($cle, $array))
            {
                $found = TRUE;
                unset($this->lesMsgs[$key][$cle]);
            }
        }
        return $found;
    }

}
