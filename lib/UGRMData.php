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
     * @return Usergroup[]
     */
    public function listGroups()
    {
        $usergroups = array();
        foreach (new IteratorIterator(new GlobIterator($this->dir->getPathname() . DIRECTORY_SEPARATOR . '*.xml')) as $file) {
            $usergroups[] = UsergroupFactory::fromXMLFile($file);

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
