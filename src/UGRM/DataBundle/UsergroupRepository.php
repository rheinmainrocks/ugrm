<?php

namespace UGRM\DataBundle;

use UGRM\DataBundle\Model\Usergroup;
use UGRM\DataBundle\XML\UsergroupFactory;
use UGRM\DataBundle\Model\Meeting;

class UsergroupRepository
{
    /**
     * @var \SplFileInfo
     */
    private $dir;

    public function __construct($dir)
    {
        $this->dir = new \SplFileInfo($dir);
    }

    /**
     * @param array
     * @return Usergroup[]
     */
    public function listGroups($filter = null)
    {
        $usergroups = array();
        $sort = array();
        $sort2 = array();
        if ($filter === null) $filter = array();

        foreach (new \IteratorIterator(new \GlobIterator($this->dir->getPathname() . DIRECTORY_SEPARATOR . '*.xml')) as $file) {
            $ug = UsergroupFactory::fromXMLFile($file);
            if (isset($filter['tag']) && !in_array(strtolower($filter['tag']), array_map('strtolower', $ug->tags))) continue;
            if (isset($filter['usergroup']) && $filter['usergroup'] != $ug->id) continue;
            if (isset($filter['incubator']) && $filter['incubator'] != $ug->incubator) continue;
            $usergroups[] = $ug;
            $sort[] = ($nm = $ug->getFutureMeeting()) ? $nm->time->getTimestamp() : 99999999999;
            $sort2[] = $ug->name;
        }
        array_multisort($sort, SORT_ASC, $sort2, SORT_ASC, $usergroups);
        return $usergroups;
    }

    /**
     * @return string[]
     */
    public function getTags()
    {
        $tagCount = array();
        foreach ($this->listGroups() as $group) {
            foreach (array_map('strtolower', $group->tags) as $tag) {
                if (!isset($tagCount[$tag])) $tagCount[$tag] = 0;
                $tagCount[$tag]++;
            }
        }
        $tags = array();
        $sort = array();
        foreach ($tagCount as $tag => $count) {
            $tags[] = array(
                'name' => $tag,
                'count' => $count
            );
            $sort[] = preg_replace('/[^A-Z0-9]/', '', strtoupper($tag));
        }
        array_multisort($sort, SORT_ASC, $tags);
        return $tags;
    }

    /**
     * @return Meeting[]
     */
    public function getMeetings()
    {
        $meetings = array();
        $sort = array();
        foreach ($this->listGroups() as $group) {
            if ($meeting = $group->getFutureMeeting()) {
                $meetings[] = $meeting;
                $sort[] = $meeting->time->format('U');
            }
        }
        array_multisort($sort, SORT_ASC, $meetings);
        return $meetings;
    }

    /**
     * @param Usergroup $group
     * @return \SplFileInfo
     */
    public function getXmlFile(Usergroup $group)
    {
        return new \SplFileInfo($this->dir->getPathname() . DIRECTORY_SEPARATOR . $group->id . '.xml');
    }
}
