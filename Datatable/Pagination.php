<?php

namespace Harmony2\Datatable;

use Exception;
use Harmony2\Http\Request;
use Harmony2\Template;
use Harmony2\Sql;

/**
 * Permet de gérer la pagination dans une liste de résultat
 * @name Pagination
 *
 */
class Pagination
{

	/**
	 * Contient l'ensemble des valeurs qui seront merger au valeur du template de retour
	 *
	 * @var array
	 */
	private $datatpl;

	/**
	 * Nombre de page total, il est calculé en fonction du nbre de résultat par page
	 *
	 * @var int
	 */
	private $paginationnbpage;

	/**
	 * Nbre d'élément par page
	 *
	 * @var int
	 */
	public $nbrepagePagination = 0;

	/**
	 * Numéro de la page courante ou en cours de consultation
	 *
	 * @var int
	 */
	private $pageCourante = 1;

	/**
	 * Nom du paramètre evènement passé dans l'url
	 *
	 * @var string
	 */
	const _NAME_PARAM_EVT = 'pgntevt';

	/**
	 * Valeur de l'evenement passé dans l'url qui permet de changer de numéro de page
	 *
	 * @var string
	 */
	const _PAGENO_EVT = 'pageno';

	const _EVT_PAGINATION = 'npp';

	/**
	 * Valeur de l'evenement dans l'url qui permet de modifier le nombre d'élément par page
	 *
	 * @var string
	 */
	const _SET_PAGINATION = 'pgnt';
	const _SET_PAGINATION_COUNT = 'nppnbre';

	public function resetPageCourante()
	{
		$this->pageCourante = 1;

		return $this;
	}

	/**
	 * Cette méthode mets à jour l'instance en fonction
	 * des évenements qu'elle va recevoir
	 *
	 * @param Request $request
	 * @throws evtsetpagination_Erreur_Pagination
	 * @return bool
	 */
	public function raffraichir(Request $request)
	{
		if (!$request->issetRequest(self::_NAME_PARAM_EVT))
			return false;
		switch ($request->request(self::_NAME_PARAM_EVT)) {
			case self::_PAGENO_EVT :
				if ($request->issetRequest(self::_EVT_PAGINATION) and ($request->request(self::_EVT_PAGINATION) != '')) {
					$this->pageCourante = (int)$request->request(self::_EVT_PAGINATION);
				} else {
					$this->pageCourante = 1;
				}
				break;
			case self::_SET_PAGINATION :
				$nbre = (int)$request->request(self::_SET_PAGINATION_COUNT);
				if ($nbre <= 0) throw new evtsetpagination_Erreur_Pagination();
				$this->nbrepagePagination = $nbre;
				break;
		}
		return true;
	}

	public function setInfoPage($pageCourante, $paginationnbpage, $nbrepagePagination)
	{
		$this->pageCourante = $pageCourante;
		$this->paginationnbpage = $paginationnbpage;
		$this->nbrepagePagination = $nbrepagePagination;
	}

	public function getInfoPage()
	{
		return [$this->pageCourante, $this->paginationnbpage, $this->nbrepagePagination];
	}

	/**
	 * Cration de l'instance
	 *
	 */
	public function __construct()
	{
		$this->datatpl = array();
		$this->pageCourante = 1;
		$this->paginationnbpage = 0;
		$this->nbrepagePagination = 10;
	}

	/**
	 * url qui permet de modifier le nbre d'élément par page
	 *
	 * @param int $valeur nbre d'élément par page
	 * @return string url qui permet d'activer l'evenement
	 */
	public function setUrlPagination($valeur)
	{
		$valeur = (int)$valeur;
		if ($valeur <= 0) return false;

		$url = "?" . self::_NAME_PARAM_EVT . "=" . self::_SET_PAGINATION . "&nppnbre=" . $valeur;

		return $url;
	}

	/**
	 * @return array
	 */
	public function getData()
	{
		return $this->datatpl['pagination'];
	}

	/**
	 * Génére le code HTML à partir d'un template qui va permettre de page les éléments d'une liste
	 *
	 * @param string $nom_template nom du fichier template utilisé
	 * @return void
	 */
	public function getPagination($nom_template = '')
	{
		$template = new Template();
		$template->merge($this->datatpl['pagination']);
		if ($nom_template == '') {
			$template->set_filenames(array(
				'pagination' => 'defaut/pagination.tpl',
			));
		} else {
			$template->set_filenames(array(
				'pagination' => $nom_template,
			));
		}
		$template->parse('pagination');
	}

