<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Connection;
use App\Entity\Opinion;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RaportController extends AbstractController
{
    public function generujRaport(EntityManagerInterface $entityManager, Connection $connection, $number): Response
    {
        // Pobierz dane z tabeli meeting
        $currentDateTime = new \DateTime();
        $currentDateTime->modify('-2 hour -30 minutes');

        $meetings = $connection->fetchAllAssociative('SELECT * FROM meeting WHERE start >= :currentDateTime AND SUBSTRING(room, -3) = :number ORDER BY start DESC', [
            'currentDateTime' => $currentDateTime->format('Y-m-d H:i:s'),
            'number' => $number
        ]);
        
        $raportData = [];
        // Pobierz dane z tabeli opinion
        foreach ($meetings as $meeting) {
            $opinions = $entityManager->getRepository(Opinion::class)->findBy(['meeting' => $meeting['id']]);
            
            // Przygotuj dane do raportu
            $raportData[] = [
                'data' => $meeting['start'],
                'nazwa_zajec' => $meeting['name'],
                'prowadzacy' => $meeting['teacher'],
                'sala' => $meeting['room'],
                'ocena_ogolna' => $this->obliczSredniaOcene($opinions),
                'ocena_semestralna' => $this->obliczSredniaOceneSemestralna($opinions),
                'ocena_zajec' => $this->obliczSredniaOceneZajec($opinions),
                'porownanie_poprzednich' => $this->porownajOcenyPoprzednich($opinions),
                'opinie_tekstowe' => $this->getOpinieTekstowe($opinions),
            ];
        }


        // Utwórz nowy arkusz kalkulacyjny
        $spreadsheet = new Spreadsheet();

        // Utwórz arkusz
        $sheet = $spreadsheet->getActiveSheet();

        // Ustaw nagłówki
        $sheet->setCellValue('A1', 'Data');
        $sheet->setCellValue('B1', 'Nazwa Zajęć');
        $sheet->setCellValue('C1', 'Prowadzący');
        $sheet->setCellValue('D1', 'Sala');
        $sheet->setCellValue('E1', 'Ocena Ogólna');
        $sheet->setCellValue('F1', 'Ocena Semestralna');
        $sheet->setCellValue('G1', 'Ocena z Zajęć');
        $sheet->setCellValue('H1', 'Porównanie z Poprzednimi');
        $sheet->setCellValue('I1', 'Opinie Tekstowe');

        // Wypełnij arkusz danymi
        $row = 2;
        foreach ($raportData as $data) {
            $sheet->setCellValue('A' . $row, $data['data']);
            $sheet->setCellValue('B' . $row, $data['nazwa_zajec']);
            $sheet->setCellValue('C' . $row, $data['prowadzacy']);
            $sheet->setCellValue('D' . $row, $data['sala']);
            $sheet->setCellValue('E' . $row, $data['ocena_ogolna']);
            $sheet->setCellValue('F' . $row, $data['ocena_semestralna']);
            $sheet->setCellValue('G' . $row, $data['ocena_zajec']);
            $sheet->setCellValue('H' . $row, $data['porownanie_poprzednich']);
            $sheet->setCellValue('I' . $row, $data['opinie_tekstowe']);
            $row++;
        }

        // Utwórz plik XLSX
        $xlsxFilePath = tempnam(sys_get_temp_dir(), 'raport') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($xlsxFilePath);

        // Przygotuj odpowiedź HTTP z plikiem
        $response = new BinaryFileResponse($xlsxFilePath);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename=raport.xlsx');

        return $response;
    }


    private function obliczSredniaOcene(array $opinions): float
    {
        // Przykładowa implementacja obliczania średniej oceny ogólnej
        // (analogicznie dostosuj funkcje poniżej do swoich potrzeb)
        if (count($opinions) === 0) {
            return 0.0;
        }

        $totalScore = 0;
        foreach ($opinions as $opinion) {
            $totalScore += $opinion->getScore();
        }

        return $totalScore / count($opinions);
    }

    private function obliczSredniaOceneSemestralna(array $opinions): float
    {
        // Przykładowa implementacja obliczania średniej oceny semestralnej
        // (analogicznie dostosuj funkcje poniżej do swoich potrzeb)
        // Zakładam, że masz daty zajęć, aby odróżnić semestry
        // i przykładowo, ocenę z pierwszej połowy semestru można odróżnić przez datę
        $semestralOpinions = array_filter($opinions, function ($opinion) {
            // Zakładam, że masz daty zajęć, aby odróżnić semestry
            return $opinion->getMeeting() >= '2023-09-01' && $opinion->getMeeting() <= '2023-12-31';
        });

        return $this->obliczSredniaOcene($semestralOpinions);
    }

    private function obliczSredniaOceneZajec(array $opinions): float
    {
        // Przykładowa implementacja obliczania średniej oceny z ostatnich zajęć
        // (analogicznie dostosuj funkcje poniżej do swoich potrzeb)
        $lastMeetingDate = $this->getLastMeetingDate($opinions);

        $lastMeetingsOpinions = array_filter($opinions, function ($opinion) use ($lastMeetingDate) {
            return $opinion->getMeeting() === $lastMeetingDate;
        });

        return $this->obliczSredniaOcene($lastMeetingsOpinions);
    }

    private function getLastMeetingDate(array $opinions): ?\DateTime
    {
        // Przykładowa implementacja pobierania daty ostatnich zajęć
        $meetingDates = array_map(function ($opinion) {
            // Assuming there's a getDate method in the Meeting entity
            return $opinion->getMeeting()->getStart();
        }, $opinions);

        // Ensure there are dates before attempting to find the maximum
        if (empty($meetingDates)) {
            return null;
        }

        // Find the maximum date
        $maxDate = max($meetingDates);

        return $maxDate;
    }


    private function porownajOcenyPoprzednich(array $opinions): string
    {
        // Przykładowa implementacja porównania ocen z poprzednimi zajęciami
        // (analogicznie dostosuj funkcje poniżej do swoich potrzeb)
        $previousMeetingsOpinions = array_filter($opinions, function ($opinion) {
            return $opinion->getMeeting() < '2023-01-01';
        });

        $currentMeetingsOpinions = array_filter($opinions, function ($opinion) {
            return $opinion->getMeeting() >= '2023-01-01';
        });

        $previousAverage = $this->obliczSredniaOcene($previousMeetingsOpinions);
        $currentAverage = $this->obliczSredniaOcene($currentMeetingsOpinions);

        $comparison = $currentAverage > $previousAverage ? 'wzrost' : ($currentAverage < $previousAverage ? 'spadek' : 'bez zmian');

        return "$comparison";
    }

    private function getOpinieTekstowe(array $opinions): string
    {
        // Przykładowa implementacja pobierania opinii tekstowych
        // (analogicznie dostosuj funkcje poniżej do swoich potrzeb)
        $opinionsText = array_map(function ($opinion) {
            return $opinion->getInfo();
        }, $opinions);

        return implode("\n", $opinionsText);
    }
}
