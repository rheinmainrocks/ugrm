<?php

class UGRMData
{
    /**
     * @var \SplFileInfo
     */
    private $dir;

    public function __construct(\SplFileInfo $dir)
    {
        $this->dir = $dir;
    }

    /**
     * @param array
     * @return Usergroup[]
     */
    public function listGroups($filter = null)
    {
        $usergroups = array();
        if ($filter === null) $filter = array();
        foreach (new IteratorIterator(new GlobIterator($this->dir->getPathname() . DIRECTORY_SEPARATOR . '*.xml')) as $file) {
            $ug = UsergroupFactory::fromXMLFile($file);
            if (isset($filter['tag']) && !in_array($filter['tag'], $ug->tags)) continue;
            $usergroups[] = $ug;
        }
        return $usergroups;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        $tagCount = array();
        foreach ($this->listGroups() as $group) {
            foreach ($group->tags as $tag) {
                if (!isset($tagCount[$tag])) $tagCount[$tag] = 0;
                $tagCount[$tag]++;
            }
        }
        $tags = array();
        foreach ($tagCount as $tag => $count) {
            $tags[] = array(
                'name' => $tag,
                'count' => $count
            );
        }
        return $tags;
    }

}
