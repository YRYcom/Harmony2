<?php

namespace Harmony2;

use Exception;

/**
 * Class EntityCollection
 * @package Harmony2
 * @method findOneById($id) array
 */
class EntityCollection
{

  protected $sql;
  private $entityClass = '';

  public function __construct($sql = null, $entityClass ='') {
    if (!($sql instanceof Sql))
      throw new Exception('Sql not defined');
    if ($entityClass == '')
      throw new Exception('Not entity');
    $this->entityClass = $entityClass;
    $this->sql = $sql;

  }

  public function find($primaryKey) {

    /**
     * @var $e Entity
     */
    $e = new $this->entityClass();

    $query = "SELECT ";
    $i = 0;

    foreach ($e->attributGetConfig() as $field=>$value) {
      if ($i > 0)
        $query .= ", ";
      $query .= "{".$this->entityClass."::".$field."}";
      $i++;
    }
    $query .= "FROM {".$this->entityClass."} where {".$this->entityClass."::".$e->primaryAttribut()."} = '#1#'";
    if ($e->attributExist('at_deleted'))
      $query .= " and {".$this->entityClass."::at_deleted} is null";
    $this->sql->setQuery($query);
    $this->sql->bindField($this->entityClass);
    $this->sql->addParam(1, $primaryKey);
    $this->sql->execute();

    return $this->sql->fetch("field");
  }

  public function findBy($key, $search) {
    /** @var Entity $e */
    $e = new $this->entityClass();

    $query = "SELECT ";
    $i = 0;

    foreach ($e->attributGetConfig() as $field=>$value) {
      if ($i > 0)
        $query .= ", ";
      $query .= "{".$this->entityClass."::".$field."}";
      $i++;
    }
    $query .= " FROM {".$this->entityClass."} where {".$this->entityClass."::".strtolower($key)."} = '#1#'";
    if ($e->attributExist('at_deleted'))
      $query .= " and {".$this->entityClass."::at_deleted} is null";
    $this->sql->setQuery($query);
    $this->sql->bindField($this->entityClass);
    $this->sql->addParam(1, $search);
    $this->sql->execute();

    return $this->sql->fetchAll("field");
  }

  /**
   * @param array $criteria
   * @param array $options
   * @return array|bool
   */
  public function findByCriteria($criteria, $options=[]) {
    if (empty($criteria) || !is_array($criteria)) {
      return false;
    }
    /** @var Entity $e */
    $e = new $this->entityClass();

    $query = "SELECT ";
    $i = 0;

    foreach ($e->attributGetConfig() as $field=>$value) {
      if ($i > 0)
        $query .= ", ";
      $query .= "{".$this->entityClass."::".$field."}";
      $i++;
    }
    $query .= " FROM {".$this->entityClass."} WHERE ";

    $i = 0;
    foreach ($criteria as $field=>$value) {
      $i++;
      if ($i > 1) {
        $query .= " AND ";
      }
      $query .= " {".$this->entityClass."::".strtolower($field)."} = '#".$i."#' ";
      $this->sql->addParam($i, $value);
    }
    if ($e->attributExist('at_deleted'))
      $query .= " and {".$this->entityClass."::at_deleted} is null";

    if (isset($options['orderby']) and is_array($options['orderby'])){
      $orderby = ' order by ';
      foreach($options['orderby'] as $attribut => $sort){
        if(in_array($sort,['ASC', 'DESC']))
          $orderby .= " {".$this->entityClass."::".$attribut."} ".$sort;
      }
      $query .= $orderby;
    }
    $this->sql->setQuery($query);

    $this->sql->bindField($this->entityClass);

    $this->sql->execute();

    return $this->sql->fetchAll("field");
  }

  public function findOneBy($key, $search) {
    /** @var Entity $e */
    $e = new $this->entityClass();

    $query = "SELECT ";
    $i = 0;

    foreach ($e->attributGetConfig() as $field=>$value) {
      if ($i > 0)
        $query .= ", ";
      $query .= "{".$this->entityClass."::".$field."}";
      $i++;
    }
    $query .= " FROM {".$this->entityClass."} where {".$this->entityClass."::".strtolower($key)."} = '#1#'";
    if ($e->attributExist('at_deleted'))
      $query .= " and {".$this->entityClass."::at_deleted} is null";
    $query .=" LIMIT 1";
    $this->sql->setQuery($query);
    $this->sql->bindField($this->entityClass);
    $this->sql->addParam(1, $search);
    $this->sql->execute();

    return $this->sql->fetch("field");
  }

