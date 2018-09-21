<?php

namespace App\Controller;

use App\Entity\RepeatableInterviewerSlot;
use App\Service\RepeatableInterviewerSlotService;
use App\Service\ResponseErrorDecoratorService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RepeatableInterviewerSlotController extends Controller
{
    /**
     * Creates new repeatable interviewer slots by passed JSON data
     *
     * @Route("/api/repeatable-interviewer-slots", methods={"POST"})
     * @param Request $request
     * @param RepeatableInterviewerSlotService $repeatableInterviewerSlotService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse Data containing just created slots or error
     */
    public function createRepeatableInterviewerSlots(
        Request $request,
        RepeatableInterviewerSlotService $repeatableInterviewerSlotService,
        ResponseErrorDecoratorService $errorDecorator
    )
    {
        $body = $request->getContent();
        $data = json_decode($body, true);
        if (is_null($data) || !isset($data['interviewer_id']) || !isset($data['repeatable_interviewer_slots'])) {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError(
                JsonResponse::HTTP_BAD_REQUEST, "Invalid JSON format"
            );
            return new JsonResponse($data, $status);
        }

        $result = $repeatableInterviewerSlotService->createRepeatableInterviewerSlots($data);

        if (is_array($result)) {
            $status = JsonResponse::HTTP_CREATED;

            $slots = [];
            foreach ($result as $slot) {
                $slots[] = [
                    'id' => $slot->getId(),
                    'interviewer_id' => $slot->getInterviewer()->getId(),
                    'day_number' => $slot->getWeekday(),
                    'start_time' => $slot->getHour()
                ];
            }

            $data = [
                'data' => [
                    'repeatable_interviewer_slots' => $slots
                ]
            ];
        } else {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError($status, $result);
        }

        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/api/repeatable-interviewer-slot/{id}", methods={"DELETE"})
     * @param RepeatableInterviewerSlot $repeatableInterviewerSlot
     * @param RepeatableInterviewerSlotService $repeatableInterviewerSlotService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function deleteRepeatableInterviewerSlot(
        RepeatableInterviewerSlot $repeatableInterviewerSlot,
        RepeatableInterviewerSlotService $repeatableInterviewerSlotService,
        ResponseErrorDecoratorService $errorDecorator
    )
    {
        $result = $repeatableInterviewerSlotService->deleteRepeatableInterviewerSlot($repeatableInterviewerSlot);
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