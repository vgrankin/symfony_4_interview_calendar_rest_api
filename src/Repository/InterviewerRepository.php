<?php

namespace App\Repository;

use App\Entity\Interviewer;
use Doctrine\ORM\EntityRepository;

class InterviewerRepository extends EntityRepository
{
    /**
     *
     * @param array $filter Array of filters to use during interviewers selection
     *
     *    Filter example (all keys are optional):
     *
     *    $filter = [
     *      'interviewer-ids' => (array) Interviewer ids. Optional.
     *    ]
     * @return Interviewer[] Array of Listing objects
     */
    public function findAllFiltered(array $filter): array
    {
        $qb = $this->createQueryBuilder('i');
        if (sizeof($filter)) {
            if (isset($filter['interviewer-ids'])) {
                $qb->andWhere('i.id IN (:interviewer-ids)')
                    ->setParameter('interviewer-ids', implode(",", $filter['interviewer-ids']));
            }
        }

        return $qb->getQuery()->execute();
    }
}