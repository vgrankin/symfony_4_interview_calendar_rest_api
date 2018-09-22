<?php

namespace App\Controller;

use App\Entity\CandidateSlot;
use App\Service\CandidateSlotService;
use App\Service\ResponseErrorDecoratorService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CandidateSlotController extends Controller
{
    /**
     * Creates new candidate slots by passed JSON data
     *
     * @Route("/api/candidate-slots", methods={"POST"})
     * @param Request $request
     * @param CandidateSlotService $candidateSlotService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse Data containing just created slots or error
     */
    public function createCandidateSlots(
        Request $request,
        CandidateSlotService $candidateSlotService,
        ResponseErrorDecoratorService $errorDecorator
    )
    {
        $body = $request->getContent();
        $data = json_decode($body, true);
        if (is_null($data) || !isset($data['candidate_id']) || !isset($data['candidate_slots'])) {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError(
                JsonResponse::HTTP_BAD_REQUEST, "Invalid JSON format"
            );
            return new JsonResponse($data, $status);
        }

        $result = $candidateSlotService->createCandidateSlots($data);

        if (is_array($result)) {
            $status = JsonResponse::HTTP_CREATED;

            $slots = [];
            foreach ($result as $slot) {
                $slots[] = [
                    'id' => $slot->getId(),
                    'candidate_id' => $slot->getCandidate()->getId(),
                    'date' => $slot->getDate()->format("Y-m-d h:i A"),
                ];
            }

            $data = [
                'data' => [
                    'candidate_slots' => $slots
                ]
            ];
        } else {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError($status, $result);
        }

        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/api/candidate-slot/{id}", methods={"DELETE"})
     * @param CandidateSlot $candidateSlot
     * @param CandidateSlotService $candidateSlotService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function deleteCandidateSlot(
        CandidateSlot $candidateSlot,
        CandidateSlotService $candidateSlotService,
        ResponseErrorDecoratorService $errorDecorator
    )
    {
        $result = $candidateSlotService->deleteCandidateSlot($candidateSlot);
        if ($result === true) {
            $status = JsonResponse::HTTP_NO_CONTENT;
            $data = null;
        } else {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError($status, $result);
        }
        return new JsonResponse($data, $status);
    }
}