<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config.php';

$ugdir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'htdocs' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'usergroup';
$svgtemplate = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'iloveugs.svg');
$data = new UGRMData(new \SplFileInfo(realpath($ugdir)));
foreach ($data->listGroups() as $group) {
    if ($group->logo) continue;
    $logofile = preg_replace('/\.xml$/', '.logo.png', $data->getXmlFile($group)->getPathname());

    $s = array(
        '{tagname}' => strtoupper($group->tags[0]),
        '{usergroupname}' => $group->name,
        '{groupy}' => stristr($group->tags[0], 'j') === false ? 1070 : 1090,
    );

    file_put_contents($logofile . '.svg', str_replace(array_keys($s), array_values($s), $svgtemplate));
    exec(sprintf('/usr/bin/env convert -background transparent -trim +repage %s %s', escapeshellarg($logofile . '.svg'), escapeshellarg($logofile)));
    echo sprintf('%s: %s' . PHP_EOL, $group->name, $logofile);
}
