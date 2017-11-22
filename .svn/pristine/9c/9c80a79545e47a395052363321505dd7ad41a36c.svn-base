<?php
namespace Harmony2\Datatable;

class Critere
{
    /**
     * Valeur du critère
     *
     * @var array
     */
    private $valeur;



    /**
     * Nom du champs
     *
     * @var string
     */
    private $champs;

    /**
     * Liste des options possible dans le cas d'une listbox
     *
     * @var array
     */
    private $option;

    /**
     * Requete qui sert à recuperer une liste de valeur dans le cas d'une listbox
     *
     * @var string
     */
    private $requeteliste;

    /**
     * 1 le test sur le critere est stric 0 (like) sinon
     *
     * @var int
     */
    private $typewhere=0;


    /**
     * permet de transferer le criter dans la clause having de la requete
     *
     * @var bool
     */
    private $having=false;

    /**
     * Creation de l'instance
     *
     * @param string $champs
     * @param array $option
     * @param string $req
     */
    public function __construct($champs, $option, $req)
    {
        $this->champs = $champs;
        $this->option = strtoupper($option);
        $this->requeteliste = $req;
    }

    /**
     * Defini la valeur du critere
     *
     * @param array $valeur
     * @return void
     */
    public function setValeur($valeur)
    {
        $this->valeur = $valeur;
    }

    /**
     * Assesseur set $champs
     *
     * @param string $champs
     * @return void
     */
    public function setChamps($champs)
    {
        $this->champs = $champs;
    }

    /**
     * Assesseur set $option
     *
     * @param string $option
     * @return void
     */
    public function setOption($option)
    {
        $this->option = strtoupper($option);
    }

    /**
     * Assesseur set requeteliste
     *
     * @param string $req
     * @return void
     */
    public function setRequeteListe($req)
    {
        $this->requeteliste = $req;
    }

    public function setHaving($value)
    {
        $this->having = $value;
        return $this;
    }

    public function getHaving()
    {
        return $this->having;
    }

    /**
     * Assesseur set typewhere
     *
     * @param string $value
     * @return int
     */
    public function setTypeWhere($value)
    {
        $this->typewhere = $value;
        return $this->typewhere;
    }

    /**
     * Assesseur get typewhere
     *
     * @return string
     */
    public function getTypeWhere()
    {
        return $this->typewhere;
    }

    /**
     * Assesseur get valeur
     *
     * @return array
     */
    public function getValeur()
    {
        return $this->valeur;
    }

    /**
     * @return bool
     */
    public function emptyValeur()
    {
        return !is_array($this->valeur) or count($this->valeur)==0;
    }

    /**
     * Assesseur get champs
     *
     * @return string
     */
    public function getChamps()
    {
        return $this->champs;
    }

    /**
     * Assesseur get option
     *
     * @return string
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * Assesseur get requeteliste
     *
     * @return string|array
     */
    public function getRequeteListe()
    {
        return $this->requeteliste;
    }
}
