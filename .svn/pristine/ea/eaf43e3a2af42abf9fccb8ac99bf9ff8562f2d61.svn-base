<?php

namespace Harmony2\Datatable;

use Exception;
use Harmony2\Http\Request;
use Harmony2\Sql;

class DataRequest
{

	const variable = 'fds';
	const EVENT_EFFACER = 'evtEffacer';

	public $variable = '';
	/** @var Critere $critere [$key] */
	public $critere = array();
	/** @var Tri $tri  */
	public $tri = null;
	public $pagination = null;
	public $fields = array();
	private $nom = '';
	private $select = '';
	private $from = '';
	private $where = '';
	private $group = '';
	private $order = '';
	private $champs_par_colonne = 2;
	private $largeur_champs = 150;
	private $largeurrubrique = '';
	/** @var Sql $sql */
	private $sql;
	private $initialise = false;
	private $collection = array();

	public function setSql(Sql $sql){
		$this->sql = $sql;
	}

	public function getHTMLFiltre($template = null, $lesOptions = array())
	{
		return new ElementFilter($this, $template, $lesOptions);
	}

	public function getHTMLPagination($template = null, $lesOptions = array())
	{
		return new ElementPagination($this, $template, $lesOptions);
	}

	public function getHTMLTable($data = array(), $template = null, $options = array(), $type = 'object')
	{
		$options['__Pager'] = $this;
		$table = new ElementTable($data, $template, $options, $type);

		return $table;
	}

	public function hydrate(DataRequest $dataRequest)
	{
		//On set les variable de datarequest à partir de l'objet
		//$dataRequest->setNom($this->getNom());

		//Mise à jours de la requete
		//On ne fait la requete du nouvel objet fait fois

		//Mise à jours des filtres
		//On recupere les valeurs des filtres qui aurait été fait
		foreach ($this->critere as $nomcritere => $objetcritere) {
			if (!isset($dataRequest->critere[$nomcritere]))
				continue;
			/** @var Critere $critere */
			$critere = $dataRequest->critere[$nomcritere];
			if($critere->emptyValeur())
				$critere->setValeur([]);
			if ($critere instanceof Critere)
				//if($critere instanceof Critere && !$critere->emptyValeur())
				$this->setValeurCritere($nomcritere, $dataRequest->getValeurCritere($nomcritere));
		}

		//Mise à jours des tries
		if (
			($dataRequest->tri != null)
			&& ($dataRequest->tri instanceof Tri)
			&& ($this->tri != null)
			&& ($this->tri instanceof Tri)
		) {
			foreach ($dataRequest->tri->getLesTris() as $idtri => $valeur) {

				if ($this->tri->est($idtri)) {
					$this->tri->ajouter($idtri, $valeur);
				}
			}
			$tri = $dataRequest->tri->recuperer();

			if (count($tri) == 3)
				$this->tri->definir($dataRequest->tri->recuperer()[2], $dataRequest->tri->recuperer()[1], true);
		}

		//Mise à jours de la pagination
		if (
			($dataRequest->pagination != null)
			&& ($dataRequest->pagination instanceof Pagination)
			&& ($this->pagination != null)
			&& ($this->pagination instanceof Pagination)
		) {
			$this
				->pagination
				->setInfoPage(
					$dataRequest->pagination->getInfoPage()[0],
					$dataRequest->pagination->getInfoPage()[1],
					$dataRequest->pagination->getInfoPage()[2]
				);
		}

		return $this;
	}


	/**
	 * DataRequest constructor.
	 * @param string $nomfiltre
	 * @param Sql $sql
	 */
	private function __construct(string $nomfiltre , Sql $sql)
	{

		$this->sql = $sql;
		$this->setNom($nomfiltre);
		$this->variable = self::variable;
		$this->largeurrubrique = '100%';
		$this->tri = new Tri();
		$this->pagination = new Pagination();
		$this->collection = array();
		$this->init();
	}

	public function init(){
		
	}

