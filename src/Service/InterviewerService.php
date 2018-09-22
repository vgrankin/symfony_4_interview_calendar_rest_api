<?php

namespace App\Service;

use App\Entity\Interviewer;

class InterviewerService extends BaseService
{
    /**
     * @param array $filter
     *
     *    $filter = [
     *      'interviewer-ids' => (string) Coma separated list of interviewer ids (). Optional.
     *    ]
     *
     * @return Interviewer[]|string Array of interviewers or error message
     */
    public function getInterviewers(array $filter)
    {
        if (sizeof($filter)) {

            if (isset($filter['interviewer-ids'])) {
                $ids = explode(",", $filter['interviewer-ids']);
                foreach ($ids as $key => $val) {
                    $ids[$key] = (int)$val;
                }

                $filter['interviewer-ids'] = $ids;
            }
        }
        $interviewers = $this->em
            ->getRepository(Interviewer::class)
            ->findAllFiltered($filter);

        return $interviewers;
    }
}