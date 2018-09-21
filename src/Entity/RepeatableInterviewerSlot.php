<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="repeatable_interviewer_slot",
 *     uniqueConstraints={@UniqueConstraint(name="daytime", columns={"weekday", "hour"})}
 * )
 */
class RepeatableInterviewerSlot
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Interviewer", inversedBy="repeatableInterviewerSlots")
     * @ORM\JoinColumn(nullable=false)
     */
    private $interviewer;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint")
     */
    private $weekday;

    /**
     * @ORM\Column(type="smallint")
     */
    private $hour;

    /**
     * @return Interviewer
     */
    public function getInterviewer(): Interviewer
    {
        return $this->interviewer;
    }

    /**
     * @param Interviewer $interviewer
     */
    public function setInterviewer($interviewer): void
    {
        $this->interviewer = $interviewer;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getWeekday(): int
    {
        return $this->weekday;
    }

    /**
     * @param int $weekday
     */
    public function setWeekday($weekday): void
    {
        $this->weekday = $weekday;
    }

    /**
     * @return int
     */
    public function getHour(): int
    {
        return $this->hour;
    }

    /**
     * @param int $hour
     */
    public function setHour($hour): void
    {
        $this->hour = $hour;
    }


}