	public function isInit()
	{
		return ($this->initialise == true);
	}

	public function setInit($init = false)
	{
		$this->initialise = (bool)$init;
	}

	public function disablePagination()
	{
		$this->pagination = null;

		return $this;
	}

	public function enablePagination()
	{
		$this->pagination = new Pagination();

		return $this;
	}



	public static function setInstance($name, DataRequest $datarequest, Request $request)
	{
		$request->setSession('lesfiltres_' . $name, $datarequest);
	}

	/**
	 * @param	string $name
	 * @param Sql $sql
	 * @param Request $request
	 * @return DataRequest|null
	 */
	public static function getSession(string $name, Sql $sql, Request $request){

		if(self::getEvent($request) == self::EVENT_EFFACER){
			$request->unsetSession('lesfiltres_' . $name);
			return null;
		}
		/**	@var DataRequest $instance */
		$instance = $request->session('lesfiltres_' . $name);
		if(!empty($instance))
			$instance->setSql($sql);
		return $instance;
	}

	/**
	 * @param	string $name
	 * @param Sql $sql
	 * @param Request $request
	 * @param bool $new
	 * @return DataRequest
	 */
	public static function getInstance(string $name, Sql $sql, Request $request, $new = false)
	{

		$className = get_called_class();
		/** @var DataRequest $instance */
		$instance = new $className($name, $sql);
		if (($new == false)
			and (self::getEvent($request) != DataRequest::EVENT_EFFACER)
			and ($request->issetSession('lesfiltres_' . $name))
			and ($request->session('lesfiltres_' . $name) instanceof DataRequest)
		) {
			/** @var DataRequest $oldinstance */
			$oldinstance = $request->session('lesfiltres_' . $name);
			$instance->hydrate($oldinstance);
		}
		$request->setSession('lesfiltres_' . $name, $instance);
		return $request->session('lesfiltres_' . $name);
	}

	public static function erase(Request $request)
	{
		$allNameSession = $request->getAllNameSession();
		foreach ($allNameSession as $name)
		{
			if(substr($name, 0, strlen('lesfiltres_')) == 'lesfiltres_'){
				$request->unsetSession($name);
			}
		}
	}

	public static function getName()
	{
		return get_called_class();
	}

	protected static  function getEvent(Request $request)
	{
		if (!empty($request->request(self::variable)))
			return $request->request(self::variable);
		else {
			return false;
		}
	}

	public function raffraichir(Request $request)
	{

		switch (self::getEvent($request)) {
			case 'filtrer':
				if (null !== $this->pagination)
					$this->pagination->resetPageCourante();
				$this->setValeursCriteres($request->getRequestTable());
				break;
		}

		if (($this->tri != null) AND ($this->tri instanceof Tri)) {
			$this->tri->raffraichir($request);
		}

		if (($this->pagination != null) AND ($this->pagination instanceof Pagination)) {
			$this->pagination->raffraichir($request);
		}
	}

	public function setRequete($select, $from, $where = '', $orderby = '', $groupby = '')
	{
		$this->select = $select;
		$this->from = $from;
		$this->where = $where;
		$this->group = $groupby;
		$this->order = $orderby;
	}

	function setLargeurRubrique($valeur)
	{
		$this->largeurrubrique = $valeur;
	}

	public function setLargeurChamps($valeur)
	{
		$this->largeur_champs = $valeur;
	}

	public function setChampsParColonne($valeur)
	{
		$this->champs_par_colonne = $valeur;
	}

	public function getNom()
	{
		return $this->nom;
	}

	/**
	 * @param $nom
	 * @return $this
	 */
	public function setNom($nom)
	{
		$this->nom = $nom;
		return $this;
	}

	/**
	 * @param $nom
	 * @param $champs
	 * @param string $option
	 * @param string $requeteliste
	 * @return Critere
	 */
	public function ajouterCritere($nom, $champs, $option = '', $requeteliste = '')
	{

		$this->critere[$nom] = new Critere($champs, $option, $requeteliste);
		return $this->critere[$nom];
	}

