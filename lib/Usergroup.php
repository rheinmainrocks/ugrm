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
    public $nickname = false;

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
    public $facebook = false;

    /**
     * @var string
     */
    public $googleplus = false;

    /**
     * @var string
     */
    public $xing = false;

    /**
     * @var string
     */
    public $email = false;

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
     * @var string
     */
    public $ical = array();

    /**
     * Typischerweise ist es DIE Usergruppe, aber es gibt Ausnahmen: DER Webmontag
     * @var boolean
     */
    public $female = true;

    /**
     * Gibt das nächste Meeting zurück
     *
     * @return Meeting|null
     */
    public function getFutureMeeting()
    {
        $futureMeetings = array_filter($this->meetings, function (Meeting $m) {
            return $m->time->isFuture();
        });
        return count($futureMeetings) > 0 ? array_shift($futureMeetings) : null;
    }
}
