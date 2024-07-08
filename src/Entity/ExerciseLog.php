<?php

namespace App\Entity;

use App\Repository\ExerciseLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExerciseLogRepository::class)]
class ExerciseLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $nr_reps = null;


    #[ORM\ManyToOne(inversedBy: 'exerciseLogs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Workout $workout = null;

    #[ORM\ManyToOne(inversedBy: 'exerciseLogs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Exercise $exercise = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $durata = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNrReps(): ?int
    {
        return $this->nr_reps;
    }

    public function setNrReps(int $nr_reps): static
    {
        $this->nr_reps = $nr_reps;

        return $this;
    }

    public function getDurata(): ?\DateTimeInterface
    {
        return $this->durata;
    }

    public function setDurata(\DateTimeInterface $durata): static
    {
        $this->durata = $durata;

        return $this;
    }

    public function getWorkout(): ?Workout
    {
        return $this->workout;
    }

    public function setWorkout(?Workout $workout): static
    {
        $this->workout = $workout;

        return $this;
    }

    public function getExercise(): ?Exercise
    {
        return $this->exercise;
    }

    public function setExercise(?Exercise $exercise): static
    {
        $this->exercise = $exercise;

        return $this;
    }
}
