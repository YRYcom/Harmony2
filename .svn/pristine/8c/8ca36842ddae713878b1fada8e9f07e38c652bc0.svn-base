<?php

namespace Harmony2;


use Harmony2\Http\Request;
use Harmony2\Http\Response;


/**
 * Gestion des templates
 */
class Template
{
	/**
	 * @var $actionMsg ActionMsg
	 */
	public $actionMsg;
	/**
	 * @var $response Response
	 */
	protected $response;
	/**
	 * @var $datatpl array
	 */
	protected $fichiers;
	/**
	 * @var $datatpl array
	 */
	private $datatpl;
	/**
	 * @var $nameSection boolean
	 */
	private $nameSection;
	/**
	 * @var $page string
	 */
	private $pageBuffer;
	/**
	 * @var $sectionBuffer array;
	 */
	private $sectionBuffer;
	/**
	 * @var $addSection bool
	 */
	private $addSection;

	/**
	 * Initialisation des données
	 * @param Response|null $response
	 */
	public function __construct(Response $response = null)
	{
		$this->nameSection = false;
		$this->pageBuffer = '';
		$this->sectionBuffer = array();
		$this->actionMsg = new ActionMsg();
		$this->response = $response;
		$this->fichiers = array();
		$this->datatpl = array();
	}

	public function setResponse(Response $response)
	{
		$this->response = $response;
	}


	/**
	 * Retourne l'instance des messages d'action
	 *
	 * @return \Harmony2\ActionMsg
	 */
	public function getActionMsg()
	{
		return $this->actionMsg;
	}

	/**
	 * Enregistre les fichiers à parser grâce à un système de clé => valeur
	 *
	 * @param array $fichiers les fichiers à parser
	 * @return bool
	 */
	public function set_filenames($fichiers)
	{
		if (!is_array($fichiers)) {
			try {
				ExceptionManager::generateException('Le parametre $fichiers n\'est pas un array');
			} catch (\Exception $e) {
				var_dump($e);
			}
		}

		foreach ($fichiers as $cle => $valeur) {
			$this->set_filename($valeur, $cle);
		}

		return true;
	}

	public function set_filename($fichier, $cle = false)
	{
		$this->fichiers[($cle ? $cle : uniqid())] = $fichier;
		return true;
	}

  /**
   * @param $valeur
   * @return bool
   */
  public function use_filename($valeur)
  {
    $fichier = PATH_TEMPLATE . $valeur;
    return in_array($fichier, $this->fichiers);
  }

	private function encodeHTMLValue($key, $value)
	{
		if (substr($key, 0, 5) != 'nosec') {
			return $value;
		}
		if (is_array($value)) {
			while (list($k, $v) = each($value)) {
				$value[$k] = $this->encodeHTMLValue($k, $v);
			}
		} elseif (is_string($value)) {
			return htmlentities($value, ENT_QUOTES | ENT_HTML401, 'UTF-8');
		}

		return $value;
	}

	private function decodeHTMLValue($value)
	{
		if (is_array($value)) {
			while (list($key, $val) = each($value)) {
				$value[$key] = $this->decodeHTMLValue($val);
			}
		} elseif (is_string($value)) {
			return html_entity_decode($value, ENT_QUOTES | ENT_HTML401, 'UTF-8');
		}

		return $value;
	}

  /**
   * Parse les fichiers TPL
   *
   * @param string $fichier Les fichiers à parser
   */
	public function parse($fichier = '')
	{

		if ($fichier == '' && count($this->fichiers) > 0) {
			$fichier = array_keys($this->fichiers)[0];
		}
		Entity::enableParse();
		if (is_array($this->datatpl)) {
			/** @noinspection  PhpUnusedLocalVariableInspection */
			while (list($k, $v) = each($this->datatpl)) {
				$this->datatpl[$k] = $this->encodeHTMLValue($k, $this->datatpl[$k]);
				@reset($this->datatpl[$k]);
			}
			@reset($this->datatpl);
		}

		if (!empty($this->fichiers[$fichier])) {

			ob_start();

			$this->includefile($this->fichiers[$fichier]);
		}
		Entity::disableParse();
	}

