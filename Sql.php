<?php

namespace Harmony2;

use mysqli;
use mysqli_result;
use Exception;

/**
 * Class Sql
 * @package Harmony2
 */
class Sql
{

  const PARAM_INT = 'int';

  /** @var array $configConnexion */
  static private $configConnexion = array();
  /** @var array $connect */
  static private $connect = array();
  /** @var string $query */
  public $query = '';
  /** @var null|string $nom */
  private $nom = null;
  /** @var null|string $host */
  private $host = null;
  /** @var null|string $user */
  private $user = null;
  /** @var null|string $pass */
  private $pass = null;
  /** @var null|string $db */
  private $db = null;
  /** @var mysqli_result $result */
  private $result = false;
  /** @var array $parametres */
  private $parametres = array();
  /** @var array $aFieldsTable */
  private $aFieldsTable = array();
  /** @var array $aFieldsValue */
  private $aFieldsValue = array();
  /** @var $returnObject */
  private $returnObject;
  /** @var array $persisted */
  private $persisted = array();
  /** @var string $error */
  private $error = '';
  /** @var bool $enableLog */
  private $enableLog = true;

  /**
   * @param $strEntity
   * @return EntityCollection
   * @throws Exception
   */
  public function getCollection($strEntity)
  {
    if (class_exists($strEntity)) {
      $this->reset();
      /** @var \Harmony2\Entity $entity */
      $entity = new $strEntity();
      $className = $entity->getCollectionClassName();

      return new $className($this, $strEntity);
    }
    throw new Exception('Entity '.$strEntity.' does not exists');
  }

  public function escape_string($string)
  {
    $sql = Sql::$connect[$this->nom];
    /** @var mysqli $sql */
    return $sql->real_escape_string($string);
  }

  /**
   * Réinitialise les données
   * @return $this
   */
  public function reset()
  {
    $this->aFieldsTable = array();
    $this->aFieldsValue = array();
    $this->returnObject = array();
    $this->parametres = array();
    return $this;
  }

  /**
   * Persist une entité, cela a pour effet de l'ajouter à la liste
   * /!\ Seules les entités peuvent être persistées
   * @throws Exception
   * @param \Harmony2\Entity $entity
   * @return boolean
   */
  public function persist($entity)
  {
    if (!$entity instanceof Entity) {
      return ExceptionManager::generateException('Seules les entités peuvent être persistées');
    }
    $this->persisted[] = $entity;
    return true;
  }

  /**
   * Sauvegarde toutes les entités persistées
   * et met à jour l'id avec l'id en base dans le cas d'une insertion
   *
   * @return integer le nombre d'entités qui ont été persistées
   */
  public function flush()
  {
    $nb = 0;
    if (is_array($this->persisted)) {
      /** @var Entity $entity */
      foreach ($this->persisted as $entity) {
        $isUpdate = (isset($entity->id) && 0 < $entity->id) ? true : false;
        $this->save($entity);
        $nb++;

        if (!$isUpdate) {
          $entity->setId($this->getInsertId());
        }
      }
    }

    return $nb;
  }

  /**
   * Ajoute une configuration SQL
   *
   * @param mixed $name Le nom associé à la connexion
   * @param array $config La configuration SQL (host, user, etc...)
   */
  static function setConfigConnexion($name, $config)
  {
    Sql::$configConnexion[$name] = $config;
  }

