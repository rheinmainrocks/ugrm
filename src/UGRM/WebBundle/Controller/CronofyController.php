<?php

namespace UGRM\WebBundle\Controller;

use Guzzle\Http\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\RouterInterface;
use UGRM\DataBundle\UsergroupRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route(service="ugrm.web.controller.cronofy")
 */
class CronofyController
{
    /**
     * @var \UGRM\DataBundle\UsergroupRepository
     */
    private $repository;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(UsergroupRepository $repository, RouterInterface $router, $cronofy_client_id, $cronofy_client_secret)
    {
        $this->repository            = $repository;
        $this->router                = $router;
        $this->cronofy_client_id     = $cronofy_client_id;
        $this->cronofy_client_secret = $cronofy_client_secret;
    }

    /**
     * @Route("/cronofy_callback")
     * @param Request $request
     * @Template()
     */
    public function callbackAction(Request $request)
    {
        return [];
        $state = $request->get('state');

        // Add event to calendar
        list($ug, $startTime) = explode('-', $state);

        $meetingToAdd = false;
        $groups       = $this->repository->listGroups(array('usergroup' => $ug));
        $group        = array_shift($groups);
        foreach ($group->meetings as $meeting) {
            if ($meeting->time->format('U') == $startTime) {
                $meetingToAdd = $meeting;
                break;
            }
        }

        if (!$meetingToAdd) {
            throw new BadRequestHttpException();
        }

        $start = new \DateTime($meetingToAdd->time);
        $start->setTimezone(new \DateTimeZone('UTC'));
        $end = clone $start;
        $end->modify('+2 hours');

        // Request Access token
        $code                = $request->get('code');
        $client              = new Client();
        $accessTokenRequest  = $client->post(
            'https://api.cronofy.com/oauth/token',
            ['Content-Type' => 'application/json; charset=utf-8'],
            json_encode([
                "client_id"     => $this->cronofy_client_id,
                "client_secret" => $this->cronofy_client_secret,
                "grant_type"    => "authorization_code",
                "code"          => $code,
                "redirect_uri"  => $this->router->generate(
                    'ugrm_web_cronofy_callback',
                    [],
                    true
                )
            ])
        );
        $accessTokenResponse = $accessTokenRequest->send();

        $accessToken = json_decode($accessTokenResponse->getBody(true))->access_token;

        $listCalendarsResponse = $client->get(
            'https://api.cronofy.com/v1/calendars',
            ['Authorization' => 'Bearer ' . $accessToken]
        )->send();

        $calendar_id         = json_decode($listCalendarsResponse->getBody(true))->calendars[0]->calendar_id;
        $createEventResponse = $client->post(
            'https://api.cronofy.com/v1/calendars/' . urlencode($calendar_id) . '/events',
            [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json; charset=utf-8'
            ],
            json_encode([
                "event_id"    => $state,
                "summary"     => $meetingToAdd->name,
                "description" => $meetingToAdd->description,
                "start"       => $start->format('Y-m-d') . 'T' . $start->format('H:i:s') . 'Z',
                "end"         => $end->format('Y-m-d') . 'T' . $end->format('H:i:s') . 'Z',
                "location"    => ["description" => sprintf('%s, %s %s, %s', $meetingToAdd->location->street, $meetingToAdd->location->zip, $meetingToAdd->location->city, $meetingToAdd->location->country)]
            ])
        )->send();
        return [];
    }
}
