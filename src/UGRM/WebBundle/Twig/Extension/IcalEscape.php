<?php

namespace UGRM\WebBundle\Twig\Extension;

class IcalEscape extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            'icalescape' => new \Twig_Filter_Method($this, 'icalescape', array('is_safe' => array('html'))),
        );
    }

    public function icalescape($str)
    {
        $str = preg_replace('/([\,;])/', '\\\$1', $str);
        $str = str_replace("\n", '\n', $str);
        return $str;
    }

    public function getName()
    {
        return 'wemoof_webbundle_twig_extension_icalescape';
    }
}
