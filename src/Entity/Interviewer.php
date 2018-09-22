<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InterviewerRepository")
 * @ORM\Table(name="interviewer")
 */
class Interviewer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=191, unique=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RepeatableInterviewerSlot", mappedBy="interviewer")
     * @ORM\OrderBy({"weekday"="ASC", "hour"="ASC"})
     */
    private $repeatableInterviewerSlots;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SingleInterviewerSlot", mappedBy="interviewer")
     * @ORM\OrderBy({"date"="ASC"})
     */
    private $singleInterviewerSlots;

    public function __construct()
    {
        // php-array on steroids
        $this->repeatableInterviewerSlots = new ArrayCollection();
        $this->singleInterviewerSlots = new ArrayCollection();
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getRepeatableInterviewerSlots()
    {
        return $this->repeatableInterviewerSlots;
    }

    /**
     * @return mixed
     */
    public function getSingleInterviewerSlots()
    {
        return $this->singleInterviewerSlots;
    }


}