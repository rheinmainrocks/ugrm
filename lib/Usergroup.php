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
    public $tags = array();
    public $logo = false;
    public $group = false;

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
