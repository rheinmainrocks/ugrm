<?php

namespace UGRM\WebBundle\Twig\Extension;

class Date extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'shortdate' => new \Twig_Filter_Method($this, 'date', array('is_safe' => array('html'))),
            'longdate' => new \Twig_Filter_Method($this, 'longdate', array('is_safe' => array('html'))),
            'atomdate' => new \Twig_Filter_Method($this, 'atomdate', array('is_safe' => array('html'))),
            'RFC822date' => new \Twig_Filter_Method($this, 'RFC822date', array('is_safe' => array('html'))),
        );
    }

    public function date(\DateTime $d, $format = "'%d. %B %Y, %H:%M Uhr")
    {
        return strftime($format, $d->getTimestamp());
    }

    public function longdate(\DateTime $d)
    {
        return $this->date($d, 'am %A, %d. %B %Y um %H:%M Uhr');
    }

    public function atomdate(\DateTime $d)
    {
        return $d->format(DATE_ATOM);
    }

    public function RFC822date(\DateTime $d)
    {
        return $d->format('D, d M Y H:i:s O');
    }

    public function getName()
    {
        return 'ugrm_web_twig_extension_date';
    }
}