  /**
   * Sauvegarde en base les changements effectués sur un objet
   *
   * @param Entity $e
   * @return bool
   * @throws Exception
   */
  public function saveChange(Entity &$e)
  {

    if ($e->primaryAttribut() === false)
      throw new Exception('Primary Key not defined');

    $query = "UPDATE ";
    $query .= $e->sqlTableName() . " SET ";


    if(count($e->sqlAttributUpdate()) == 0)
      return false;

    $i = 0;
    foreach ($e->sqlAttributUpdate() as $field => $value) {
      if ($i > 0)
        $query .= ", ";
      $i++;
      $functionName = 'get' . $field;
      $val = $e->$functionName();
      if (null === $val) {
        $query .= $e->sqlField($field) . " = NULL";
      } else {
        $query .= $e->sqlField($field) . " = '#" . $i . "#'";
        $this->addParam($i, $val);
      }
    }

    $i++;
    //Implementer l'existance de plusieur primary key
    $query .= " WHERE  " . $e->sqlField($e->primaryAttribut()) . " = '#" . $i . "#'";
    //Declencher une exception si la valeur de la primary egal false
    $functionName = $e->primaryAttribut();
    $this->addParam($i, $e->$functionName);

    $this->setQuery($query);
    $result = $this->execute();
    $e->resetToUpdate();

    return $result;
  }

  /**
   * Sauvegarde en base un nouvel objet
   * -- Si l'objet n'est pas nouveau alors on fait appel à saveChange automatiquement
   *
   * @param \Harmony2\Entity $e
   * @param boolean $ignore
   * @throws Exception
   * @return mixed
   */
  public function save(Entity &$e, $ignore = false)
  {

    if ($e->primaryAttribut() === false)
      throw new Exception('Primary Key not defined');

    $attr = $e->sqlAttributUpdate();
    if (empty($attr)) {
      return false;
    }

    // Si la clé primaire est defini, on enclenche un saveChange
    if ((int)$e->attributGet($e->primaryAttribut()) > 0) {
      return $this->saveChange($e);
    }

    $i = 1;
    $query = "INSERT " . ($ignore == true ? 'IGNORE ' : '') . "INTO ";
    $query .= $e->sqlTableName() . " ";

    $primary = $e->primaryAttribut();
    $queryDef = $e->sqlField($primary);
    $functionName = 'get' . $primary;
    $val = $e->$functionName();
    if ($val === null) {
      $queryValue = "NULL";
    } else {
      $queryValue = "'#" . $i . "#'";
      $this->addParam($i, $val);
    }

    foreach ($attr as $field => $value) {
      $queryDef .= ", ";
      $queryValue .= ", ";
      $i++;
      $queryDef .= $e->sqlField($field);

      $functionName = 'get' . $field;
      $val = $e->$functionName();

      if ($val === null) {
        $queryValue .= "NULL";
      } else {
        $queryValue .= "'#" . $i . "#'";
        $this->addParam($i, $val);
      }
    }
    $query .= "(" . $queryDef . ") ";
    $query .= "VALUES (" . $queryValue . ");";

    $this->setQuery($query);
    $result = $this->execute();

    if(!$result)
      throw new Exception('Erreur Sql : '.$this->getError().' Requete : '.$this->getQuery());

    $func = 'set' . $primary;
    $e->$func($this->getInsertId());

    $e->resetToUpdate();

		//On appel le callback apres enregistrement reussi

    return $result;
  }

  /**
   * supprime la line de la table correspondant a l'entite
   *
   * @param  Entity $e
   * @throws Exception
   * @return void
   */
  public function delete(Entity $e)
  {
    static $pattern = 'DELETE FROM %s WHERE %s = #1#';

    $pk = $e -> primaryAttribut();

    if (false === $pk)
      throw new Exception('undefined primary property');

    $id = (int)$e -> attributGet($pk);

    if (0 >= $id)
      throw new Exception('bad value for primary property');

    $query = sprintf($pattern, $e -> sqlTableName(), $e -> sqlField($pk));
    $this -> setQuery($query);
    $this -> addParam(1, $id, 'int');

    if (! $this -> execute())
      throw new Exception('failed to delete line ' . $id . ' from ' . $e -> sqlTableName());
  }

  /**
   * Permet d'activer le log des requêtes lentes
   */
  public function enableLog()
  {
    $this->enableLog = true;
  }

  /**
   * Permet de désactiver le log des requêtes lentes
   */
  public function disableLog()
  {
    $this->enableLog = false;
  }

