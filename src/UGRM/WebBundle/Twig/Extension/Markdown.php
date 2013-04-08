<?php

namespace UGRM\WebBundle\Twig\Extension;

use dflydev\markdown\MarkdownParser;

class Markdown extends \Twig_Extension
{
    public function __construct()
    {
        $this->markdownParser = new MarkdownParser();
    }

    public function getFilters()
    {
        return array(
            'md' => new \Twig_Filter_Method($this, 'markdown', array('is_safe' => array('html'))),
        );
    }

    public function markdown($str)
    {
        return $this->markdownParser->transformMarkdown($str);
    }

    public function getName()
    {
        return 'wemoof_webbundle_twig_extension_markdown';
    }
}