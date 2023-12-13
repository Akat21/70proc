<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Meeting;
use App\Entity\Opinion;
use App\Form\OpinionType;
use App\Repository\MeetingRepository;
use App\Repository\OpinionRepository;
use App\Service\PlanHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class OpinionController extends AbstractController
{
    #[Route('/room/{room}')]
    public function forRoom(
        string $room,
        #[MapQueryParameter('now')] ?string $now,
        PlanHelper $planHelper,
        MeetingRepository $meetingRepository,
    ): Response
    {
        $now = new \DateTime($now ?? 'now');

        $meeting = $meetingRepository->findInRoom($room, $now);
        if (!$meeting) {
            $meetingDto = $planHelper->getMeetingInRoom($room, $now);
            if (!$meetingDto) {
                throw $this->createNotFoundException("Meeting in room $room not found at {$now->format('Y-m-d H:i:s')}");
            }
            $meeting = $meetingDto->buildMeeting();
            $meetingRepository->save($meeting, true);
        }

        return $this->redirectToRoute('app_opinion_formeeting', [
            'id' => $meeting->getId(),
        ]);
    }

    #[Route('/meeting/{id}')]
    public function forMeeting(
        Request $request,
        Meeting $meeting,
        OpinionRepository $opinionRepository,
    ): Response
    {
        if ($request->cookies->has("opinion_for_meeting_{$meeting->getId()}")) {
            return $this->redirectToRoute('app_opinion_thankyou');
        }

        $opinion = new Opinion();

        $form = $this->createForm(OpinionType::class, $opinion, [
            'validation_groups' => ['express'],
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $meeting->addOpinion($opinion);
            $opinion->setCreatedAt(new \DateTime());
            $opinionRepository->save($opinion, true);

            $response = $this->redirectToRoute('app_opinion_thankyou');
            $response->headers->setCookie(new Cookie("opinion_for_meeting_{$meeting->getId()}", 'complete', new \DateTime('+1 year')));
            return $response;
        }

        return $this->render('opinion/for_meeting.html.twig', [
            'meeting' => $meeting,
            'form' => $form,
        ]);
    }

    #[Route('/thank-you')]
    public function thankYou(): Response
    {
        return $this->render('thank_you.html.twig');
    }
}
