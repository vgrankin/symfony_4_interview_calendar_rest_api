<?php

namespace App\Service;

use App\Entity\Interviewer;
use App\Entity\RepeatableInterviewerSlot;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;


class RepeatableInterviewerSlotService extends BaseService
{
    private $timeConverterService;

    public function __construct(EntityManagerInterface $em, TimeConverterService $timeConverterService)
    {
        $this->timeConverterService = $timeConverterService;
        parent::__construct($em);
    }

    /**
     * Create repeatable interviewer slot(s) by given data
     *
     * @param $data array which contains information about repeatable interviewer slots
     *    $data = [
     *      'interviewer_id' => (int) Interviewer id. Required.
     *      'repeatable_interviewer_slots' => (array) Array of repeatable interviewer slots. Required.
     *    ]
     *
     *    Repeatable interviewer slots structure example:
     *      $data['repeatable_interviewer_slots'] = [
     *           ['day_number' => 1, 'start_time' => '09:00 AM'],
     *           ['day_number' => 1, 'start_time' => '10:00 AM'],
     *           ['day_number' => 1, 'start_time' => '11:00 AM'],
     *           ['day_number' => 2, 'start_time' => '07:00 AM'],
     *           ['day_number' => 2, 'start_time' => '04:00 PM'],
     *           ['day_number' => 4, 'start_time' => '01:00 PM'],
     *           ['day_number' => 5, 'start_time' => '12:00 AM'],
     *           ['day_number' => 5, 'start_time' => '11:00 AM'],
     *           ['day_number' => 5, 'start_time' => '11:00 PM'],
     *      ];
     *
     * @return RepeatableInterviewerSlot[]|string Array of RepeatableInterviewerSlot objects or error message
     */
    public function createRepeatableInterviewerSlots(array $data)
    {
        $violations = $this->getCreateRepeatableInterviewerSlotsViolations($data);
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

            $repeatableInterviewerSlots = [];
            foreach ($data['repeatable_interviewer_slots'] as $slot) {
                $hours24 = $this->timeConverterService->convertTo24H($slot['start_time']);
                $hour = (int)$this->timeConverterService->extractHour($hours24);

                $repeatableInterviewerSlot = new RepeatableInterviewerSlot();
                $repeatableInterviewerSlot->setInterviewer($interviewer);
                $repeatableInterviewerSlot->setWeekday($slot['day_number']);
                $repeatableInterviewerSlot->setHour($hour);

                $repeatableInterviewerSlots[] = $repeatableInterviewerSlot;

                $this->em->persist($repeatableInterviewerSlot);
            }

            $this->em->flush();

            return $repeatableInterviewerSlots;
        } catch (\Exception $ex) {
            return "Unable to create repeatable interviewer slots (possibly some slot already exists)";
        }
    }

    private function getCreateRepeatableInterviewerSlotsViolations($data)
    {
        $violationsFinal = [];

        $rules = [
            "interviewer_id" => new Assert\Type(["type" => "integer", "message" => "Unexpected value for`interviewer_id`"]),
        ];
        $violations = $this->getViolations($data, $rules);
        foreach ($violations as $violation) {
            $violationsFinal[] = $violation;
        }

        foreach ($data['repeatable_interviewer_slots'] as $key => $entry) {
            $rules = [
                'day_number' => new Assert\Length(['min' => 1, 'max' => 7]),
                'start_time' => new Assert\Regex([
                    'pattern' => "/\b((1[0-2]|0?[1-9]):([0][0]) ([AaPp][Mm]))/",
                    'message' => "Provided `start_time` value: `{$entry['start_time']}` is not valid"
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
     * Remove repeatable interviewer slot(s) by given data
     *
     * @param $data array which contains information about repeatable interviewer slots to remove
     *    $data = [
     *      'repeatable_interviewer_slots' => (array) Array of repeatable interviewer slot ids to remove. Required.
     *    ]
     *
     *    Repeatable interviewer slots structure example:
     *      $data['repeatable_interviewer_slots'] = [1,2,4,6];
     *
     * @return bool|string True if slots were successfully deleted, error message otherwise
     */
    public function deleteRepeatableInterviewerSlots(array $data)
    {
        try {
            foreach ($data['repeatable_interviewer_slots'] as $id) {
                $slot = $this->em->getReference(RepeatableInterviewerSlot::class, $id);
                $this->em->remove($slot);
            }

            $this->em->flush();

            return True;
        } catch (\Exception $ex) {
            return "Unable to delete repeatable interviewer slots";
        }
    }

    /**
     * Remove repeatable interviewer slot by given entity object
     *
     * @param RepeatableInterviewerSlot $repeatableInterviewerSlot
     * @return bool|string True if RepeatableInterviewerSlot was successfully deleted, error message otherwise
     */
    public function deleteRepeatableInterviewerSlot(RepeatableInterviewerSlot $repeatableInterviewerSlot)
    {
        try {
            $this->em->remove($repeatableInterviewerSlot);
            $this->em->flush();
        } catch (\Exception $ex) {
            return "Unable to remove repeatable interviewer slot";
        }
        return true;
    }
}