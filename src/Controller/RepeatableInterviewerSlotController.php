<?php

namespace App\Controller;

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
     * @return JsonResponse Data containing just created slots
     */
    public function createRepeatableInterviewerSlots(
        Request $request,
        RepeatableInterviewerSlotService $repeatableInterviewerSlotService,
        ResponseErrorDecoratorService $errorDecorator
    )
    {

    }

    /**
     * Removes repeatable interviewer slots by passed JSON data
     *
     * @Route("/api/repeatable-interviewer-slots", methods={"DELETE"})
     * @param Request $request
     * @param RepeatableInterviewerSlotService $repeatableInterviewerSlotService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function deleteRepeatableInterviewerSlots(
        Request $request,
        RepeatableInterviewerSlotService $repeatableInterviewerSlotService,
        ResponseErrorDecoratorService $errorDecorator
    )
    {

    }

    /**
     * Gets repeatable interviewer slots optionally filtered by interviewer id
     *
     * Here is usage example:
     * url: http://localhost:8000/api/repeatable-interviewer-slots?interviewer_id=1
     * (where interviewer_id is id of the interviewer we want to filter by)
     *
     * @Route("/api/repeatable-interviewer-slots", methods={"GET"})
     * @param RepeatableInterviewerSlotService $repeatableInterviewerSlotService
     * @return JsonResponse List of repeatable interviewer slots
     */
    public function getRepeatableInterviewerSlots(RepeatableInterviewerSlotService $repeatableInterviewerSlotService)
    {

    }
}