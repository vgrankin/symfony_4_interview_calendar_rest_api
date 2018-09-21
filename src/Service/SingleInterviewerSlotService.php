<?php

namespace App\Service;

use App\Entity\Interviewer;
use App\Entity\SingleInterviewerSlot;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;


class SingleInterviewerSlotService extends BaseService
{
    private $timeConverterService;

    public function __construct(EntityManagerInterface $em, TimeConverterService $timeConverterService)
    {
        $this->timeConverterService = $timeConverterService;
        parent::__construct($em);
    }

    /**
     * Create single interviewer slot(s) by given data
     *
     * @param $data array which contains information about single interviewer slots
     *    $data = [
     *      'interviewer_id' => (int) Interviewer id. Required.
     *      'single_interviewer_slots' => (array) Array of single interviewer slots. Required.
     *    ]
     *
     *    Repeatable interviewer slots structure example:
     *      $data['single_interviewer_slots'] = [
     *           ['date' => '2018-09-01 10:00 AM', 'is_blocked_slot' => false],
     *           ['date' => '2018-09-01 11:00 PM', 'is_blocked_slot' => true],
     *           ['date' => '2018-09-15 01:00 AM', 'is_blocked_slot' => false],
     *           ['date' => '2018-10-30 04:00 PM', 'is_blocked_slot' => false],
     *      ];
     *
     * @return SingleInterviewerSlot[]|string Array of SingleInterviewerSlot objects or error message
     */
    public function createSingleInterviewerSlots(array $data)
    {
        $violations = $this->getCreateSingleInterviewerSlotsViolations($data);
        if (sizeof($violations)) {
            return $this->getErrorsStr($violations);
        }

        try {
            $interviewer = $this->em
                ->getRepository(Interviewer::class)
                ->find($data['interviewer_id']);
            if (!$interviewer) {
                return "Unable to find interviewer by given interviewer_id";
            }

            $singleInterviewerSlots = [];
            foreach ($data['single_interviewer_slots'] as $slot) {

                $singleInterviewerSlot = new SingleInterviewerSlot();
                $singleInterviewerSlot->setInterviewer($interviewer);
                $singleInterviewerSlot->setDate($slot['date']);
                $singleInterviewerSlot->setIsBlockedSlot($slot['is_blocked_slot']);

                $singleInterviewerSlots[] = $singleInterviewerSlot;

                $this->em->persist($singleInterviewerSlot);
            }

            $this->em->flush();

            return $singleInterviewerSlots;
        } catch (\Exception $ex) {
            return "Unable to create single interviewer slots (possibly some slot already exists)";
        }
    }

    private function getCreateSingleInterviewerSlotsViolations($data)
    {
        $violationsFinal = [];

        $rules = [
            "interviewer_id" => new Assert\Type(["type" => "integer", "message" => "Unexpected value: `{{ value }}` for`interviewer_id`"]),
        ];
        $violations = $this->getViolations($data, $rules);
        foreach ($violations as $violation) {
            $violationsFinal[] = $violation;
        }

        foreach ($data['single_interviewer_slots'] as $key => $entry) {
            $rules = [
                'date' => new Assert\DateTime(['format' => 'Y-m-d h:i A', 'message' => "Provided `date` value: `{{ value }}` is not valid"]),
                'is_blocked_slot' => new Assert\Type([
                    'type' => "bool",
                    'message' => "Provided `is_blocked_slot` value: `{{ value }}` is not valid"
                ]),
            ];
            $violations = $this->getViolations($entry, $rules);
            foreach ($violations as $violation) {
                $violationsFinal[] = $violation;
            }
        }

        return $violationsFinal;
    }

    /**
     * Remove single interviewer slot(s) by given data
     *
     * @param $data array which contains information about single interviewer slots to remove
     *    $data = [
     *      'single_interviewer_slots' => (array) Array of single interviewer slot ids to remove. Required.
     *    ]
     *
     *    Repeatable interviewer slots structure example:
     *      $data['single_interviewer_slots'] = [1,2,4,6];
     *
     * @return bool|string True if slots were successfully deleted, error message otherwise
     */
    public function deleteSingleInterviewerSlots(array $data)
    {
        try {
            foreach ($data['single_interviewer_slots'] as $id) {
                $slot = $this->em->getReference(SingleInterviewerSlot::class, $id);
                $this->em->remove($slot);
            }

            $this->em->flush();

            return True;
        } catch (\Exception $ex) {
            return "Unable to delete single interviewer slots";
        }
    }

    /**
     * Remove single interviewer slot by given entity object
     *
     * @param SingleInterviewerSlot $singleInterviewerSlot
     * @return bool|string True if SingleInterviewerSlot was successfully deleted, error message otherwise
     */
    public function deleteSingleInterviewerSlot(SingleInterviewerSlot $singleInterviewerSlot)
    {
        try {
            $this->em->remove($singleInterviewerSlot);
            $this->em->flush();
        } catch (\Exception $ex) {
            return "Unable to remove single interviewer slot";
        }
        return true;
    }
}