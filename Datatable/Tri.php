<?php
namespace Harmony2\Datatable;

use Harmony2\Http\Request;

class Tri
{
	/**
	 * Nom de la variable evenement de tri
	 *
	 * @var string
	 */

	const _EVT = 'trievt';

	/**
	 * Nom de l'evt pour appliquer un tri sur un champs
	 *
	 * @var string
	 */
	const _EVT_TRI = 'chptri';

	/**
	 * Contient l'ensemble des champs à trier
	 *
	 * @var array
	 */
	private $lestris = array();

	/**
	 * Champs courant en cours de tri
	 *
	 * @var array $triCourant
	 */
	public $triCourant = array();

	/**
	 * Cette méthode raffraichi l'objet tri en fonction des evts passé en parametre d'url
	 *
	 * @param Request $request
	 * @return bool
	 */
	public function raffraichir(Request $request)
	{
		if (!$request->issetRequest(self::_EVT))
			return false;

		switch ($request->request(self::_EVT)) {
			case self::_EVT_TRI :
				$this->definir($request->request('npptri'));
				break;
		}
		return true;
	}

	public function getLesTris()
	{
		return $this->lestris;
	}

	/**
	 * Ajoute un champs à trier
	 *
	 * @param string $id nom du champs
	 * @param string $valeur valeur du champs
	 */
	public function ajouter($valeur, $id)
	{
		$this->lestris[$id] = $valeur;
	}

	/**
	 * Défifini le tri courant
	 *
	 * @param string $id nom du champs
	 * @param int $tri sens du tri 0: DESC 1:ASC
	 * @param $force boolean on force l'initialisation du tri
	 * @return boolean
	 */
	public function definir($id, $tri = 1, $force = false)
	{
		if ($this->est($id)) {
			if ($force == true) {
				//on force l'initilisation du tri
				$this->triCourant = array($this->lestris[$id], $tri, $id);
			} elseif ((count($this->triCourant) == 3) && ($this->lestris[$id] == $this->triCourant[0])) {
				//on change de sens le tri courant
				$this->triCourant[1] = ($this->triCourant[1] + 1) % 2;
			} else {
				//on initialise le tri courant
				$this->triCourant = array($this->lestris[$id], $tri, $id);
			}

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Renvoi le champs courant trier ainsi que le sens
	 *
	 * @return array
	 */
	public function recuperer()
	{
		return $this->triCourant;
	}

	/**
	 * Nom du champs courant trier
	 *
	 * @return string
	 */
	public function courantChamp()
	{
		return $this->triCourant[0];
	}

	/**
	 * Sens de trie du champs courant
	 *
	 * @return int
	 */
	public function sensCourantChamp()
	{
		if (isset($this->triCourant[1]))
			return $this->triCourant[1];
		return false;

	}

	/**
	 * Id du champs courant
	 *
	 * @return int
	 */
	public function idCourantChamp()
	{
		if (isset($this->triCourant[2]))
			return $this->triCourant[2];
		return false;
	}

	/**
	 * Permet de savoir si un champs courant est défini
	 *
	 * @return boolean
	 */
	public function existe()
	{
		return ((is_array($this->triCourant)) && (count($this->triCourant) == 3));
	}

	/**
	 * Verifie si le champs fait partie des champs triable
	 *
	 * @param string $id nom du champs
	 * @return boolean
	 */
	public function est($id)
	{
		return array_key_exists($id, $this->lestris);
	}

}
