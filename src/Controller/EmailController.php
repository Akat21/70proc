<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

# odpowiedzi serwera bla bla bla
set_time_limit(300);

class EmailController extends AbstractController
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    #[Route('/email')]
    public function sendEmail($to = 'testai70test@gmail.com', $subject = 'Raport', $text = 'data i lekcja', $file = '') : Response
    {
        $absPath = realpath($this->getParameter('kernel.project_dir') . '/' . $file);

        $email = (new Email())
            ->from('testai70test@gmail.com')
            ->to($to)
            ->subject($subject)
            ->text($text);

        if ($file) {
            $email->attachFromPath($absPath);
        }

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {}

        return $this->render('base.html.twig', [
            'message' => 'email sent',
        ]);
    }
}