<?php

namespace App\Command;

use App\Controller\EmailController;
use App\Controller\RaportController;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'generateAndSendReports',
    description: 'This command is called every 15 minutes to check if there is any report to send',
)]
class GenerateAndSendReportsCommand extends Command
{

    private $emailController;
    private $raportController;

    public function __construct(EmailController $emailController, RaportController $raportController)
    {
        parent::__construct();
        $this->emailController = $emailController;
        $this->raportController = $raportController;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        echo 'test';
        date_default_timezone_set('Europe/Warsaw');
        $currentDateTime = new \DateTime("now");

        $reports = $this->raportController->generujRaporty();
        $sheets = $reports['reports'];
        $teachers = $reports['teachers'];

        for ($i = 0; $i < count($teachers); $i++) {
            $this->emailController->sendEmail(
                file: $sheets[$i]
            );
        }

        return Command::SUCCESS;
    }
}

