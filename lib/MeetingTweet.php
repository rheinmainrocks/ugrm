<?php

class MeetingTweet
{
    /**
     * @var Meeting
     */
    private $meeting;

    /**
     * @var string
     */
    private $host;

    private $tweet = '';

    public function __construct(Meeting $meeting, $host)
    {
        $this->meeting = $meeting;
        $this->host = $host;
    }

    function addToTweet($str, $strlen = null)
    {
        if (strlen($this->tweet) + ($strlen ? $strlen : strlen($str)) > 140) return;
        $this->tweet .= $str;
    }

    /**
     * @return string
     */
    public function getTweet()
    {
        $this->tweet = '';
        if ($this->meeting->time->isToday()) {
            $this->addToTweet('Heute');
        } elseif ($this->meeting->time->isTomorrow()) {
            $this->addToTweet('Morgen');
        } else {
            $this->addToTweet('Am ' . strftime('%a, %d.%m', $this->meeting->time->format('U')));
        }
        $this->addToTweet(' trifft sich die ' . ($this->meeting->usergroup->twitter ? $this->meeting->usergroup->twitter : ($this->meeting->usergroup->nickname ? $this->meeting->usergroup->nickname : $this->meeting->usergroup->name)));
        $this->addToTweet(' um ' . strftime('%H:%M Uhr', $this->meeting->time->format('U')));
        if ($this->meeting->location && $this->meeting->location instanceof Location) {
            $this->addToTweet(' in ' . $this->meeting->location->city);
            if ($this->meeting->location->twitter) $this->addToTweet(' ' . $this->meeting->location->twitter);
        }
        $this->addToTweet(' ' . sprintf('http://%s/~%s', $this->host, $this->meeting->usergroup->id), 22);
        $this->addToTweet(' #ugrm');
        return $this->tweet;
    }

    public function getLink()
    {
        $url = 'https://twitter.com/intent/tweet';
        $params = array(
            'original_referer' => sprintf('http://%s/', $this->host),
            'text' => $this->getTweet());
        return $url . '?' . http_build_query($params);
    }
}
