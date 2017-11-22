<?php

namespace Harmony2;

use Exception;
use DateTime;

/**
 * Class Entity
 * @package Harmony2
 *
 */
class Entity {

    static protected $parse_template = false;

    //TODO desactivé l'accès public des attribut de class

    protected $collectionClassName  = '';
    protected $table_name           = '';
    protected $attributs            = array();
    //private $attributValue          = array();
    private $attributUpdate         = array();
    private $__primaryKey           = '';

    public function callDestruct() {
      unset($this->collectionClassName);
      unset($this->table_name);


      foreach ($this->attributUpdate as $field=>$value) {
        unset($this->$field);
      }

      unset($this->attributs);
      unset($this->attributUpdate);
      unset($this->__primaryKey);
    }

    public static function enableParse() {
        self::$parse_template = true;
    }

    public static function disableParse() {
        self::$parse_template = false;
    }

    public static function getParse() {
        return self::$parse_template;
    }

    public function getCollectionClassName()
    {
        if ($this->collectionClassName != '')
            return $this->collectionClassName;
        else
            return 'Harmony2\EntityCollection';
    }

    public function sqlTableName()
    {
        return $this->table_name;
    }

    public function sqlAttributUpdate()
    {
        return $this->attributUpdate;
    }

    public function sqlField($field)
    {

        if(!isset($this->attributs[$field][0]))
            throw new \Exception("L'attribut n'existe pas: ".$field);
        return $this->attributs[$field][0];
    }

    public function primaryAttribut()
    {
        if ($this->__primaryKey != '')
            return $this->__primaryKey;
        foreach($this->attributs as $field => $value) {
            if ($value[1]['PK'] == 'PK') {
                $this->__primaryKey = $field;
                return $field;
            }
        }
        return false;
    }

    public function __construct()
    {
        if (count($this->attributs) == 0)
            throw new Exception('Attribut not defined');
        if ($this->table_name == '')
            throw new Exception('Attribut not defined');
    }

    public static function getClass()
		{
			return get_called_class ();
		}

    public function attributGetConfig()
    {
        return $this->attributs;
    }

    public function attributExist($key) {
        return array_key_exists($key, $this->attributs);
    }

	/**
	 * @param $field
	 * @param $value
	 * @param bool $force
	 * @return $this
	 * @throws Exception
	 */
    public function attributSet($field, $value, $force = false)
    {

        $field = strtolower($field);
        if (!isset($this->attributs[$field]) && false === $force)
            throw new Exception('Attribut not implemented');
        if (!isset($this->$field) || $this->$field !== $value) {
            $this->$field = $value;

            if($field != $this->primaryAttribut() || true === $force)
                $this->attributUpdate[$field] = true;
        }

        return $this;
    }

    public function attributGet($field)
    {
        $field = strtolower($field);
        if (!isset($this->attributs[$field]))
            throw new Exception('Attribut not implemented : '.$field);

        if (self::getParse() == true) {
            return isset($this->$field) ? htmlentities($this->$field, ENT_QUOTES | ENT_HTML401) : null;
        }
        return isset($this->$field) ? $this->$field : null;
    }

    public function resetToUpdate()
    {

      unset($this->attributUpdate);
      $this->attributUpdate = array();
    }

    public function dateFormat($date, $format = 'Y-m-d', $formatReturn = 'd/m/Y')
    {
        return self::staticDateFormat($date, $format, $formatReturn);

    }

    public static function dateSqlToLocale($date) {
        return self::staticDateFormat($date, 'Y-m-d', LOCALE_DATE_FORMAT);
    }

    public static function dateLocaleToSql($date) {
        return self::staticDateFormat($date, LOCALE_DATE_FORMAT, 'Y-m-d');
    }

    private static function staticDateFormat($date, $format = 'Y-m-d', $formatReturn = 'd/m/Y')
    {
        $datetime = DateTime::createFromFormat($format, $date);
        if ($datetime instanceof \DateTime)
            return \date_format($datetime, $formatReturn);
        else
            return null;
    }

    /**
     * jj/mm/aaaa => aaaa-mm-jj
     * @param $date
     * @return bool|null|string
     */
    public function dateFrToSql($date)
    {
        return $this->dateFormat($date, 'd/m/Y', 'Y-m-d');
    }

    /**
     * aaaa-mm-jj => jj/mm/aaaa
     * @param $dateStr
     * @return bool|null|string
     */
    function dateSqlToFr($dateStr)
    {
        return $this->dateFormat($dateStr, 'Y-m-d', 'd/m/Y');
    }

    /**
     * aaaa-mm-jj hh:mm:ss => jj/mm/aaaa
     * @param $datetime
     * @return bool|null|string
     */
    function datetimeToDateFr($datetime)
    {
        return $this->dateFormat($datetime, 'Y-m-d H:i:s', 'd/m/Y');
    }

    /**
     * aaaa-mm-jj hh:mm:ss => hh:mm:ss
     * @param $datetime
     * @return bool|null|string
     */
    function datetimeToTime($datetime)
    {
        return $this->dateFormat($datetime, 'Y-m-d H:i:s', 'H:i:s');
    }

    /**
     * aaaa-mm-jj hh:mm:ss => jj/mm/aaaa hh:mm:ss
     * @param $date
     * @return bool|null|string
     */
    function datetimeSqlToFr($date)
    {
        return $this->dateFormat($date, 'Y-m-d H:i:s', 'd/m/Y H:i:s');
    }

    /**
     * jj/mm/aaaa hh:mm:ss => aaaa-mm-jj hh:mm:ss
     * @param $date
     * @return bool|null|string
     */
    function datetimeFrToSql($date)
    {
        return $this->dateFormat($date, 'd/m/Y H:i:s', 'Y-m-d H:i:s');
    }

    public static function datetimeSqlToLocale($date) {
        return self::staticDateFormat($date, 'Y-m-d H:i:s', LOCALE_DATE_FORMAT." H:i:s");
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        switch(strtolower(substr($name, 0 ,3)))
        {
            case 'get' :
                if (method_exists($this, '__'.strtolower($name))) {
                    $functionName = '__'.$name;
                    return $this->$functionName();
                } else {
                    $field = substr($name, 3);
                    if ($field == '')
                        return call_user_func_array(array($this, 'attributGet'), $arguments);
                    else
                        return $this->attributGet($field);
                }
                break;
            case 'set':
                if (method_exists($this, '__'.strtolower($name))) {
                    $functionName = '__'.$name;
                    return $this->$functionName($arguments[0]);
                } else {
                    $field = substr($name, 3);
                    $force = false;
                    if (array_key_exists('force', $arguments) && true === $arguments['force'])
                        $force = true;
                    return $this->attributSet($field, $arguments[0], $force);
                }
                break;
        }
        return false;
    }

    function toStringMontant($montant, $arrondi=0) {
        return number_format($montant, $arrondi, ',', ' ').' €';
    }

    public function formatNull($value)
    {
        return null === $value ? 'Non' : 'Oui';
    }

    public function formatActif($value)
    {
        return 0 === (int)$value ? 'Non' : 'Oui';
    }

    /**
     * Permet d'hydrater un objet grâce à un tableau standard
     *
     * @param array $values
     *
     * @return \Harmony2\Entity
     */
    public function hydrate(Array $values)
    {
        foreach($values as $k => $v) {
            $method = 'set' . $k;
            $this->{$method}($v);
        }

        return $this;
    }

    public function validate() {
        $validator = new Validator();
        return $validator;
    }
}
