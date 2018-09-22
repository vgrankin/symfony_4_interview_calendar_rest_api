<?php

namespace App\Service;

use App\Entity\Candidate;
use App\Entity\CandidateSlot;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;


class CandidateSlotService extends BaseService
{
    /**
     * Create candidate slot(s) by given data
     *
     * @param $data array which contains information about candidate slots
     *    $data = [
     *      'candidate_id' => (int) Candidate id. Required.
     *      'candidate_slots' => (array) Array of candidate slots. Required.
     *    ]
     *
     *    Repeatable candidate slots structure example:
     *      $data['candidate_slots'] = [
     *           ['date' => '2018-09-01 10:00 AM'],
     *           ['date' => '2018-09-01 11:00 PM'],
     *           ['date' => '2018-09-15 01:00 AM'],
     *           ['date' => '2018-10-30 04:00 PM'],
     *      ];
     *
     * @return CandidateSlot[]|string Array of CandidateSlot objects or error message
     */
    public function createCandidateSlots(array $data)
    {
        $violations = $this->getCreateCandidateSlotsViolations($data);
        if (sizeof($violations)) {
            return $this->getErrorsStr($violations);
        }

        try {
            $candidate = $this->em
                ->getRepository(Candidate::class)
                ->find($data['candidate_id']);
            if (!$candidate) {
                return "Unable to find candidate by given candidate_id";
            }

            $singleCandidateSlots = [];
            foreach ($data['candidate_slots'] as $slot) {

                $singleCandidateSlot = new CandidateSlot();
                $singleCandidateSlot->setCandidate($candidate);
                $singleCandidateSlot->setDate($slot['date']);

                $singleCandidateSlots[] = $singleCandidateSlot;

                $this->em->persist($singleCandidateSlot);
            }

            $this->em->flush();

            return $singleCandidateSlots;
        } catch (\Exception $ex) {
            return "Unable to create candidate slots (possibly some slot already exists)";
        }
    }

    private function getCreateCandidateSlotsViolations($data)
    {
        $violationsFinal = [];

        $rules = [
            "candidate_id" => new Assert\Type(["type" => "integer", "message" => "Unexpected value: `{{ value }}` for`candidate_id`"]),
        ];
        $violations = $this->getViolations($data, $rules);
        foreach ($violations as $violation) {
            $violationsFinal[] = $violation;
        }

        foreach ($data['candidate_slots'] as $key => $entry) {
            $rules = [
                'date' => new Assert\DateTime(['format' => 'Y-m-d h:i A', 'message' => "Provided `date` value: `{{ value }}` is not valid"])
            ];
            $violations = $this->getViolations($entry, $rules);
            foreach ($violations as $violation) {
                $violationsFinal[] = $violation;
            }
        }

        return $violationsFinal;
    }

    /**
     * Remove candidate slot by given entity object
     *
     * @param CandidateSlot $singleCandidateSlot
     * @return bool|string True if CandidateSlot was successfully deleted, error message otherwise
     */
    public function deleteCandidateSlot(CandidateSlot $singleCandidateSlot)
    {
        try {
            $this->em->remove($singleCandidateSlot);
            $this->em->flush();
        } catch (\Exception $ex) {
            return "Unable to remove candidate slot";
        }
        return true;
    }
}