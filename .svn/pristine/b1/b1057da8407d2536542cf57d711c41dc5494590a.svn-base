<?php
namespace Harmony2\Datatable;
/**
 * Gestion des élèments de pagination
 */
class ElementPagination extends ElementHTML
{
    /**
     * {@inheritdoc}
     */
    public function __construct($pager, $template_name = null, $options = array())
    {
        if (is_null($template_name))
            $template_name = 'ElementPagination';
        $options['__Pager'] = $pager;
        parent::__construct($template_name, $options);
    }
}
