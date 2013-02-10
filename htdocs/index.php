<?php

require_once '..' . DIRECTORY_SEPARATOR . 'config.php';

$req = substr(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), 1);
$parts = explode('/', $req);

$data = new UGRMData(new \SplFileInfo(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'usergroup'));

$e = function ($str) {
    echo htmlspecialchars($str);
};

$l = function ($str) {
    echo preg_replace('/^www\./', '', parse_url($str, PHP_URL_HOST));
};

use dflydev\markdown\MarkdownParser;

$markdownParser = new MarkdownParser();
$m = function ($str) use ($markdownParser) {
    echo $markdownParser->transformMarkdown($str);
};

$nick = function (Usergroup $group) use ($e) {
    if ($group->nickname): ?>
    <abbr title="<?php $e($group->name); ?>"><?php $e($group->nickname); ?></abbr>
        <?php else:
        $e($group->name);
    endif;
};

?><!doctype html>
<html lang="de-de">
<head>
    <meta charset="utf-8">
    <title>UGRM &ndash; Usergroups RheinMain</title>
    <meta name="description" content="Beschreibung und Termine der Technologie-Usergroups im Rhein-Main-Gebiet.">
    <meta name="author" content="Markus Tacker | http://coderbyheart.de/">
    <!-- See /humans.txt for more infos -->
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="/build/complete-min.<?php echo filemtime(__DIR__ . DIRECTORY_SEPARATOR . 'build' . DIRECTORY_SEPARATOR . 'complete-min.css'); ?>.css" type="text/css">
    <!--
    <link rel="stylesheet" href="/assets/styles.css" type="text/css">
    -->
</head>
<body>
<header>
    <h1>
        <a href="/" rel="index"><img src="/build/logo.png" alt="UGRM &ndash; Usergroups RheinMain"></a>
    </h1>
    <nav>
        <a href="#termine" class="mobile">Termine</a> <a href="#usergroups" class="mobile">Usergroups</a>
        <a href="#tags" class="mobile">Tags</a> <a href="http://coderbyheart.de/blog/usergroups-rheinmain">Was ist
        das?</a> <a href="http://github.com/tacker/ugrm-data/" class="desktop">Eintrag bearbeiten</a>
    </nav>
