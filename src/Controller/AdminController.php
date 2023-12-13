<?php

namespace App\Controller;

use App\Repository\MeetingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(MeetingRepository $meetingsRepository): Response
    {
        $meetings = $meetingsRepository->findAll();
        return $this->render('admin/index.html.twig', [
            "meetings" => $meetings
        ]);
    }

}