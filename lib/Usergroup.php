<?php

class Usergroup
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $nickname;

    /**
     * @var string[]
     */
    public $tags = array();

    /**
     * @var string
     */
    public $logo = false;

    /**
     * @var string
     */
    public $group = false;

    /**
     * @var string
     */
    public $twitter = false;

    /**
     * @var string
     */
    public $hashtag = false;

    /**
     * @var string
     */
    public $logo_credit;

    /**
     * @var string
     */
    public $group_credit;

    /**
     * @var Mailinglist[]
     */
    public $mailinglists = array();

    /**
     * @var Sponsor[]
     */
    public $sponsors = array();

    /**
     * @var Meeting[]
     */
    public $meetings = array();

    /**
     * Gibt das nächste Meeting zurück
     *
     * @return Meeting|null
     */
    public function getFutureMeeting()
    {
        $futureMeetings = array_filter($this->meetings, function (Meeting $m) {
            return !$m->isPast;
        });
        return count($futureMeetings) > 0 ? array_shift($futureMeetings) : null;
    }
}