	/**
	 * Nombre total de page
	 *
	 * @return int
	 */
	public function paginationCount()
	{
		return $this->paginationnbpage;
	}

	/**
	 * Calcul les éléments nécessaire à la pagination à partir de la requete sql
	 *
	 * @param string $requete requete sql qui va être paginée
	 * @param Sql $sql
	 * @throws calcul_Erreur_Pagination Se déclenche lors de la pagination de la requête
	 * @throws Exception
	 * @return void
	 */
	public function pagination($requete, $sql)
	{
		$n = $this->nbrepagePagination;

		if (!($sql instanceof Sql))
			throw new Exception('Sql name config not defined');


		$variable_get = self::_EVT_PAGINATION;
		$this->datatpl['pagination']['VARIABLEGET'] = $variable_get;

		/**
		$sql->setQuery('SELECT SQL_CALC_FOUND_ROWS ' . substr(trim($requete), 6) . ' LIMIT 0');
		$sql->execute();
		$sql->setQuery("SELECT FOUND_ROWS()");
		$sql->execute();
		$this->paginationnbpage = $sql->fetch('row')[0];
**/
		$this->paginationnbpage = 0;
		//  NBRE_PAR_PAGE

		$page = $this->pageCourante;

		/*
				$pages = ceil($this->paginationnbpage / $n);
				if (($page > $pages) or ($page < 1))
					$page = 1;

				if ($pages == 0) $pages = 1;

				if ($pages <= 11) {
					$page_start = 1;
				} elseif ($pages - $page < 5) {
					$page_start = $pages - 10;
				} elseif ($pages > 11 && $page > 6) {
					$page_start = $page - 5;
				} else {
					$page_start = 1;
				}


				$this->datatpl['pagination']['PAGES'] = array();
				for ($x = 0; $x < 11 && $x < $pages; $x++) {
					$pageno = $x + $page_start;
					$this->datatpl['pagination']['PAGES'][] = array(
						"PAGENO" => $pageno,
						"URL" => "?" . self::_NAME_PARAM_EVT . "=" . self::_PAGENO_EVT . "&" . $variable_get . "=$pageno",
						"PARAM_URL" => [self::_NAME_PARAM_EVT => self::_PAGENO_EVT, $variable_get => $pageno],
					);
				}

				$this->datatpl['pagination']["CURRENTPAGE"] = $page;
				$this->datatpl['pagination']["TOTALPAGES"] = $pages;
				$this->datatpl['pagination']["NBREPARPAGE"] = $n;

				if ($pages > $page) {
					$nextpage = $page + 1;
					$this->datatpl['pagination']["NEXTPAGE"] = $nextpage;
				} else {
					$this->datatpl['pagination']["NEXTPAGE"] = false;
				}


				if ($page > 1) {
					$prevpage = $page - 1;
					$this->datatpl['pagination']["PREVPAGE"] = $prevpage;
				} else {
					$this->datatpl['pagination']["PREVPAGE"] = false;
				}
		*/

		$this->datatpl['pagination']["CURRENTPAGE"] = $page;
		$this->datatpl['pagination']["TOTALPAGES"] = 0;
		$this->datatpl['pagination']["NBREPARPAGE"] = $n;
		$this->datatpl['pagination']["NEXTPAGE"] = $page +1;
		if ($page > 1) {
			$prevpage = $page - 1;
			$this->datatpl['pagination']["PREVPAGE"] = $prevpage;
		} else {
			$this->datatpl['pagination']["PREVPAGE"] = false;
		}

		//COnstruciton de la requete avec le limit
		$this->datatpl['pagination']['REQUETEPAGINATION'] = $requete . " LIMIT " . (($page - 1) * $n) . "," . $n;
	}

	/**
	 * Renvoi la requete sql qui a est paginé dans l'instance
	 *
	 * @return string
	 */
	public function getRequetePagination()
	{
		return $this->datatpl['pagination']['REQUETEPAGINATION'];
	}

}

/**
 * Se déclenche lors de la pagination de la requête
 *
 * @name calcul_Erreur_Pagination
 */
class calcul_Erreur_Pagination extends Exception
{
}

/**
 * Se déclenche si le nombre d'élément par page est incorrecte
 *
 * @name evtsetpagination_Erreur_Pagination
 */
class evtsetpagination_Erreur_Pagination extends Exception
{
}