	/**
	 * Récupère la valeur d'une donnée enregistrée
	 *
	 * @param string $nom Le nom de la donnée que l'on souhaite
	 * @param boolean $displayHtml
	 * @return mixed La valeur de la donnée ou NULL si inexistante
	 */
	public function get($nom, $displayHtml = false)
	{
		if (array_key_exists($nom, $this->datatpl)) {
			if (true === $displayHtml)
				return $this->decodeHTMLValue($this->datatpl[$nom]);
			else
				return $this->datatpl[$nom];
		}

		return null;
	}

	/**
	 * Ajoute une nouvelle donnée avec sa valeur
	 *
	 * @param string $nom Le nom de la donnée
	 * @param mixed $valeur La valeur de la donnée
	 * @return mixed
	 */
	public function set($nom, $valeur)
	{
		return $this->datatpl[$nom] = $valeur;
	}

	/**
	 * Merge le tableau passé en paramètre avec les données existantes
	 *
	 * @param array $tabvaleur Les données à merge
	 * @return array Les données
	 */
	public function merge($tabvaleur)
	{
		if (is_array($tabvaleur))
			return $this->datatpl = array_merge($this->datatpl, $tabvaleur);
		else
			return $this->datatpl;
	}

	/**
	 * Récupère la valeur d'une donnée dans un tableau
	 *
	 * @param string $nom Le nom de la donnée
	 * @param mixed $key La clé que l'on recherche dans le tableau
	 * @return mixed Soit la valeur que l'on recherche soit false
	 */
	public function gettable($nom, $key)
	{
		if ((is_array($this->datatpl[$nom])) && (array_key_exists($key, $this->datatpl[$nom]))) {
			return $this->datatpl[$nom][$key];
		} else {
			return false;
		}
	}

	/**
	 * Savoir si une donnée existe ou pas
	 *
	 * @param string $nom Le nom de la donnée que l'on recherche
	 * @return boolean
	 */
	public function isdefined($nom)
	{
		if ((array_key_exists($nom, $this->datatpl))) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Permet de savoir si la valeur d'une donnée est vide
	 *
	 * @param string $nom Le nom de la donnée
	 * @return boolean
	 */
	public function isempty($nom)
	{
		if ((array_key_exists($nom, $this->datatpl)) && (empty($this->datatpl[$nom]))) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Inclut un fichier TPL dans le fichier TPL courant
	 *
	 * @param string $filename
	 * @return bool
	 */
	public function includefile($filename)
	{
		if(substr($filename,0,1) != '/')
			$filename = '/'.$filename;
		$file = PATH_TEMPLATE . $filename;
		if (empty($file) or !file_exists($file) or !is_readable($file)) {
			return ExceptionManager::generateException('Erreur dans le template : fichier ' . $file . ' est introuvable.');
		}
		TemplateFactory::call($filename, $this);
		/** @noinspection PhpIncludeInspection */
		include($file);

		return true;
	}

	public function startSection($nameSection)
	{
		$this->beginSection($nameSection, false);
	}

	public function addSection($nameSection)
	{
		$this->beginSection($nameSection, true);
	}

	public function beginSection($nameSection, $mode)
	{
		if ($this->nameSection !== false)
			throw new \Exception("Section is started", 500);
		if ($nameSection == '')
			throw new \Exception("nameSection is null", 500);
		$this->nameSection = $nameSection;
		$this->addSection = $mode;
		$this->pageBuffer = ob_get_contents();
		ob_clean();
	}

	public function endSection()
	{
		if ($this->nameSection === false)
			throw new \Exception("Section is not started", 500);
		if ($this->addSection == true && isset($this->sectionBuffer[$this->nameSection])) {
			$this->sectionBuffer[$this->nameSection] = $this->sectionBuffer[$this->nameSection] . ob_get_contents();
		} else {
			$this->sectionBuffer[$this->nameSection] = ob_get_contents();
		}
		$this->nameSection = false;
		ob_clean();
		echo $this->pageBuffer;
		$this->pageBuffer = '';
	}

	public function existSection($nameSection)
	{
		return isset($this->sectionBuffer[$nameSection]);
	}

	public function getSection($nameSection)
	{
		if ($this->existSection($nameSection)) {
			echo $this->sectionBuffer[$nameSection];
		}
	}
}
