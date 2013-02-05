<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config.php';

$ugdir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'htdocs' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'usergroup';

$data = new UGRMData(new \SplFileInfo($ugdir));
foreach ($data->listGroups() as $group) {
    if (!$group->ical) continue;
    copy($group->ical, sprintf($ugdir . DIRECTORY_SEPARATOR . '%s.ical', $group->id));
}