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
        return $usergroup;
    }
}
