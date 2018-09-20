<?php

namespace App\Controller;


use App\Entity\Interviewer;
use App\Entity\RepeatableInterviewerSlot;
use App\Tests\BaseTestCase;

class RepeatableInterviewerSlotControllerTest extends BaseTestCase
{
    public function testCreateRepeatableInterviewerSlots____when_Creating_New_RepeatableInterviewerSlots____RepeatableInterviewerSlots_Are_Created_And_Returned_With_Correct_Response_Status()
    {
        $interviewer = $this->createTestInterviewer('Philipp');
        $this->assertTrue($interviewer instanceof Interviewer);

        $repeatableSlots = [
            ['day_number' => 1, 'start_time' => '09:00 AM'],
            ['day_number' => 1, 'start_time' => '10:00 AM'],
            ['day_number' => 1, 'start_time' => '11:00 AM'],
            ['day_number' => 2, 'start_time' => '07:00 AM'],
            ['day_number' => 2, 'start_time' => '04:00 PM'],
            ['day_number' => 4, 'start_time' => '01:00 PM'],
            ['day_number' => 5, 'start_time' => '12:00 AM'],
            ['day_number' => 5, 'start_time' => '15:00 AM'],
            ['day_number' => 5, 'start_time' => '18:00 PM'],
        ];
        $data = [
            'interviewer_id' => $interviewer->getId(),
            'repeatable_slots' => $repeatableSlots
        ];

        $response = $this->client->post("repeatable-interviewer-slots", [
            'body' => json_encode($data)
        ]);
        $responseData = json_decode($response->getBody(), true);
        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey("data", $responseData);
        $this->assertArrayHasKey("interviewer_id", $responseData['data']);
        $this->assertArrayHasKey("repeatable_intervirwer_slots", $responseData['data']);

        // get just created repeatable interviewer slots
        $container = $this->getPrivateContainer();
        $interviewer = $container->get('doctrine')
            ->getRepository(Interviewer::class)
            ->find((int)$responseData['data']['interviewer_id']);
        $slots = $interviewer->getRepeatableInterviewerSlots();

        print_r($slots);
    }
}
