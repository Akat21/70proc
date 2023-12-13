<?php

namespace App\Entity;

use App\Repository\MeetingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MeetingRepository::class)]
#[ORM\UniqueConstraint(columns: ['room', 'start', 'teacher', 'name'])]
class Meeting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $room = null;

    #[ORM\Column(length: 255)]
    private ?string $teacher = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $stop = null;

    #[ORM\Column(nullable: true)]
    private ?float $scoreMeeting = null;

    #[ORM\Column(nullable: true)]
    private ?float $scoreTeacher = null;

    #[ORM\Column(nullable: true)]
    private ?float $scoreGeneralWeek = null;

    #[ORM\Column(nullable: true)]
    private ?float $scoreCourse = null;

    #[ORM\OneToMany(mappedBy: 'meeting', targetEntity: Opinion::class, orphanRemoval: true)]
    private Collection $opinions;

    public function __construct()
    {
        $this->opinions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoom(): ?string
    {
        return $this->room;
    }

    public function setRoom(string $room): static
    {
        $this->room = $room;

        return $this;
    }

    public function getTeacher(): ?string
    {
        return $this->teacher;
    }

    public function setTeacher(string $teacher): static
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): static
    {
        $this->start = $start;

        return $this;
    }

    public function getStop(): ?\DateTimeInterface
    {
        return $this->stop;
    }

    public function setStop(\DateTimeInterface $stop): static
    {
        $this->stop = $stop;

        return $this;
    }

    public function getScoreMeeting(): ?float
    {
        return $this->scoreMeeting;
    }

    public function setScoreMeeting(?float $scoreMeeting): static
    {
        $this->scoreMeeting = $scoreMeeting;

        return $this;
    }

    public function getScoreTeacher(): ?float
    {
        return $this->scoreTeacher;
    }

    public function setScoreTeacher(?float $scoreTeacher): static
    {
        $this->scoreTeacher = $scoreTeacher;

        return $this;
    }

    public function getScoreGeneralWeek(): ?float
    {
        return $this->scoreGeneralWeek;
    }

    public function setScoreGeneralWeek(?float $scoreGeneralWeek): static
    {
        $this->scoreGeneralWeek = $scoreGeneralWeek;

        return $this;
    }

    public function getScoreCourse(): ?float
    {
        return $this->scoreCourse;
    }

    public function setScoreCourse(?float $scoreCourse): static
    {
        $this->scoreCourse = $scoreCourse;

        return $this;
    }

    /**
     * @return Collection<int, Opinion>
     */
    public function getOpinions(): Collection
    {
        return $this->opinions;
    }

    public function addOpinion(Opinion $opinion): static
    {
        if (!$this->opinions->contains($opinion)) {
            $this->opinions->add($opinion);
            $opinion->setMeeting($this);
        }

        return $this;
    }

    public function removeOpinion(Opinion $opinion): static
    {
        if ($this->opinions->removeElement($opinion)) {
            // set the owning side to null (unless already changed)
            if ($opinion->getMeeting() === $this) {
                $opinion->setMeeting(null);
            }
        }

        return $this;
    }
}
