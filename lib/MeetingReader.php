<?php

use Carbon\Carbon;

class MeetingReader
{
    public static function fetchMeetings(Usergroup $usergroup, \SplFileInfo $cacheFile)
    {
        $parser = new CalendarParser($cacheFile);
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
