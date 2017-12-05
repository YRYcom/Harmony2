<?php
namespace Harmony2\Datatable;

/**
 * Gestion des messages d'action
 */
class ElementActionMsg extends ElementHTML
{
    /**
     * {@inheritdoc}
     */
    public function __construct($template_name = null, $options = array())
    {
        if (is_null($template_name))
            $template_name = 'ElementActionMsg';
        parent::__construct($template_name, $options);
    }
}
