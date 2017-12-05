<?php
namespace Harmony2\Datatable;

class ElementHeaderTitle extends ElementHTML
{
    /**
     * {@inheritdoc}
     */
    public function __construct($template_name = null, $options = array())
    {
        if (null === $template_name)
            $template_name = 'ElementHeaderTitle';
        parent::__construct($template_name, $options);
    }
}
