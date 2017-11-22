<?php
namespace Harmony2\Datatable;

/**
 * Gestion des élèment de filtres
 */
class ElementFilter extends ElementHTML
{
    /**
     * {@inheritdoc}
     */
    public function __construct($pager, $template_name = null, $options = array())
    {
        if (is_null($template_name))
            $template_name = 'ElementFilter';
        $options['__Pager'] = $pager;
        parent::__construct($template_name, $options);
    }
}
