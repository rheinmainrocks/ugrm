<?php

class Meeting
{
    /**
     * @var \DateTime
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
     * @var string
     */
    public $url;

    /**
     * @var boolean
     */
    public $isPast;
}
