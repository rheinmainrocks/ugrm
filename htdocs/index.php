<?php

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '1');

spl_autoload_register(function ($classname) {
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $classname . '.php';
});

$data = new UGRMData(new \SplFileInfo(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'usergroup'));


?><!doctype html>
<html lang="de-de">
<head>
    <meta charset="utf-8">
    <title>UGRM &ndash; Usergroups RheinMain</title>
    <meta name="description" content="Beschreibung und Termine der Technologie-Usergroups im Rhein-Main-Gebiet.">
    <meta name="author" content="Markus Tacker | http://coderbyheart.de/">
    <!-- See /humans.txt for more infos -->
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="/assets/styles.css" type="text/css">
    <!--
    <link rel="stylesheet" href="/build/complete-min.2013013.css" type="text/css">
    -->
</head>
<body>
<header>
    <h1>
        <a href="/" rel="index"><img src="/build/logo.png" alt="UGRM &ndash; Usergroups RheinMain"></a>
    </h1>
    <nav>
        <a href="http://github.com/tacker/ugrm-data/">Eintrag bearbeiten</a>
    </nav>
</header>
<aside id="left">
    <h2>Tags</h2>
    <nav class="tags">
        <?php foreach ($data->getTags() as $tag): ?>
        <a href="/tag/<?php echo $tag['name']; ?>"
           data-count="<?php echo $tag['count']; ?>"><?php echo $tag['name']; ?></a>
        <?php endforeach; ?>
    </nav>
</aside>
<div id="right">
    <?php foreach ($data->listGroups() as $group): ?>
    <div class="usergroup">
        <h2><?php echo $group->name; ?></h2>

        <div class="description">
            <?php if ($group->logo): ?><a href="<?php echo $group->url; ?>"><img
                src="/data/usergroup/<?php echo $group->logo; ?>" class="logo"></a><?php endif; ?>
            <?php echo $group->description; ?>
        </div>
        <dl>
            <dt>Homepage</dt>
            <dd><a href="<?php echo $group->url; ?>"><i class="icon-link"></i> <?php echo $group->url; ?></a></dd>
            <?php if ($group->twitter || $group->hashtag): ?>
            <dt>Twitter</dt>
            <dd>
                <?php if ($group->twitter): ?>
                <a href="http://twitter.com/<?php echo substr($group->twitter, 1); ?>"><i
                        class="icon-twitter"></i> <?php echo substr($group->twitter, 1); ?></a>
                <?php endif; ?>
                <?php if ($group->twitter && $group->hashtag): ?><br><?php endif; ?>
                <?php if ($group->hashtag): ?>
                <a href="search.twitter.com/?q=%23<?php echo urlencode(substr($group->hashtag, 1)); ?>"># <?php echo substr($group->hashtag, 1); ?></a>
                <?php endif; ?>
            </dd>
            <?php endif; ?>
        </dl>
    </div>
    <?php endforeach; ?>
</div>
</body>
</html>
