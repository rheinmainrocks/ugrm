<?php

namespace UGRM\DataBundle\Model;

class Meeting
{
    /**
     * @var \Carbon\Carbon
     */
    public $time;

    /**
     * @var Location
     */
    public $location;

    /**
     * @var string
     */
    public $publictransport;

    /**
     * @var string
     */
    public $description;

    /**
     * @var boolean
     */
    public $descriptionIsMarkdown = true;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $url;

    /**
     * @var Usergroup
     */
    public $usergroup;
}
