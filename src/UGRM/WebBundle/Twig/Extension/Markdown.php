<?php

namespace UGRM\WebBundle\Twig\Extension;

use Michelf\Markdown as MichelfMarkdown;

class Markdown extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'md' => new \Twig_Filter_Method($this, 'markdown', array('is_safe' => array('html'))),
        );
    }

    public function markdown($str)
    {
        return MichelfMarkdown::defaultTransform($str);
    }

    public function getName()
    {
        return 'wemoof_webbundle_twig_extension_markdown';
    }
}
