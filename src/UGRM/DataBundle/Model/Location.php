<?php

namespace UGRM\DataBundle\Model;

class Location extends SimpleLocation
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $twitter = false;

    /**
     * @var string
     */
    public $publictransport;
    /**
     * @var string
     */

    public $street;
    /**
     * @var string
     */
    public $zip;

    /**
     * @var string
     */
    public $city;

    /**
     * @var string
     */
    public $region = 'Hessen';

    /**
     * @var string
     */
    public $country = 'Deutschland';

}
