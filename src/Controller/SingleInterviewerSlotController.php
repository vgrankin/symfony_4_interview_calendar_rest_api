<?php

namespace App\Controller;

use App\Entity\SingleInterviewerSlot;
use App\Service\SingleInterviewerSlotService;
use App\Service\ResponseErrorDecoratorService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SingleInterviewerSlotController extends Controller
{
    /**
     * Creates new single interviewer slots by passed JSON data
     *
     * @Route("/api/single-interviewer-slots", methods={"POST"})
     * @param Request $request
     * @param SingleInterviewerSlotService $singleInterviewerSlotService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse Data containing just created slots or error
     */
    public function createSingleInterviewerSlots(
        Request $request,
        SingleInterviewerSlotService $singleInterviewerSlotService,
        ResponseErrorDecoratorService $errorDecorator
    )
    {
        $body = $request->getContent();
        $data = json_decode($body, true);
        if (is_null($data) || !isset($data['interviewer_id']) || !isset($data['single_interviewer_slots'])) {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError(
                JsonResponse::HTTP_BAD_REQUEST, "Invalid JSON format"
            );
            return new JsonResponse($data, $status);
        }

        $result = $singleInterviewerSlotService->createSingleInterviewerSlots($data);

        if (is_array($result)) {
            $status = JsonResponse::HTTP_CREATED;

            $slots = [];
            foreach ($result as $slot) {
                $slots[] = [
                    'id' => $slot->getId(),
                    'interviewer_id' => $slot->getInterviewer()->getId(),
                    'date' => $slot->getDate()->format("Y-m-d h:i A"),
                    'is_blocked_slot' => $slot->getIsBlockedSlot()
                ];
            }

            $data = [
                'data' => [
                    'single_interviewer_slots' => $slots
                ]
            ];
        } else {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError($status, $result);
        }

        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/api/single-interviewer-slot/{id}", methods={"DELETE"})
     * @param SingleInterviewerSlot $singleInterviewerSlot
     * @param SingleInterviewerSlotService $singleInterviewerSlotService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function deleteSingleInterviewerSlot(
        SingleInterviewerSlot $singleInterviewerSlot,
        SingleInterviewerSlotService $singleInterviewerSlotService,
        ResponseErrorDecoratorService $errorDecorator
    )
    {
        $result = $singleInterviewerSlotService->deleteSingleInterviewerSlot($singleInterviewerSlot);
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