</header>
<div id="main">
    <div id="right">
        <?php
        $q = array();
        if ($parts[0] == 'tag' && isset($parts[1]) && !empty($parts[1])) $q['tag'] = urldecode($parts[1]);
        if ($parts[0] == 'usergroup' && isset($parts[1]) && !empty($parts[1])) $q['usergroup'] = $parts[1];
        $groups = $data->listGroups($q);
        $single = count($groups) === 1;
        foreach ($groups as $group): ?>
            <article class="usergroup <?php if ($single): ?>single<?php endif; ?>" itemscope itemtype="http://schema.org/Organization">
                <div class="description">
                    <h2><a href="/usergroup/<?php $e($group->id); ?>" itemprop="name"><?php $e($group->name); ?></a>
                        <?php if ($group->nickname): ?><small>(<?php $e($group->nickname); ?>)</small><?php endif; ?>
                    </h2>

                    <p itemprop="description"><?php $e($group->description); ?></p>

                    <?php if (count($group->sponsors) > 0): ?>
                    <div class="showsingle">
                        <h3><i class="icon-heart"></i> Sponsoren</h3>

                        <p>Die <?php $nick($group); ?> dankt ihren Sponsoren:</p>
                        <ul>
                            <?php foreach ($group->sponsors as $sponsor): ?>
                            <li>
                                <a href="<?php $e($sponsor->url); ?>"><?php $e($sponsor->name); ?></a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <?php
                    $meeting = $group->getFutureMeeting();
                    if ($meeting): ?>
                        <div itemscope itemtype="http://schema.org/Event" class="event" itemprop="event">
                            <h3><i class="icon-calendar"></i> Nächster
                                Termin:<br><span itemprop="description"><?php $e($meeting->name); ?></span> <?php echo strftime('am %A, %d. %B %Y um %H:%M Uhr', $meeting->time->format('U')); ?>
                            </h3>
                            <?php if ($meeting->description): ?>
                            <div><?php $m($meeting->description); ?></div>
                            <?php endif; ?>
                            <?php if ($meeting->url): ?>
                            <p>Details unter
                                <a href="<?php echo $meeting->url; ?>" itemprop="url"><?php $l($meeting->url); ?></a>
                            </p>
                            <?php endif; ?>
                            <span class="hidden" itemprop="name">Treffen der <?php $nick($group); ?>
                                <time datetime="<?php echo $meeting->time->format(DATE_ATOM); ?>" itemprop="startDate"><?php echo strftime('am %A, %d. %B %Y um %H:%M Uhr', $meeting->time->format('U')); ?></time>
                    </span>
                            <?php if ($meeting->location): ?>
                            <h4><i class="icon-map-marker"></i> Ort</h4>
                            <?php if ($meeting->location instanceof Location): ?>
                                <p itemprop="location" itemscope itemtype="http://schema.org/PostalAddress">
                                    <span itemprop="name"><?php $e($meeting->location->name); ?></span><br>
                                    <a href="https://maps.google.com/maps?q=<?php echo urlencode(sprintf("%s, %d %s, %s, %s (%s)", $meeting->location->street, $meeting->location->zip, $meeting->location->city, $meeting->location->region, $meeting->location->country, $meeting->location->name)); ?>">
                                        <span itemprop="streetAddress"><?php $e($meeting->location->street); ?></span>,
                                        <span itemprop="postalCode"><?php $e($meeting->location->zip); ?></span>
                                        <span itemprop="addressLocality"><?php $e($meeting->location->city); ?></span>
                                        <span itemprop="addressRegion" class="hidden"><?php $e($meeting->location->region); ?></span>
                                        <span itemprop="addressCountry" class="hidden"><?php $e($meeting->location->country); ?></span>
                                    </a>
                                    <?php if ($meeting->location->url): ?><br><i class="icon-home"></i>
                                    <a href="<?php echo $meeting->location->url; ?>" itemprop="url"><?php $l($meeting->location->url); ?></a><?php endif; ?>
                                    <?php if ($meeting->location->twitter): ?>
                                    <br>
                                    <i class="icon-twitter"></i>
                                    <a href="http://twitter.com/<?php echo substr($meeting->location->twitter, 1); ?>"><?php echo $meeting->location->twitter; ?>
                                    </a>
                                    <?php endif; ?>
                                </p>
                                <?php endif; // Location ?>
                            <?php if ($meeting->location instanceof SimpleLocation): ?>
                                <p itemprop="location"><?php $e($meeting->location->description); ?></p>
                                <?php endif; // SimpleLocation ?>
                            <?php endif; // $meeting->location ?>
                        </div>
                        <?php endif; ?>

                    <p class="hidesingle"><a href="/usergroup/<?php $e($group->id); ?>">Details …</a></p>

                </div>
                <aside>
                    <dl class="clearfix">
                        <?php if ($group->logo): ?>
                        <dt class="hidden">Logo</dt>
                        <dd>
                            <a href="<?php echo $group->url; ?>"><img src="/data/usergroup/<?php echo $group->logo; ?>" class="logo" alt="<?php $e($group->name); ?>" itemprop="logo"></a>
                        </dd>
                        <?php endif; ?>

                        <dt>Links</dt>
                        <dd>
                            <ul>
                                <li><i class="icon-home"></i>
                                    <a href="<?php echo $group->url; ?>" itemprop="url"><?php $l($group->url); ?></a>
                                </li>
                                <?php if ($group->twitter || $group->hashtag): ?>
                                <li><i class="icon-twitter"></i>
                                    <?php if ($group->twitter): ?>
                                        <a href="http://twitter.com/<?php echo substr($group->twitter, 1); ?>"><?php echo $group->twitter; ?>
                                        </a>
                                        <?php endif; ?>
                                    <?php if ($group->hashtag): ?>
                                        <a href="https://twitter.com/search?q=%23<?php echo urlencode(substr($group->hashtag, 1)); ?>"># <?php echo substr($group->hashtag, 1); ?></a>
                                        <?php endif; ?>
                                </li>
                                <?php endif; ?>
                                <?php if ($group->facebook): ?>
                                <li><i class="icon-facebook"></i> <a href="<?php $e($group->facebook); ?>">Facebook</a>
                                </li>
                                <?php endif; ?>
                                <?php if ($group->googleplus): ?>
                                <li><i class="icon-google-plus"></i>
                                    <a href="<?php $e($group->googleplus); ?>">Google+</a>
                                </li>
                                <?php endif; ?>
                                <?php if ($group->xing): ?>
                                <li><i class="icon-sign-blank"></i> <a href="<?php $e($group->xing); ?>">XING</a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </dd>

                        <?php if (count($group->mailinglists) > 0): ?>
                        <dt>
                            <i class="icon-envelope"></i> <?php echo count($group->mailinglists) > 1 ? 'Mailinglisten' : 'Mailingliste'; ?>
                        </dt>
                        <dd>
                            <ol>
                                <?php foreach ($group->mailinglists as $mailinglist): ?>
                                <li>
                                    <a href="<?php $e($mailinglist->url); ?>"><?php $e($mailinglist->label); ?></a>
                                    <?php if ($mailinglist->description): ?><br><small><?php $e($mailinglist->description); ?></small><?php endif; ?>
                                </li>
                                <?php endforeach; ?>
                            </ol>
                        </dd>
                        <?php endif; ?>
                    </dl>
                    <dl>
                        <?php if ($group->group): ?>
                        <dt class="showsingle">Gruppenfoto</dt>
                        <dd class="showsingle">
                            <img src="/data/usergroup/<?php echo $group->group; ?>" class="group" alt="<?php $e($group->name); ?>" itemprop="image">
                        </dd>
                        <?php endif; ?>

                        <dt class="showsingle">Eintrag bearbeiten</dt>
                        <dd class="showsingle">
                            <i class="icon-github"></i>
                            <a href="https://github.com/tacker/ugrm-data/tree/master/usergroup/<?php $e($group->id); ?>.xml"><?php $e(sprintf("%s.xml", $group->id)); ?></a>
                            auf GitHub
                        </dd>
                    </dl>
                    <?php if ($group->logo_credit || $group->group_credit): ?>
                    <?php if ($group->logo_credit): ?>
                        <div class="small showsingle"><?php $m($group->logo_credit); ?></div>
                        <?php endif; ?>
                    <?php if ($group->group_credit): ?>
                        <div class="small showsingle"><?php $m($group->group_credit); ?></div>
                        <?php endif; ?>
                    <?php endif; ?>
                </aside>
            </article>
            <?php endforeach; ?>
    </div>
    <aside id="left">
        <?php $meetings = $data->getMeetings();
        if ($meetings): ?>
            <h2 id="termine">Termine</h2>
            <ul>
                <?php foreach ($meetings as $meeting): ?>
                <li>
                    <a href="/usergroup/<?php $e($meeting->usergroup->id); ?>">
                        <time datetime="<?php echo $meeting->time->format(DATE_ATOM); ?>"><?php echo strftime('%a, %d. %B %Y, %H:%M Uhr', $meeting->time->format('U')); ?></time>
                    </a><br>Treffen der <a href="/usergroup/<?php $e($meeting->usergroup->id); ?>">
                    <?php $nick($meeting->usergroup); ?>
                </a>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        <?php $groups = $data->listGroups();
        usort($groups, function (Usergroup $a, Usergroup $b) {
            return strcmp(preg_replace('/[^a-z0-9]/i', '', $a->name), preg_replace('/[^a-z0-9]/i', '', $b->name));
        });
        ?>
        <h2 id="usergroups">Usergroups
            <small>(<?php $e(count($groups)); ?>)</small>
        </h2>
        <ul class="compact">
            <?php foreach ($groups as $group): ?>
            <li>
                <a href="/usergroup/<?php $e($group->id); ?>">
                    <?php $e($group->name); ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <h2 id="tags">Tags</h2>
        <nav class="tags">
            <?php foreach ($data->getTags() as $tag): ?>
            <a href="/tag/<?php echo urlencode($tag['name']); ?>" data-count="<?php echo $tag['count']; ?>"><?php $e($tag['name']); ?></a>
            <?php endforeach; ?>
        </nav>
    </aside>
</div>
<footer>
    <p><a href="/" rel="index">Usergroups RheinMain</a> ist ein Projekt von <a href="http://tckr.cc" rel="author">Markus
        Tacker</a>.</p>

    <p>Der Quellcode für dieses Projekt <a href="http://github.com/tacker/ugrm">findet sich auf GitHub</a>.</p>
</footer>
</body>
</html>
