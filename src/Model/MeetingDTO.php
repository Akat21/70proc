<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Meeting;

class MeetingDTO
{
    public string $title;
    public string $description;
    public \DateTimeInterface $start;
    public \DateTimeInterface $end;
    public string $workerTitle;
    public string $worker;
    public ?string $workerCover;
    public string $lessonForm;
    public string $lessonFormShort;
    public string $groupName;
    public string $tokName;
    public string $room;
    public int $hours;
    public string $color;
    public string $borderColor;

    public static function fromArray($array): self
    {
        $dto = new static();

        $dto->title = $array['title'];
        $dto->description = $array['description'];
        $dto->start = new \DateTimeImmutable($array['start']);
        $dto->end = new \DateTimeImmutable($array['end']);
        $dto->workerTitle = $array['worker_title'];
        $dto->worker = $array['worker'];
        $dto->workerCover = $array['worker_cover'];
        $dto->lessonForm = $array['lesson_form'];
        $dto->lessonFormShort = $array['lesson_form_short'];
        $dto->groupName = $array['group_name'];
        $dto->tokName = $array['tok_name'];
        $dto->room = $array['room'];
        $dto->hours = (int) $array['hours'];
        $dto->color = $array['color'];
        $dto->borderColor = $array['borderColor'];

        return $dto;
    }

    public function buildMeeting(): Meeting
    {
        $meeting = new Meeting();
        $meeting
            ->setName($this->title)
            ->setRoom($this->room)
            ->setStart($this->start)
            ->setStop($this->end)
            ->setTeacher($this->worker)
        ;

        return $meeting;
    }
}
