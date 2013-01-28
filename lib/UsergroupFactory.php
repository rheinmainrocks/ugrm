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
        $usergroup->twitter = strval($xml->contact->twitter);
        $usergroup->hashtag = strval($xml->contact->hashtag);
        foreach ($xml->tags->tag as $tag) $usergroup->tags[] = strval($tag);

        foreach (new IteratorIterator(new GlobIterator(dirname($xmlfile->getPathname()) . DIRECTORY_SEPARATOR . str_replace('.xml', '.*', $xmlfile->getFilename()))) as $infofile) {
            if (preg_match('/\.(logo|group)\.(gif|jpg|png)$/', $infofile->getFilename(), $match)) {
                if ($match[1] == 'logo') $usergroup->logo = $infofile->getFilename();
                if ($match[1] == 'group') $usergroup->group = $infofile->getFilename();
            }
        }

        // Meetings
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
