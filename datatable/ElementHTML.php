<?php
namespace Harmony2\Datatable;

use Harmony2\Template;

/**
 * Gestion des <strong>HTMLElement</strong>
 * <ul>
 *      <li>Pagination</li>
 *      <li>Filtre</li>
 *      <li>Tableau</li>
 *      <li>Header</li>
 *      <li>Message d'action</li>
 * </ul>
 *
 */
abstract class ElementHTML
{
    /**
     *
     * @var Template
     */
    private $template = '';
    /**
     *
     * @var string
     */
    private $template_name = '';

    /**
     * Initialisation des paramètres du template
     * 
     * @param string $template_name Nom du template désiré
     * @param array $options Variables que l'on souhaite passer au template
     */
    public function __construct($template_name, $options = array())
    {
        $this->template = new Template();
        $this->template_name = $template_name;

        if (!empty($options)) {
            $this->template->merge($options);
        }
    }

    /**
     * Parse le template avec les informations
     * 
     */
    public function parse()
    {
        $this->template->set_filenames(array(
            'contenu' => 'HTMLElement/'.$this->template_name.'.tpl'
        ));

        $this->template->parse('contenu');
    }

    /**
     * Permet de passer une variable ou une valeur au template
     * 
     * @param string $name Le nom de l'objet donné
     * @param mixed $value La valeur que l'on souhaite passé au template
     */
    public function setOption($name, $value)
    {
        $this->template->set($name, $value);
    }

    /**
     * Récupère la valeur associée au nom donné
     * 
     * @param string $name
     * @return mixed
     */
    public function getOption($name)
    {
        return $this->template->get($name);
    }
}
