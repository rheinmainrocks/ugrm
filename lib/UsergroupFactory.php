<?php

use \Carbon\Carbon;

class UsergroupFactory
{
    public static function fromXMLFile(\SplFileInfo $xmlfile)
    {
        $xml = simplexml_load_file($xmlfile->getPathname());

        $usergroup = new Usergroup();
        $usergroup->id = str_replace('.xml', '', $xmlfile->getFilename());
        static::setProps(array('name', 'url', 'description'), $xml, $usergroup);
        static::setProps(array('nickname'), $xml, $usergroup, true);
        // Tags
        foreach ($xml->tags->tag as $tag) $usergroup->tags[] = strval($tag);
        // Contact
        static::setProps(array('twitter', 'hashtag', 'facebook', 'googleplus', 'xing', 'email'), $xml->contact, $usergroup, true);
        if (property_exists($xml->contact, 'mailinglist')) {
            foreach ($xml->contact->mailinglist as $m) {
                $mailinglist = new Mailinglist();
                static::setProps(array('url', 'label'), $m, $mailinglist);
                static::setProps(array('description'), $m, $mailinglist, true);
                $usergroup->mailinglists[] = $mailinglist;
            }
        }
        // Sponsors
        if (property_exists($xml, 'sponsors')) {
            foreach ($xml->sponsors->sponsor as $s) {
                $sponsor = new Sponsor();
                static::setProps(array('url', 'name'), $s, $sponsor);
                $usergroup->sponsors[] = $sponsor;
            }
        }

        // Photos
        foreach (new IteratorIterator(new GlobIterator(dirname($xmlfile->getPathname()) . DIRECTORY_SEPARATOR . str_replace('.xml', '.*', $xmlfile->getFilename()))) as $infofile) {
            if (preg_match('/\.(logo|group)\.(gif|jpg|png)$/', $infofile->getFilename(), $match)) {
                if ($match[1] == 'logo') $usergroup->logo = $infofile->getFilename();
                if ($match[1] == 'group') $usergroup->group = $infofile->getFilename();
            }
        }
        if (property_exists($xml, 'photocredits')) {
            if (property_exists($xml->photocredits, 'logo')) $usergroup->logo_credit = $xml->photocredits->logo;
            if (property_exists($xml->photocredits, 'group')) $usergroup->group_credit = $xml->photocredits->group;
        }

        // Meetings
        if (property_exists($xml, 'schedule')) {
            // Default location
            $attrs = $xml->schedule->attributes();
            $defaultLocation = null;
            if (isset($attrs['usedefaultmeetinglocation']) && $attrs['usedefaultmeetinglocation'] && property_exists($xml, 'defaultmeetinglocation')) {
                $defaultLocation = self::getLocation($xml, 'defaultmeetinglocation');
            }

            if (property_exists($xml->schedule, 'ical')) {
                $usergroup->ical = strval($xml->schedule->ical);
                $icalfile = dirname($xmlfile->getPathname()) . DIRECTORY_SEPARATOR . str_replace('.xml', '.ical', $xmlfile->getFilename());
                if (file_exists($icalfile)) {
                    MeetingReader::fetchMeetings($usergroup, new \SplFileInfo($icalfile));
                }
                // Meeting location immer mit default location überschreiben
                if ($defaultLocation) foreach ($usergroup->meetings as $meeting) $meeting->location = $defaultLocation;
            } else {
                $sort = array();
                foreach ($xml->schedule->meeting as $m) {
                    $meeting = new Meeting();
                    $meeting->usergroup = $usergroup;
                    $meeting->time = new Carbon(strval($m->time));
                    $meeting->name = strval($m->name);
                    static::setProps(array('description', 'url'), $m, $meeting, true);
                    $meeting->location = self::getLocation($m, 'location');
                    // Meeting location nur mit default location überschreiben, falls nicht gesetzt
                    if ($meeting->location === null) if ($defaultLocation) $meeting->location = $defaultLocation;

                    $usergroup->meetings[] = $meeting;
                    $sort[] = strval($m->time);
                }
                array_multisort($sort, SORT_ASC, $usergroup->meetings);
            }
        }

        return $usergroup;
    }

    /**
     * @param SimpleXMLElement $node
     * @param $key
     * @return Location|null
     */
    private static function getLocation(\SimpleXMLElement $node, $key)
    {
        if (!property_exists($node, $key)) return null;
        $loc = new Location();
        static::setProps(array('name', 'street', 'zip', 'city'), $node->$key, $loc);
        static::setProps(array('url', 'twitter', 'publictransport', 'region', 'country'), $node->$key, $loc, true);
        return $loc;
    }

    private static function setProps($props, \SimpleXMLElement $node, $target, $optional = null)
    {
        if ($optional === null) $optional = false;
        foreach ($props as $k) {
            if ($optional) if (!property_exists($node, $k)) continue;
            $target->$k = trim(strval($node->$k));
        }
    }
}
