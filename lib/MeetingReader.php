<?php

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
            $start = new \DateTime($event->DTSTART);
            $meeting->time = $start;
            $meeting->isPast = $now->diff($start)->invert === 1;
            $meeting->location = new SimpleLocation();
            $meeting->location->description = $event->LOCATION;
            $usergroup->meetings[] = $meeting;
        }
    }
}
