<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\MeetingDTO;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PlanHelper
{
    public function __construct(
        private HttpClientInterface $planClient,
        private RouterInterface $router,
    )
    {
    }

    public function getMeetingInRoom(
        string $room,
        \DateTimeInterface $now = new \DateTime()
    ): ?MeetingDTO
    {
        $url = $this->generateUrlForMeetingsInRoom($room, $now);

        $response = $this->planClient->request('GET', $url);
        $meetingsArray = $response->toArray(true);

        $meetingDtos = [];
        foreach ($meetingsArray as $meetingArray) {
            if (empty($meetingArray)) {
                continue;
            }

            $meetingDtos[] = MeetingDTO::fromArray($meetingArray);
        }

        $dto = $this->getClosestMeeting($meetingDtos, $now);

        return $dto;
    }

    private function getClosestMeeting(array $meetingDtos, \DateTimeInterface $now): ?MeetingDTO
    {
        // sort meetings by start date
        usort($meetingDtos, fn(MeetingDTO $a, MeetingDTO $b) => $a->start <=> $b->start);

        $candidate = null;

        // return the first meeting that begins before now and ends no later than 15 minutes ago
        foreach ($meetingDtos as $meetingDto) {
            if ($meetingDto->start > $now) {
                continue;
            }

            $extendedEnd = new \DateTime($meetingDto->end->format('Y-m-d H:i:s'));
            $extendedEnd->modify('+15 minutes');
            if ($now > $extendedEnd) {
                continue;
            }

            $candidate = $meetingDto;
            break;
        }

        return $candidate;
    }


    public function generateUrlForMeetingsInRoom(string $room, \DateTimeInterface $now): string
    {
        $start = $now->format('Y-m-d');
        $end = date('Y-m-d', $now->getTimestamp() + 3600 * 24);

        $url = $this->router->generate('plan_room', [
            'room' => $room,
            'start' => $start,
            'end' => $end,
        ]);

        return $url;
    }
}