  public function findOneByCriteria($criteria) {
    if (empty($criteria) || !is_array($criteria)) {
      return false;
    }

    /** @var Entity $e */
    $e = new $this->entityClass();

    $query = "SELECT ";
    $i = 0;

    foreach ($e->attributGetConfig() as $field=>$value) {
      if ($i > 0)
        $query .= ", ";
      $query .= "{".$this->entityClass."::".$field."}";
      $i++;
    }
    $query .= " FROM {".$this->entityClass."} WHERE ";

    $i = 0;
    foreach ($criteria as $field=>$value) {
      $i++;
      if ($i > 1) {
        $query .= " AND ";
      }
      $query .= " {".$this->entityClass."::".strtolower($field)."} = '#".$i."#' ";
      $this->sql->addParam($i, $value);
    }
    if ($e->attributExist('at_deleted'))
      $query .= " and {".$this->entityClass."::at_deleted} is null";
    $query .= " LIMIT 1";
    $this->sql->setQuery($query);
    $this->sql->bindField($this->entityClass);

    if(!$this->sql->execute())
      throw new Exception('Erreur SQL: '.$this->sql->getError());
    return $this->sql->fetch("field");
  }

  public function findAll() {
    /** @var Entity $e */
    $e = new $this->entityClass();
    $query = "SELECT ";
    $i = 0;

    foreach ($e->attributGetConfig() as $field=>$value) {
      if ($i > 0)
        $query .= ", ";
      $query .= "{".$this->entityClass."::".$field."}";
      $i++;
    }
    $query .= " FROM {".$this->entityClass."}";
    if ($e->attributExist('at_deleted'))
      $query .= " where {".$this->entityClass."::at_deleted} is null";
    $this->sql->setQuery($query);
    $this->sql->bindField($this->entityClass);
    $this->sql->execute();

    return $this->sql->fetchAll("field");
  }

  /**
   * Fonction de comptage des entités selon des criteres.
   */
  public function count() {
    /** @var Entity $e */
    $e = new $this->entityClass();

    $query = "SELECT count(*) as nb FROM {".$this->entityClass."}";
    if ($e->attributExist('at_deleted'))
      $query .= " where {".$this->entityClass."::at_deleted} is null";
    $this->sql->setQuery($query);
    $this->sql->bindField($this->entityClass);
    $this->sql->execute();
    $result = $this->sql->fetch('row');
    return $result[0];
  }

  public function countBy($key, $search) {
    /** @var Entity $e */
    $e = new $this->entityClass();

    $query = "SELECT count(*) as nb FROM {".$this->entityClass."} where {".$this->entityClass."::".strtolower($key)."} = '#1#'";
    if ($e->attributExist('at_deleted'))
      $query .= " and {".$this->entityClass."::at_deleted} is null";
    $this->sql->setQuery($query);
    $this->sql->bindField($this->entityClass);
    $this->sql->addParam(1, $search);
    $this->sql->execute();
    $result = $this->sql->fetch('row');
    return $result[0];
  }

  public function countByCriteria($criteria) {
    if (empty($criteria) || !is_array($criteria)) {
      return false;
    }
    /** @var Entity $e */
    $e = new $this->entityClass();

    $query = "SELECT count(*) as nb FROM {".$this->entityClass."} WHERE ";

    $i = 0;
    foreach ($criteria as $field=>$value) {
      $i++;
      if ($i > 1) {
        $query .= " AND ";
      }
      $query .= " {".$this->entityClass."::".strtolower($field)."} = '#".$i."#' ";
      $this->sql->addParam($i, $value);
    }
    if ($e->attributExist('at_deleted'))
      $query .= " and {".$this->entityClass."::at_deleted} is null";
    $this->sql->setQuery($query);
    $this->sql->bindField($this->entityClass);
    $this->sql->execute();
    $result = $this->sql->fetch('row');
    return $result[0];
  }

  public function __call($name, $arguments) {
    switch (true) {
      case (0 === strpos($name, 'findBy')):
        $by = substr($name, 6);
        return $this->findBy($by, $arguments[0]);
        break;
      case (0 === strpos($name, 'findOneBy')):
        $by = substr($name, 9);
        return $this->findOneBy($by, $arguments[0]);
        break;
      default :
        throw new Exception('Method not defined');
        break;
    }
  }
}