<?php

setlocale(LC_ALL, $_SERVER['LOCALE']);

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '1');

spl_autoload_register(function ($classname) {
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $classname . '.php';
});

$req = substr(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), 1);
$parts = explode('/', $req);

$data = new UGRMData(new \SplFileInfo(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'usergroup'));

$e = function ($str) {
    echo htmlspecialchars($str);
};

$l = function ($str) {
    echo parse_url($str, PHP_URL_HOST);
}


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
        <a href="/tag/<?php echo urlencode($tag['name']); ?>" data-count="<?php echo $tag['count']; ?>"><?php $e($tag['name']); ?></a>
        <?php endforeach; ?>
    </nav>
    <?php $meetings = $data->getMeetings();
    if ($meetings): ?>
        <h2>Termine</h2>
        <ul>
            <?php foreach ($meetings as $meeting): ?>
            <li>
                <a href="/usergroup/<?php $e($meeting->usergroup->id); ?>"><time datetime="<?php echo $meeting->time->format(DATE_ATOM); ?>"><?php echo strftime('%a, %d. %B %Y, %H:%M Uhr', $meeting->time->format('U')); ?></time></a><br>Treffen der
                <a href="/usergroup/<?php $e($meeting->usergroup->id); ?>"><abbr title="<?php $e($meeting->usergroup->name); ?>"><?php $e($meeting->usergroup->nickname); ?></abbr></a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
</aside>
<div id="right">
    <?php
    $q = array();
    if ($parts[0] == 'tag' && isset($parts[1]) && !empty($parts[1])) $q['tag'] = $parts[1];
    if ($parts[0] == 'usergroup' && isset($parts[1]) && !empty($parts[1])) $q['usergroup'] = $parts[1];
    foreach ($data->listGroups($q) as $group): ?>
        <article class="usergroup">
            <div class="description">
                <h2><?php $e($group->name); ?>
                    <small>(<?php $e($group->nickname); ?>)</small>
                </h2>
                <p><?php $e($group->description); ?></p>
                <?php
                $meeting = $group->getFutureMeeting();
                if ($meeting): ?>
                    <div itemscope itemtype="http://schema.org/Event" class="event">
                        <h3><i class="icon-calendar"></i> Nächster
                            Termin:<br><span itemprop="description"><?php $e($meeting->description); ?></span> <?php echo strftime('am %A, %d. %B %Y um %H:%M Uhr', $meeting->time->format('U')); ?>
                        </h3>
                        <?php if ($meeting->url): ?>
                        <p>Details unter
                            <a href="<?php echo $meeting->url; ?>" itemprop="url"><?php $l($meeting->url); ?></a>
                        </p>
                        <?php endif; ?>
                        <span class="hidden" itemprop="name">Treffen der <?php $e($group->nickname); ?>
                            <time datetime="<?php echo $meeting->time->format(DATE_ATOM); ?>" itemprop="startDate"><?php echo strftime('am %A, %d. %B %Y um %H:%M Uhr', $meeting->time->format('U')); ?></time>
                    </span>
                        <?php if ($meeting->location): ?>
                        <h3><i class="icon-map-marker"></i> Ort</h3>
                        <p itemprop="location" itemscope itemtype="http://schema.org/PostalAddress">
                            <?php if ($meeting->location->url): ?><a href="<?php echo $meeting->location->url; ?>" itemprop="url"><?php endif; ?>
                        <span itemprop="name"><?php $e($meeting->location->name); ?></span>
                            <?php if ($meeting->location->url): ?></a><?php endif; ?>
                            <br>
                            <a href="https://maps.google.com/maps?q=<?php echo urlencode(sprintf("%s, %d %s, %s, %s (%s)", $meeting->location->street, $meeting->location->zip, $meeting->location->city, $meeting->location->region, $meeting->location->country, $meeting->location->name)); ?>">
                                <span itemprop="streetAddress"><?php $e($meeting->location->street); ?></span>,
                                <span itemprop="postalCode"><?php $e($meeting->location->zip); ?></span>
                                <span itemprop="addressLocality"><?php $e($meeting->location->city); ?></span>
                                <span itemprop="addressRegion" class="hidden"><?php $e($meeting->location->region); ?></span>
                                <span itemprop="addressCountry" class="hidden"><?php $e($meeting->location->country); ?></span>
                            </a>
                        </p>
                        <?php endif; // $meeting->location ?>
                    </div>
                    <?php endif; ?>

            </div>
            <aside>
                <dl>
                    <?php if ($group->logo): ?>
                    <dt class="hidden">Logo</dt>
                    <dd>
                        <a href="<?php echo $group->url; ?>"><img src="/data/usergroup/<?php echo $group->logo; ?>" class="logo" alt="<?php $e($group->name); ?>"></a>
                    </dd>
                    <?php endif; ?>
                    <dt><i class="icon-link"></i> Homepage</dt>
                    <dd><a href="<?php echo $group->url; ?>"><?php echo $group->url; ?></a></dd>
                    <?php if ($group->twitter || $group->hashtag): ?>
                    <dt><i class="icon-twitter"></i> Twitter</dt>
                    <dd>
                        <?php if ($group->twitter): ?>
                        <a href="http://twitter.com/<?php echo substr($group->twitter, 1); ?>"><?php echo $group->twitter; ?>
                        </a>
                        <?php endif; ?>
                        <?php if ($group->twitter && $group->hashtag): ?><br><?php endif; ?>
                        <?php if ($group->hashtag): ?>
                        <a href="https://twitter.com/search?q=%23<?php echo urlencode(substr($group->hashtag, 1)); ?>"># <?php echo substr($group->hashtag, 1); ?></a>
                        <?php endif; ?>
                    </dd>
                    <?php endif; ?>
                </dl>
            </aside>
        </article>
        <?php endforeach; ?>
</div>
</body>
</html>
