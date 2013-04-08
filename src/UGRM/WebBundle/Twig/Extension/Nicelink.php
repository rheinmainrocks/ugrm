<?php

namespace UGRM\WebBundle\Twig\Extension;

class Nicelink extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            'nicelink' => new \Twig_Filter_Method($this, 'nicelink', array('is_safe' => array('html'))),
        );
    }

    public function nicelink($str)
    {
        return preg_replace('/^www\./', '', parse_url($str, PHP_URL_HOST));
    }

    public function getName()
    {
        return 'wemoof_webbundle_twig_extension_nicelink';
    }
}