	public function setValeurCritere($nom, $valeur)
	{

		if (isset($this->critere[$nom])) {
			/** @var Critere $critere */
			$critere = $this->critere[$nom];
			if (is_array($valeur))
				$critere->setValeur($valeur);
			else
				$critere->setValeur([$valeur]);
		}
	}

	public function setHavingCritere($nom, $valeur = true)
	{
		if (isset($this->critere[$nom])) {
			/** @var Critere $critere */
			$critere = $this->critere[$nom];
			$critere->setHaving((bool)$valeur);
		}
	}

	public function setTypeConditionCritere($nom, $valeur)
	{
		if (isset($this->critere[$nom])) {
			/** @var Critere $critere */
			$critere = $this->critere[$nom];
			$critere->setTypeWhere($valeur);
		}
	}

	//deprecated
	public function setTypeWhereCritere($nom, $valeur)
	{
		$this->setTypeConditionCritere($nom, $valeur);
	}

	public function setValeursCriteres($tableau)
	{
		foreach ($this->critere as $nomcritere => $objetcritere) {
			/** @var $objetcritere Critere */
			if (isset($tableau[$nomcritere]) and (is_array($tableau[$nomcritere]))) {
				$objetcritere->setValeur($tableau[$nomcritere]);
			} else {
				if (!isset($tableau[$nomcritere]) or ($tableau[$nomcritere] == ''))
					$objetcritere->setValeur([]);
				else {
					$objetcritere->setValeur([$tableau[$nomcritere]]);
				}
			}
		}
	}


	public function getValeurCritere($nom)
	{
		if (isset($this->critere[$nom])) {
			/** @var Critere $critere */
			$critere = $this->critere[$nom];
			return $critere->getValeur();
		}
		return false;
	}


	public function getCritere()
	{
		return $this->critere;
	}

	/**
	 * @param string $date
	 * @param string $format
	 * @param string $formatReturn
	 * @return bool|string
	 */
	private function dateFormat($date, $format = 'Y-m-d', $formatReturn = 'd/m/Y')
	{
		$datetime = \DateTime::createFromFormat($format, $date);
		if (($datetime instanceof \DateTime) and is_string($return = \date_format($datetime, $formatReturn)))
			return $return;
		else
			return '';
	}

