<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

// odpowiedzi serwera bla bla bla
// ma php.ini default 30 nie zadziala
set_time_limit(300);

class EmailController extends AbstractController
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    // do zrobienia auto wysylanie, nie przez link
    #[Route('/email')]
    public function sendEmail($to = 'testai70test@gmail.com', $subject = 'Raport', $text = 'data i lekcja', $file = '') : Response
    {
        // $file to path do pliku względem main directory projektu
        // zamienianie na absolute path
//        $absPath = realpath($this->getParameter('kernel.project_dir') . '/' . $file);

        $email = (new Email())
            ->from('testai70test@gmail.com')
            ->to($to) // email prowadzacego
            ->subject($subject) // RAPORT
            ->text($text); // info o lekcji i dacie | text jest potrzebny w przypadku braku pliku inaczej sie wysadzi |

        if ($file) {
            $email->attachFromPath($file);
        }

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {}

        // wywalic po integracji z automatycznym wyslaniem
        return $this->render('base.html.twig', [
            'message' => 'email sent',
        ]);
    }
}