<?php

class Usergroup
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $nickname;
    public $tags = array();
    public $logo = false;
    public $group = false;

    /**
     * @var Meeting[]
     */
    public $meetings = array();
}