  /**
   * Sql constructor.
   * @param string $nom Le nom de l'instance souhaité
   */
  public function __construct($nom = '')
  {
    if (empty($nom))
      return ExceptionManager::generateException('La configuration n\'est pas défini');
    $this->nom = $nom;

    if (!array_key_exists($this->nom, Sql::$configConnexion))
      return ExceptionManager::generateException('La configuration n\'existe pas : ' . $this->nom);

    $this->host = Sql::$configConnexion[$this->nom]['host'];
    $this->user = Sql::$configConnexion[$this->nom]['user'];
    $this->pass = Sql::$configConnexion[$this->nom]['pass'];
    $this->db = Sql::$configConnexion[$this->nom]['db'];

    //Si l'instance n'est pas connnecté on connecte
    if (!$this->isConnected()) {
      if (!$this->connection()) {
        return ExceptionManager::generateException('La connexion à la base de donnée a echoué : ' . Sql::$connect[$this->nom]->connect_error);
      }
    }

    return true;
  }

  /**
   * Retourne le nom de l'hôte de la configuration SQL
   *
   * @return string
   */
  public function getHostName()
  {
    return $this->host;
  }

  /**
   * Retourne le nom de la configuration SQL
   *
   * @return string
   */
  public function getConfigName()
  {
    return $this->nom;
  }

  /**
   * Indique si l'on est connecté ou pas
   *
   * @return boolean
   */
  public function isConnected()
  {
    if (!isset(Sql::$connect[$this->nom]) || !(Sql::$connect[$this->nom] instanceof mysqli) || Sql::$connect[$this->nom]->connect_error)
      return false;

    return true;
  }

  /**
   * Retourne si l'on a un résultat
   *
   * @return boolean
   */
  public function isResult()
  {
    return $this->result instanceof mysqli_result;
  }

  /**
   * Ajoute des paramètres à la requete
   * @param $var
   * @param $value
   * @param string $type
   * @return $this
   * @throws Exception
   */
  public function addParam($var, $value, $type = 'string')
  {
    if (!($this->isConnected()))
      return ExceptionManager::generateException('Pas connecté à la base de donnée');

    switch ($type) {
      case self::PARAM_INT :
        $value = (int)$value;
        break;
      case 'float' :
        $value = (float)$value;
        break;
      case 'html' :
        /** @var mysqli $sql */
        $sql = Sql::$connect[$this->nom];
        $value = $sql->real_escape_string($value);
        break;
      case 'textunderscore' :
        /** @var mysqli $sql */
        $sql = Sql::$connect[$this->nom];
        $value = $sql->real_escape_string(strip_tags((string)$value));
        break;
      case 'sql' :
        $value = (string)$value;
        break;
      case 'like' :
        /** @var mysqli $sql */
        $sql = Sql::$connect[$this->nom];
        $value = $sql->real_escape_string($value);
        $value = addcslashes($value, "%_");
        break;
      case 'under_special' :
      default :
        /** @var mysqli $sql */
        $sql = Sql::$connect[$this->nom];
        $value = $sql->real_escape_string($value);
        break;
    }
    $this->parametres['#' . $var . '#'] = $value;
    return $this;
  }

  /**
   * Libère les resultats
   * @throws Exception
   * @return boolean
   */
  public function free()
  {
    if (!($this->isResult()))
      return ExceptionManager::generateException('Pas de résultat');
    $this->result->free_result();
    return true;
  }

  /**
   * Statistiques Mysqli
   *
   * @return array
   */
  public function stats()
  {
    if (!($this->isConnected()))
      return ExceptionManager::generateException('Non connecté à la base de données');
    /** @var mysqli $sql */
    $sql = Sql::$connect[$this->nom];
    return $sql->stat();
  }

  /**
   * Défini la requete
   *
   * @param $query
   * @return $this
   */
  public function setQuery($query)
  {
    $this->query = $query;
    return $this;
  }


