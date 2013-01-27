<?php


class UsergroupFactory
{
    public static function fromXMLFile(\SplFileInfo $xmlfile)
    {
        $xml = simplexml_load_file($xmlfile->getPathname());

        $usergroup = new Usergroup();
        $usergroup->name = strval($xml->name);
        $usergroup->url = strval($xml->url);
        $usergroup->description = strval($xml->description);
        $usergroup->twitter = strval($xml->contact->twitter);
        $usergroup->hashtag = strval($xml->contact->hashtag);
        foreach ($xml->tags->tag as $tag) $usergroup->tags[] = strval($tag);

        foreach(new IteratorIterator(new GlobIterator(dirname($xmlfile->getPathname()) . DIRECTORY_SEPARATOR . str_replace('.xml', '.*', $xmlfile->getFilename()))) as $infofile) {
            if (preg_match('/\.(logo|group)\.(gif|jpg|png)$/', $infofile->getFilename(), $match)) {
                if ($match[1] == 'logo') $usergroup->logo = $infofile->getFilename();
                if ($match[1] == 'group') $usergroup->group = $infofile->getFilename();
            }
        }

        return $usergroup;
    }
}
