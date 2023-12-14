<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
class QRController extends AbstractController
{
    public function redirectToRoom(Request $request, $number, $building)
    {
        date_default_timezone_set('Europe/Warsaw');
        // Pobierz aktualną datę
        $currentDate = date('Y-m-d');
        // Pobierz aktualną godzinę
        $currentHour = date('H:i');
        // Zbuduj URL docelowe
        $targetUrl = "/room/WI%20{$building}-%20{$number}?now={$currentDate}%20{$currentHour}";

        // Wykonaj przekierowanie
        return new RedirectResponse($targetUrl);
    }

}