  /**
   * Appel rapide à trois des functions les plus utilisées (setQuery -> execute -> fetch)
   * La fonction renvoie un tableau contenant TOUTES les lignes renvoyées par la requête...
   * @param string $query La requete SQL a effectuer.
   * @param null $params
   * @param string $fetchMode
   * @return array|bool
   * @throws Exception
   */
  public function setQueryEx($query, $params = NULL, $fetchMode = 'assoc')
  {
    $this->setQuery($query);
    if ($params) {
      foreach ($params as $key => &$param)
        $this->addParam($key, $param);
    }

    try {
      if (!$this->execute())
        return ExceptionManager::generateException('La connexion à la base de données a echoué : ' . Sql::$connect[$this->nom]->connect_error);
    } catch (Exception $e) {
      throw $e;
    }

    return $this->fetchAll($fetchMode);
  }

  /**
   * Renvoie la requête à executer correctement formatée
   * @return mixed|string
   */
  public function getQuery()
  {
    if (!empty($this->parametres))
      $this->query = str_replace(array_keys($this->parametres), array_values($this->parametres), $this->query);

    /** @noinspection PhpUnusedLocalVariableInspection */
    foreach ($this->aFieldsTable as $alias => &$value) {
      /** @var Entity $entity */
      $entity = $this->aFieldsTable[$alias][0];
      $basePrefix = $this->aFieldsTable[$alias][2];

      if (!$basePrefix)
        $this->query = str_replace('{' . $alias . '}', $entity->sqlTableName() . " t" . $this->aFieldsTable[$alias][1], $this->query);
      else
        $this->query = str_replace('{' . $alias . '}', $basePrefix . '.' . $entity->sqlTableName() . " t" . $this->aFieldsTable[$alias][1], $this->query);

      $indicePrecedent = 0;
      while (($indice = strpos($this->query, '{' . $alias . '::', $indicePrecedent)) !== false) {
        $indice++;
        $indicePrecedent = $indice;

        $replaceTag = substr($this->query, $indice, (strpos($this->query, '}', $indice)) - $indice);
        $attribut = explode('::', $replaceTag)[1];
        if ($attribut == '_ALL') {
          $this->query = str_replace('{' . $replaceTag . '}', "t" . $this->aFieldsTable[$alias][1] . ".* ", $this->query);
        } else {
          $aliasField = "";
          if (strpos($attribut, '@') !== false) {
            list($attribut, $aliasField) = explode('@', $attribut);
            if (empty($aliasField)) {
              $aliasField = $attribut;
            }
          }

          $field = $entity->sqlField($attribut);

          if (!empty($aliasField)) {
            $aliasAttribut = $aliasField;
          } else {
            $aliasAttribut = $field;
          }

          $this->aFieldsValue["t" . $this->aFieldsTable[$alias][1] . "." . $field] = array(strtolower($attribut), count($this->aFieldsValue), $alias, $aliasAttribut, -1);
          $this->query = str_replace('{' . $replaceTag . '}', "t" . $this->aFieldsTable[$alias][1] . "." . $field . " " . $aliasField, $this->query);
        }
      }
    }

    return $this->query;
  }

  /**
   * Renvoi le dernier ID inséré
   * @return bool
   * @throws Exception
   */
  public function getInsertId()
  {
    if (!$this->isConnected())
      return ExceptionManager::generateException('Pas connecté à la base de donnée');

    return Sql::$connect[$this->nom]->insert_id;
  }

