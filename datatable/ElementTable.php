<?php
namespace Harmony2\Datatable;

use Exception;

/**
 * Gestion élèments table
 */
class ElementTable extends ElementHTML
{
    /**
     * Liste des colonnes du tableau
     * 
     * @var array
     */
    protected $listTH = array();
    /**
     * Liste des lignes du tableau
     * 
     * @var array
     */
    protected $listTR = array();
    /**
     * Type de tableau
     * -- Object ou Array
     * 
     * @var string 
     */
    protected $type;
    /**
     * Les données à mettre dans le tableau
     * 
     * @var array
     */
    protected $data = array();
    /**
     * L'élèment TH qui sera cloné pour remplir le tableau
     * 
     * @var ElementTH
     */
    protected $th;
    /**
     * L'élèment TR qui sera cloné pour remplir le tableau
     * 
     * @var ElementTR
     */
    protected $tr;
    /**
     * L'élèment TD qui sera cloné pour remplir le tableau
     * 
     * @var ElementTD
     */
    protected $td;
    /**
     * Les actions disponibles pour chaque ligne du tableau
     *
     * @var array
     */
    protected $actions;

    /**
     * Construction et initialisation du tableau
     * 
     * @param array $data Les données du tableau
     * @param string $template_name Le nom du template à utiliser
     * @param array $options Les variables à ajouter au template
     * @param string $type Le type de données
     */
    public function __construct(Array $data = array(), $template_name = null, Array $options = array(), $type = 'object')
    {
        $this->type = $type;
        $this->data = $data;
        $this->th = new ElementTH;
        $this->tr = new ElementTR;
        $this->td = new ElementTD;
        if (null === $template_name)
            $template_name = 'ElementTable';
        parent::__construct($template_name, $options);
    }
    
    /**
     * Ajoute les élèments TH du tableau avec leurs paramètres
     * 
     * @param array $elements
     */
    public function addElements(Array $elements = array())
    {
        $this->tr = clone $this->tr;
        foreach ($elements as &$params) {
            $this->th = clone $this->th;
            $this->th->clean();
            $this->th->setValues($params);
            $this->listTH[] = $this->th;
            
            if (null !== $attr = $this->th->getOption('attr')) {
                $this->td = clone $this->td;
                $this->td->clean();
                $this->td->value($attr);
                if (null !== $func = $this->th->getOption('func')) {
                    $this->td->func($func);
                }
                $this->tr->addTD($this->td);  
            }
        }
        $this->listTR[] =  $this->tr;
    }

    /**
     * Ajoute les actions possibles sur chaque ligne
     */
    public function addActionsByLine(Array $actions)
    {
        foreach ($actions as $key => $value) {
            if (!isset($value['url']) || empty($value['url']))
                throw new Exception("Missing URL in action ".$key, 1);
            if (!isset($value['img']) || empty($value['img']))
                throw new Exception("Missing IMG in action ".$key, 1);      
        }
        $this->actions = $actions;
    }

    /**
     * Parse le template du tableau avec les données nécessaires
     */
    public function parse()
    {
        $this->setOption('actions', $this->actions);
        $this->setOption('data',    $this->data);
        $this->setOption('listTH',  $this->listTH);
        $this->setOption('listTR',  $this->listTR);

        parent::parse();
    }
}