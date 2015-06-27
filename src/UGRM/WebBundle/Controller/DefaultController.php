<?php

namespace UGRM\WebBundle\Controller;

use Carbon\Carbon;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UGRM\DataBundle\Model\Usergroup;
use UGRM\DataBundle\Model\Meeting as MeetingData;
use UGRM\DataBundle\UsergroupRepository;
use UGRM\WebBundle\Model\Meeting;
use UGRM\WebBundle\Model\MeetingTweet;

/**
 * @Route(service="ugrm.web.controller.default")
 */
class DefaultController
{
    /**
     * @var \UGRM\DataBundle\UsergroupRepository
     */
    private $repository;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    public function __construct(Request $request, UsergroupRepository $repository)
    {
        $this->request = $request;
        $this->repository = $repository;
    }

    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $groups = array();
        foreach ($this->repository->listGroups() as $g) {
            $g->meetings = $this->convertMeetings($g->meetings);
            $groups[] = $g;
        }
        return array(
            'groups' => $groups,
        );
    }

    /**
     * @Route("/liste")
     * @Template()
     */
    public function listAction()
    {
        $groups = $this->repository->listGroups(array('incubator' => false));
        $ugsort = function (Usergroup $a, Usergroup $b) {
            return strcmp(preg_replace('/[^a-z0-9]/i', '', $a->name), preg_replace('/[^a-z0-9]/i', '', $b->name));
        };
        usort($groups, $ugsort);
        return array(
            'groups' => $groups,
        );
    }

    /**
     * @Route("/~{ug}")
     * @Template()
     */
    public function usergroupAction($ug)
    {
        $groups = $this->repository->listGroups(array('usergroup' => $ug));
        $group = array_shift($groups);
        $group->meetings = $this->convertMeetings($group->meetings);
        return array(
            'group' => $group,
        );
    }


    /**
     * @Route("/termine")
     * @Template()
     */
    public function termineAction()
    {
        return array(
            'meetings' => $this->convertMeetings($this->repository->getMeetings())
        );
    }


    /**
     * @Route("/todayfeed")
     * @Template("UGRMWebBundle:Default:feed.xml.twig");
     */
    public function feedAction(Request $r)
    {
        $hours = 8;
        return array(
            'meetings' => array_map(function($m) use($hours) {
                $m->pubDate = $m->time->subHours($hours);
                return $m;
            }, $this->convertMeetings(array_filter($this->repository->getMeetings(), function(MeetingData $m) use($hours) {
                return Carbon::now()->diffInHours($m->time) < $hours;
            })))
        );
    }


    /**
     * @Route("/tags")
     * @Template()
     */
    public function tagsAction()
    {
        return array(
            'tags' => $this->repository->getTags(),
        );
    }


    /**
     * @Route("/tag/{tag}")
     * @Template()
     */
    public function tagAction($tag)
    {
        $groups = array();
        foreach ($this->repository->listGroups(array('tag' => $tag)) as $g) {
            $g->meetings = $this->convertMeetings($g->meetings);
            $groups[] = $g;
        }
        return array(
            'groups' => $groups,
        );
    }


    /**
     * @Route("/incubator")
     * @Template()
     */
    public function incubatorAction()
    {
        $groups = array();
        foreach ($this->repository->listGroups(array('incubator' => true)) as $g) {
            $g->meetings = $this->convertMeetings($g->meetings);
            $groups[] = $g;
        }
        return array(
            'groups' => $groups,
        );
    }

    protected function convertMeetings($meetings)
    {
        $viewMeetings = array();
        foreach ($meetings as $meeting) {
            $viewMeeting = new Meeting();
            $viewMeeting->description = $meeting->description;
            $viewMeeting->location = $meeting->location;
            $viewMeeting->name = $meeting->name;
            $viewMeeting->publictransport = $meeting->publictransport;
            $viewMeeting->time = $meeting->time;
            $viewMeeting->url = $meeting->url;
            $viewMeeting->usergroup = $meeting->usergroup;
            $mt = new MeetingTweet($meeting, $this->request->getHost());
            $viewMeeting->tweet = $mt->getLink();
            $viewMeeting->tweetText = $mt->getTweet();
            $viewMeetings[] = $viewMeeting;
        }
        return $viewMeetings;
    }
}