  /**
   * Construit la requete avec les attributs des entites
   *
   * exemple : bindField("Abo", "Abonne") => remplacera les {Abo} par le nom de la table correspondant à l'entite Abonne
   *
   * Exemple d'usage :
   *
   * Utilisation des attributs de classe comme champs
   * {Table::Attribut}                => "alias de la table"."champ en base"              => {Abonne::id}                 => t0.id_abonnes
   * => la traduction est automatique. La valeur de l'attribut sera automatiquement associé à l'instance de l'objet Table dans le résultat fetch en mode Field
   * {Table::Attribut@}           => "alias de la table"."attribut de l'entite" => {Abonne::id@}              => t0.id_abonnes as id
   * => la traduction est automatique. Cela permet de recuperer la valeur d'un attribut dans un fetch en mode Assoc sans connaitre le nom réel du champs
   * {Table::Attribut@id_abo} => "alias de la table"."alias definit"              => {Abonne::id@id_abo}  => t0.id_abonnes as id_abo
   * => la traduction est automatique. Cela permet de recuperer la valeur d'un attribut dans un fetch en mode Assoc avec un nom de champs spécifique
   *
   * Le from
   * {Table} => "table de la base" t"nombre de table" => {Abonne} => abonnes t0
   *
   * @param $alias
   * @param string $className
   * @param bool|FALSE $basePrefix
   * @return $this
   * @throws Exception
   */
  public function bindField($alias, $className = '', $basePrefix = FALSE)
  {
    if ($className == '')
      $className = $alias;

    if (array_key_exists($alias, $this->aFieldsTable))
      return ExceptionManager::generateException('L\'alias est déjà affecté');

    $entity = new $className();
    $this->aFieldsTable[$alias] = array($entity, count($this->aFieldsTable), $basePrefix);
    return $this;
  }

  /**
   * @param $alias
   * @param $instanceEntity
   * @return $this
   * @throws Exception
   */
  public function bindInstanceField($alias, $instanceEntity)
  {
    if (array_key_exists($alias, $this->aFieldsTable))
      return ExceptionManager::generateException('L\'alias est déjà affecté');

    $this->aFieldsTable[$alias] = array($instanceEntity, count($this->aFieldsTable), false);
    return $this;
  }

  /**
   * Retourne un tableau d'objet des entités de la requête
   * Cette fonction peut etre appelée que si la fonction bindField a été appeléé
   * @return bool|array
   */
  private function fetchField()
  {
    if (!($result = $this->fetch('row')))
      return false;

		$this->returnObject = [];
		foreach ($this->aFieldsTable as $alias => &$info) {
			$this->returnObject[$alias] = new $info[0]();
		}

    foreach ($this->aFieldsValue as &$data) {
      $fctname = "set".$data[0];
      if (isset($result[$data[4]])) {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->returnObject[$data[2]]->$fctname($result[$data[4]]);
      }
    }
    return $this->returnObject;
  }

  /**
   * Execution de la requête
   *
   * @return boolean
   */
  public function execute()
  {

    if (!$this->isConnected())
      return ExceptionManager::generateException('Pas connecté à la base de donnée');

    $query = $this->getQuery();
    if (DebugBar::isInit()) {
      DebugBar::ajouterMessage('<span style="margin-left:30px"></span>'.(DebugBar::getCountRequeste()+1).' - '.$query);
    }

    $debutExecution = microtime(true);
    /** @var mysqli $sql */
    $sql = Sql::$connect[$this->nom];
    $this->result = $sql->query($query);
    $finExecution = microtime(true);
    $tempsPasseExecution = $finExecution - $debutExecution;

    if (DebugBar::isInit()) {
      DebugBar::ajouterRequete($query, $tempsPasseExecution, Sql::$connect[$this->nom]->error);
    }

    if ($this->result === false) {
      $this->error = Sql::$connect[$this->nom]->errno . ': ' . Sql::$connect[$this->nom]->error;
      return ExceptionManager::generateException($this->error . ' | Requête : <br /><b>' . $this->getQuery() . '</b>');
    }

    if ($this->isResult()) {
      $nbField = $this->result->field_count;
      for ($i = 0; $i < $nbField; $i++) {
        $fieldInfo = $this->result->fetch_field_direct($i);
        if (isset($this->aFieldsValue[$fieldInfo->table . "." . $fieldInfo->orgname])) {
          $this->aFieldsValue[$fieldInfo->table . "." . $fieldInfo->orgname][4] = $i;
        }
      }
    }



    return true;
  }

