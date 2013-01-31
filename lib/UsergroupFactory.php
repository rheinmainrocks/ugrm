<?php


class UsergroupFactory
{
    public static function fromXMLFile(\SplFileInfo $xmlfile)
    {
        $xml = simplexml_load_file($xmlfile->getPathname());

        $usergroup = new Usergroup();
        $usergroup->id = str_replace('.xml', '', $xmlfile->getFilename());
        $usergroup->name = strval($xml->name);
        $usergroup->nickname = strval($xml->nickname);
        $usergroup->url = strval($xml->url);
        $usergroup->description = strval($xml->description);
        // Tags
        foreach ($xml->tags->tag as $tag) $usergroup->tags[] = strval($tag);
        // Contact
        static::setProps(array('twitter', 'hashtag'), $xml->contact, $usergroup, true);
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
            $now = new \DateTime();
            if ($xml->schedule->ical) {
                // FIXME: Implementieren
            } else {
                $sort = array();
                foreach ($xml->schedule->meeting as $m) {
                    $meeting = new Meeting();
                    $meeting->usergroup = $usergroup;
                    $meeting->time = new \DateTime(strval($m->time));
                    $meeting->isPast = $now->diff($meeting->time)->invert === 1;
                    $meeting->description = strval($m->description);
                    if (property_exists($m, 'url')) $meeting->url = strval($m->url);

                    // Location
                    if (property_exists($m, 'location')) {
                        $meeting->location = new Location();
                        static::setProps(array('name', 'street', 'zip', 'city'), $m->location, $meeting->location);
                        static::setProps(array('url', 'publictransport', 'region', 'country'), $m->location, $meeting->location, true);
                    }

                    $usergroup->meetings[] = $meeting;
                    $sort[] = strval($m->time);
                }
                array_multisort($sort, SORT_ASC, $usergroup->meetings);
            }
        }

        return $usergroup;
    }

    private static function setProps($props, \SimpleXMLElement $node, $target, $optional = null)
    {
        if ($optional === null) $optional = false;
        foreach ($props as $k) {
            if ($optional) if (!property_exists($node, $k)) continue;
            $target->$k = strval($node->$k);
        }
    }
}
