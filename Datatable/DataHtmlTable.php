<?php
/**
 * Created by PhpStorm.
 * User: yannick
 * Date: 24/05/2016
 * Time: 09:50
 */

namespace Harmony2\Datatable;

class DataHtmlTable
{

  private $dataRequest;

  private $collection = array();

  private $lesColonnes = array();

  private $lesFiltres = array();

	/**
	 * DataHtmlTable constructor.
	 * @param $dataRequest
	 * @param $collection
	 * @throws \Exception
	 */
  public function __construct($dataRequest, $collection) {
    if (!($dataRequest instanceof DataRequest)) {
      throw new \Exception('Not instance of DataRequest');
    }
    if (!is_array($collection)) {
      throw new \Exception('Not is array');
    }
    $this->dataRequest = $dataRequest;
    $this->collection = $collection;
  }

  public function getCollection() {
    return $this->collection;
  }

  public function getDataRequest() {
    return $this->dataRequest;
  }

  public function setColonne($identifiant, $libelle, $tri = false, $func = false, $specific_class=false) {

    $this->lesColonnes[$identifiant] = [
      'identifiant' => $identifiant,
      'libelle' => $libelle,
    ];

    if(is_bool($tri)) {
      $this->lesColonnes[$identifiant] = [
        'identifiant' => $identifiant,
        'libelle' => $libelle,
        'tri' => $tri,
        'func' => $func,
        'specific_class' => $specific_class,
      ];
    } elseif(is_array($tri)) {
      //tableau option
      $this->lesColonnes[$identifiant] = [
        'identifiant' => $identifiant,
        'libelle' => $libelle,
        'func' => $func,
        'specific_class' => $specific_class,
      ];
      $this->lesColonnes[$identifiant] = array_merge($this->lesColonnes[$identifiant], $tri);
    } else {
      throw new \Exception('Tri not a boolean');
    }





  }

  public function getLesColonnes() {
    return $this->lesColonnes;
  }

  public function setFiltre($identifiant, $libelle) {
    $this->lesFiltres[$identifiant] = [
      'identifiant' => $identifiant,
      'libelle' => $libelle
    ];
  }

  public function getLesFiltres() {
    return $this->lesFiltres;
  }

  public function getValue($identifiant, $data) {
    if (isset($this->lesColonnes[$identifiant]['func']) && $this->lesColonnes[$identifiant]['func'] !== false) {
      return $this->lesColonnes[$identifiant]['func']($data);
    } else {
      return $data[$identifiant];
    }
  }
}