	/**
	 * @param $value Critere
	 * @return string
	 */
	private function getCondition($value)
	{

		$sql = new sql(1);
		$retour = "( ";
		$loopOr = 0;
		foreach ($value->getValeur() as $valeurCritere) {
			if ($loopOr++ > 0)
				$retour .= ' OR ';
			switch ($value->getTypeWhere()) {
				case 11:
					$retour .= sprintf('%s & %d', (string)$value->getChamps(), (int)$valeurCritere);
					break;
				case 10:
					list($bits, $result) = explode(':', $valeurCritere);
					$retour .= sprintf('(%s & %d) = %d', (string)$value->getChamps(), (int)$bits, (int)$result);
					break;
				case 9:
					$date = implode('-', array_reverse(explode('/', $sql->quote_smart($valeurCritere))));
					$retour .= str_replace('%s', "'$date'", $value->getChamps());
					break;
				case 6:
					$retour .= $value->getChamps();
					break;
				case 5:
					if ($value->getOption() === 'DATE_FR') {
						$date = implode('-', array_reverse(explode('/', $sql->quote_smart($valeurCritere))));
						$retour .= sprintf('DATEDIFF( %s, \'%s\' ) >= 0', $value->getChamps(), $date);
					} else {
						$retour .= $value->getChamps() . " >= '" . $sql->quote_smart($valeurCritere) . "'";
					}
					break;
				case 4:
					if ($value->getOption() === 'DATE_FR') {
						$date = implode('-', array_reverse(explode('/', $sql->quote_smart($valeurCritere))));
						$retour .= sprintf('DATEDIFF( %s, \'%s\' ) <= 0', $value->getChamps(), $date);
					} else {
						$retour .= $value->getChamps() . " <= '" . $sql->quote_smart($valeurCritere) . "'";
					}
					break;
				case 3:
					$retour .= $value->getChamps() . " LIKE '" . $sql->quote_smart($valeurCritere) . "%'";
					break;
				case 2:
					$retour .= $value->getChamps() . " " . $valeurCritere;
					break;
				case 1:
					if (in_array($value->getOption(), ['DATE', 'DATEPICKER', 'DATE_FR'])) {
						$retour .= $value->getChamps() . " = '" . $this->dateFormat($sql->quote_smart($valeurCritere), LOCALE_DATE_FORMAT, 'Y-m-d') . "'";
					} else {
						$retour .= $value->getChamps() . " = '" . $sql->quote_smart($valeurCritere) . "'";
					}
					break;
				case 14:
					if (in_array($value->getOption(), ['DATE', 'DATEPICKER', 'DATE_FR']))
						$retour .= $value->getChamps() . " > '" . $this->dateFormat($sql->quote_smart($valeurCritere), LOCALE_DATE_FORMAT, 'Y-m-d') . "'";
					else
						$retour .= $value->getChamps() . " > '" . $sql->quote_smart($valeurCritere) . "'";
					break;
				case 15:
					if (in_array($value->getOption(), ['DATE', 'DATEPICKER', 'DATE_FR']))
						$retour .= $value->getChamps() . " < '" . $this->dateFormat($sql->quote_smart($valeurCritere), LOCALE_DATE_FORMAT, 'Y-m-d') . "'";
					else
						$retour .= $value->getChamps() . " < '" . $sql->quote_smart($valeurCritere) . "'";
					break;
				case 7:
					$retour .= $value->getChamps() . " REGEXP '" . $sql->quote_smart($valeurCritere) . "'";
					break;
				case 8:
					if ($valeurCritere == 1)
						$retour .= $value->getChamps() . " IS NOT NULL ";
					else
						$retour .= $value->getChamps() . " IS NULL ";
					break;
				case 12:
					$retour .= str_replace('#PARAM#', $valeurCritere, $value->getChamps());
					break;
				case 13:
					$retour .= "date(" . $value->getChamps() . ") = '" . $sql->quote_smart(\DateTime::createFromFormat(LOCALE_DATE_FORMAT, $valeurCritere)->format('Y-m-d')) . "'";
					break;
				case 0:
				default:
					$retour .= $value->getChamps() . " LIKE '%" . $sql->quote_smart($valeurCritere) . "%'";
					break;
			}

		}
		if ($retour == "( ")
			return '';
		$retour .= ")";

		return $retour;

	}

	/**
	 * @param $sql Sql
	 */
	public function bindRequest($sql)
	{
		if (count($this->fields) > 0) {
			foreach ($this->fields as $key => $value) {
				if (!is_array($value)) {
					$sql->bindField($key, $value);
				} else {
					if ($value['instance']) {
						$sql->bindInstanceField($key, $value['table']);
					} else {
						$sql->bindField($key, $value['table'], $value['prefix']);
					}
				}
			}
		}
	}

	public function getFrom()
	{
		return " FROM " . $this->from;
	}

	/**
	 * @param $where string
	 * @return $this
	 */
	public function setWhere($where)
	{
		$this->where = $where;
		return $this;
	}

	public function getWhere()
	{

		$requete = '';
		if (!empty($this->where)) {
			$nbcond = 1;
			$requete .= " WHERE " . $this->where;
		} else {
			$nbcond = 0;
		}
		foreach ($this->critere as $value) {
			/**
			 * @var $value Critere
			 */
			if ($value->getHaving())
				continue;
			if (!$value->emptyValeur()) {
				if ($nbcond == 0) {
					$nbcond = 1;
					$requete .= " WHERE ";
				} else {
					$requete .= " AND ";
				}

				$requete .= $this->getCondition($value);
			}
		}
		return $requete;
	}