  /**
   * @return string
   */
  public function getError()
  {
    return $this->error;
  }

  /**
   * Récuperation de tous les resultats de la requête
   * @param string $fetchMode
   * @return array|bool
   * @throws Exception
   */
  public function fetchAll($fetchMode = 'array')
  {
    if (!($this->isResult()))
      return ExceptionManager::generateException('Pas de résultat');

    $fetchall = array();
    while ($r = $this->fetch($fetchMode)) {
      $fetchall[] = $r;
    }

    return $fetchall;
  }

  /**
   * Retourne les résultats de la requête
   * @param string $fetchMode
   * @return array
   */
  public function fetch($fetchMode = 'array')
  {
    $fetch = array();
    if (!($this->isResult()))
      return ExceptionManager::generateException('Pas de résultat');

    switch ($fetchMode) {
      case 'newrow':
        $fetch = $this->result->fetch_row();
        break;
      case 'newarray':
        $fetch = $this->result->fetch_array();
        break;
      case 'row':
        $fetch = $this->result->fetch_row();
        break;
      case 'array':
        $fetch = $this->result->fetch_array();
        break;
      case 'assoc':
        $fetch = $this->result->fetch_assoc();
        break;
      case 'object':
        $fetch = $this->result->fetch_object();
        break;
      case 'field':
        $fetch = $this->fetchField();
        break;
    }

    return $fetch;
  }

  /**
   * Retourne le nombre de résultats de la requête
   *
   * @return integer
   */
  public function count()
  {
    if (!($this->isResult()))
      return ExceptionManager::generateException('Pas de résultat');

    return $this->result->num_rows;
  }

  /**
   * Retourne le nombre de ligne affectées par la requête
   *
   * @return integer
   */
  public function nombreLigneModifiee()
  {
    if (!$this->isConnected())
      return ExceptionManager::generateException('Pas connecté à la base de donnée');

    return Sql::$connect[$this->nom]->affected_rows;
  }

  /**
   * Reconnexion à Mysqli (pour éviter un timeout de 5 minutes par exemple)
   *
   */
  public function reconnect()
  {
    $this->close();
    $this->connection();
  }

  /**
   * Ferme la connexion
   *
   */
  public function close()
  {
    if ($this->isConnected()) {
      /** @var mysqli $sql */
      $sql = Sql::$connect[$this->nom];
      $sql->close();
      unset(Sql::$connect[$this->nom]);
    }

  }

  /**
   * Sécurisation des données
   * @param $value
   * @param bool|true $special
   * @return bool
   * @throws Exception
   */
  public function quote_smart($value, /** @noinspection PhpUnusedParameterInspection */
                              $special = true)
  {
    if (!$this->isConnected())
      return ExceptionManager::generateException('Pas connecté à la base de donnée');
    /** @var mysqli $sql */
    $sql = Sql::$connect[$this->nom];
    return $sql->real_escape_string($value);
  }

  /**
   * Connexion à la base de données
   *
   * @return boolean
   */
  private function connection()
  {
    Sql::$connect[$this->nom] = new mysqli($this->host, $this->user, $this->pass, $this->db);

    return $this->isConnected();
  }

  /**
   * Renvoi le mysqli_result
   * @return mysqli_result
   */
  public function getResult()
  {
    return $this->result;
  }

  /**
   * Retourne la configuration Sql en cours
   * @return array
   */
  public function getConfigConnexion()
  {
    return array(
      'host' => $this->host,
      'user' => $this->user,
      'pass' => $this->pass,
      'db' => $this->db
    );
  }

  /**
   * Retourne la date courante format SQL
   * @return bool|string
   */
  public static function getNow()
  {
    return date('Y-m-d H:i:s');
  }


}
