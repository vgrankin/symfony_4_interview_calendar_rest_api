<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="single_interviewer_slot",
 *     uniqueConstraints={@UniqueConstraint(name="singleslot", columns={"date", "is_blocked_slot"})}
 * )
 */
class SingleInterviewerSlot
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Interviewer", inversedBy="singleInterviewerSlots")
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
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(name="is_blocked_slot", type="boolean")
     */
    private $isBlockedSlot;

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
     * @return bool
     */
    public function getIsBlockedSlot(): bool
    {
        return $this->isBlockedSlot;
    }

    /**
     * @param bool $isBlockedSlot
     */
    public function setIsBlockedSlot($isBlockedSlot): void
    {
        $this->isBlockedSlot = $isBlockedSlot;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param string $date
     */
    public function setDate($date): void
    {
        $format = "Y-m-d H:i A";
        $date = \DateTime::createFromFormat($format, $date);
        $this->date = $date;
    }
}