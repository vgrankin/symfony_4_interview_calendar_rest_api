<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="candidate_slot")
 */
class CandidateSlot
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Candidate", inversedBy="candidateSlots")
     * @ORM\JoinColumn(nullable=false)
     */
    private $candidate;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", unique=true)
     */
    private $date;

    /**
     * @return Candidate
     */
    public function getCandidate(): Candidate
    {
        return $this->candidate;
    }

    /**
     * @param Candidate $candidate
     */
    public function setCandidate($candidate): void
    {
        $this->candidate = $candidate;
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