	private function getDetailRequest()
	{
		$requete = $this->getWhere();

		if (!empty($this->group))
			$requete .= ' GROUP BY ' . $this->group;

		$nbcond = 0;
		foreach ($this->critere as $value) {
			/**
			 * @var $value Critere
			 */
			if ($value->getHaving() !== true)
				continue;
			$valeur = $value->getValeur();
			if (isset($valeur) && $valeur != '') {
				if ($nbcond == 0) {
					$nbcond = 1;
					$requete .= " HAVING ";
				} else {
					$requete .= " AND ";
				}

				$requete .= $this->getCondition($value);
			}
		}

		if (!empty($this->order))
			$requete .= ' ORDER BY ' . $this->order;

		if ($this->tri->existe()) {
			list($champ, $ordre) = $this->tri->recuperer();

			$requete .= empty($this->order) ? ' ORDER BY ' : ', ';
			$requete .= ' ' . $champ . ($ordre == 1 ? ' ASC' : ' DESC');
		}
		return $requete;
	}

	/**
	 * renvoi les param de l'url qui permet de modifier le tri sur ce champs
	 * @param string $id nom du champs
	 * @return string
	 */
	public function getParamTriUrl($id)
	{
		if (!$this->tri->est($id))
			return false;
		return [Tri::_EVT => Tri::_EVT_TRI, "npptri" => $id, "data_request" => $this->getNom()];
	}

	public function getParamPaginationUrl($num_page)
	{
		return [Pagination::_NAME_PARAM_EVT => Pagination::_PAGENO_EVT, Pagination::_EVT_PAGINATION => $num_page, "data_request" => $this->getNom()];
	}

	public function getParamCountPaginationUrl($nb)
	{
		return [Pagination::_NAME_PARAM_EVT => Pagination::_SET_PAGINATION, Pagination::_SET_PAGINATION_COUNT => $nb, "data_request" => $this->getNom()];
	}

	public function getParamCancelFilter()
	{
		return [self::variable => self::EVENT_EFFACER, "data_request" => $this->getNom()];
	}

	public function getParamValidFilter()
	{
		return [self::variable => $this->getFilterFormURL_Filter(), "data_request" => $this->getNom()];
	}



	public function initQuery()
	{

		$this->sql->reset();
		$requete = "SELECT " . $this->select;
		$requete .= $this->getFrom();

		$requete .= $this->getDetailRequest();

		$this->sql->setQuery($requete);
		if (count($this->fields) > 0) {
			foreach ($this->fields as $key => $value) {
				if (!is_array($value)) {
					$this->sql->bindField($key, $value);
				} else {
					if ($value['instance']) {
						$this->sql->bindInstanceField($key, $value['table']);
					} else {
						$this->sql->bindField($key, $value['table'], $value['prefix']);
					}
				}
			}
		}


	}

	public function addPaginationQuery()
	{
		if (!is_null($this->pagination)) {
			$requete = $this->sql->getQuery();
			$this->pagination->pagination($requete, $this->sql);
			$requete = $this->pagination->getRequetePagination();
			$this->sql->setQuery($requete);
		}
	}

	/**
	 * Permet d'obtenir la requete du request
	 * Avant d'appeller cette méthode il faut initialiser la requete avec initQuery et si besoin ajouter la pagination avec addPaginationQuery
	 * @return mixed|string
	 */
	public function getQuery()
	{
		return $this->sql->getQuery();
	}

	public function bindField($alias, $table = '', $basePrefix = FALSE)
	{
		$this->fields[$alias] = ['table' => $table, 'prefix' => $basePrefix, 'instance' => false];
	}

	public function bindInstanceField($alias, $table = '', $basePrefix = FALSE)
	{
		$this->fields[$alias] = ['table' => $table, 'prefix' => $basePrefix, 'instance' => true];
	}

