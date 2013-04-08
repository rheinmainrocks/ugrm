<?php

namespace UGRM\DataBundle\Meeting;

use Carbon\Carbon;
use UGRM\DataBundle\Model\Meeting;
use UGRM\DataBundle\Model\SimpleLocation;
use UGRM\DataBundle\Model\Usergroup;

class MeetingReader
{
    public static function fetchMeetings(Usergroup $usergroup, \SplFileInfo $cacheFile)
    {
        $parser = new \CalendarParser($cacheFile);
        $now = new \DateTime();
        foreach ($parser->getEvents() as $event) {
            $meeting = new Meeting();
            $meeting->usergroup = $usergroup;
            $meeting->name = $event->SUMMARY;
            $meeting->description = $event->DESCRIPTION;
            $meeting->time = new Carbon($event->DTSTART);
            $meeting->location = new SimpleLocation();
            $meeting->location->description = $event->LOCATION;
            $usergroup->meetings[] = $meeting;
        }
    }
}