	/**
	 * @param string $type
	 * @return array
	 * @throws Exception
	 */
	public function execute($type = 'assoc')
	{

		$this->initQuery();
		$this->addPaginationQuery();

		if (!$this->sql->execute())
			throw new Exception('Erreur SQL : ' . $this->sql->getError() . 'Requete : ' . $this->sql->getQuery());

		try {
			$result = $this->sql->fetchAll($type);
		} catch (Exception $e) {
			return array();
		}
		if ($result === false)
			return array();

		return $result;
	}


	public function getCollection()
	{
		return $this->collection;
	}

	public function supprimerCritere($nom)
	{
		unset($this->critere[$nom]);
	}

	public function viderCriteres()
	{
		$this->reset();
	}

	public function reset()
	{
		$this->setValeursCriteres([]);
		$this->tri = new Tri();
		$this->pagination = new Pagination();
		$this->collection = array();
	}

/**
	public function getFilterFormURL()
	{
		$url = parse_url($_SERVER['REQUEST_URI']);

		return $url['path'];
	}

	public function getFilterFormURL_Event()
	{
		return $this->variable;
	}



	public function getFilterFormURL_Reset()
	{
		$url = parse_url($_SERVER['REQUEST_URI']);

		return $url['path'] . '?' . $this->getFilterFormURL_Event() . '=' . DataRequest::EVENT_EFFACER;
	}*/


	public function getFilterFormURL_Filter()
	{
		return 'filtrer';
	}

	public function generateElementForm($nomcritere, Critere $objetcritere)
	{
		switch ($objetcritere->getOption()) {
			case 'LISTBOX' :
				$champs = $this->generateListbox($nomcritere, $objetcritere);
				break;
			case 'DATE' :
			case 'DATEPICKER' :
			case 'DATE_FR':
				$champs = $this->generateDatePicker($nomcritere, $objetcritere, $this->largeur_champs);
				break;
			default :
				$champs = $this->generateText($nomcritere, $objetcritere);
		}
		return $champs;
	}

	/**
	 * @param $nomcritere
	 * @param Critere $objetcritere
	 * @return string
	 */
	private function generateListbox($nomcritere, Critere $objetcritere)
	{
		$input = '<select class="form-control select2" multiple="multiple"  id="' . $nomcritere . '" name="' . $nomcritere . '[]" data-placeholder="Choisissez">';
		foreach ($objetcritere->getRequeteliste() as $ligne) {
			if (count($objetcritere->getValeur()) > 0) {
				$input .= '<option value="' . $ligne["value"] . '" ' . (in_array($ligne['value'], $objetcritere->getValeur()) ? 'selected="selected"' : '') . '>' . $ligne["texte"] . '</option>';
			} else {
				$input .= '<option value="' . $ligne["value"] . '" >' . $ligne["texte"] . '</option>';
			}
		}
		$input .= '</select>';

		return $input;
	}

	private function generateText($nomcritere, Critere $objetcritere)
	{
		return '<div data-tags-input-name="' . $nomcritere . '" class="tagBox" id="' . $nomcritere . '" name="' . $nomcritere . '">' . (!empty($objetcritere->getValeur()) ? implode('|#', $objetcritere->getValeur()) : '') . '</div>';
	}

	private function generateDatePicker($nomcritere, Critere $objetcritere, $width)
	{
		$input = '<div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right datepicker_filtre"  style="' . ($width != '' ? 'witdh:' . $width : '') . '" id="' . $nomcritere . '" name="' . $nomcritere . '" value="' . (count($objetcritere->getValeur())>0?htmlentities($objetcritere->getValeur()[0], ENT_QUOTES, 'UTF-8'):'') . '">
                </div>        ';


		/*        $input  = '<div class="input-append date_filter">';
						$input .= '<input type="text" class="datepicker form-control" id="'.$nomcritere.'" name="'.$nomcritere.'" data-date-format="dd/mm/yyyy" value="' . htmlentities($objetcritere->getValeur(), ENT_QUOTES, 'UTF-8') . '">';
						$input .= '<span class="add-on hack_second"><i class="icon icon-calendar"></i></span>';
						$input .= '</div>';*/
		return $input;
	}
}
