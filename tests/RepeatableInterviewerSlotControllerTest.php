<?php

namespace App\Controller;


use App\Entity\Interviewer;
use App\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class RepeatableInterviewerSlotControllerTest extends BaseTestCase
{
    public function testCreateRepeatableInterviewerSlots____when_Creating_New_RepeatableInterviewerSlots____RepeatableInterviewerSlots_Are_Created_And_Returned_With_Correct_Response_Status()
    {
        $interviewer = $this->createTestInterviewer('Philipp');
        if (!$interviewer instanceof Interviewer) {
            $this->fail($interviewer);
        }

        $repeatableSlots = [
            ['day_number' => 1, 'start_time' => '09:00 AM'],
            ['day_number' => 1, 'start_time' => '10:00 AM'],
            ['day_number' => 1, 'start_time' => '11:00 AM'],
            ['day_number' => 2, 'start_time' => '07:00 AM'],
            ['day_number' => 2, 'start_time' => '04:00 PM'],
            ['day_number' => 4, 'start_time' => '01:00 PM'],
            ['day_number' => 5, 'start_time' => '12:00 AM'],
            ['day_number' => 5, 'start_time' => '12:00 PM'],
            ['day_number' => 5, 'start_time' => '05:00 PM'],
        ];
        $data = [
            'interviewer_id' => $interviewer->getId(),
            'repeatable_interviewer_slots' => $repeatableSlots
        ];

        $response = $this->client->post("repeatable-interviewer-slots", [
            'body' => json_encode($data)
        ]);
        $responseData = json_decode($response->getBody(), true);

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey("data", $responseData);
        $this->assertArrayHasKey("repeatable_interviewer_slots", $responseData['data']);

        // get just created repeatable interviewer slots
        $container = $this->getPrivateContainer();
        $interviewer = $container->get('doctrine')
            ->getRepository(Interviewer::class)
            ->find((int)$responseData['data']["repeatable_interviewer_slots"][0]['interviewer_id']);
        $arr = $interviewer->getRepeatableInterviewerSlots();
        $this->assertEquals(sizeof($repeatableSlots), sizeof($arr));

        $slots = [];
        foreach ($arr as $slot)
        {
            $slots[] = [
                'id' => $slot->getId(),
                'interviewer_id' => $slot->getInterviewer()->getId(),
                'day_number' => $slot->getWeekday(),
                'start_time' => $slot->getHour()
            ];
        }

        // checking first 5 records data having ids 1 to 5

        $this->assertEquals(1, $responseData['data']["repeatable_interviewer_slots"][0]['id']);
        $this->assertEquals($data['interviewer_id'], $slots[0]['interviewer_id']);
        $this->assertEquals(1, $slots[0]['day_number']);
        $this->assertEquals(9, $slots[0]['start_time']);

        $this->assertEquals(2, $responseData['data']["repeatable_interviewer_slots"][1]['id']);
        $this->assertEquals($data['interviewer_id'], $slots[1]['interviewer_id']);
        $this->assertEquals(1, $slots[1]['day_number']);
        $this->assertEquals(10, $slots[1]['start_time']);

        $this->assertEquals(3, $responseData['data']["repeatable_interviewer_slots"][2]['id']);
        $this->assertEquals($data['interviewer_id'], $slots[2]['interviewer_id']);
        $this->assertEquals(1, $slots[2]['day_number']);
        $this->assertEquals(11, $slots[2]['start_time']);

        $this->assertEquals(4, $responseData['data']["repeatable_interviewer_slots"][3]['id']);
        $this->assertEquals($data['interviewer_id'], $slots[3]['interviewer_id']);
        $this->assertEquals(2, $slots[3]['day_number']);
        $this->assertEquals(7, $slots[3]['start_time']);

        $this->assertEquals(5, $responseData['data']["repeatable_interviewer_slots"][4]['id']);
        $this->assertEquals($data['interviewer_id'], $slots[4]['interviewer_id']);
        $this->assertEquals(2, $slots[4]['day_number']);
        $this->assertEquals(16, $slots[4]['start_time']);
    }

    public function testDeleteRepeatableInterviewerSlot____when_Deleting_Existing_RepeatableInterviewerSlot____RepeatableInterviewerSlot_Is_Deleted_And_Status_204_Is_Returned()
    {
        $interviewer = $this->createTestInterviewer('Philipp');
        if (!$interviewer instanceof Interviewer) {
            $this->fail($interviewer);
        }

        $repeatableSlots = [
            ['day_number' => 1, 'start_time' => '09:00 AM'],
        ];
        $data = [
            'interviewer_id' => $interviewer->getId(),
            'repeatable_interviewer_slots' => $repeatableSlots
        ];

        $slots = $this->createTestRepeatableInterviewerSlots($data);

        $response = $this->client->delete("repeatable-interviewer-slot/{$slots[0]->getId()}", []);
        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testDeleteRepeatableInterviewerSlots____when_Deleting_Existing_RepeatableInterviewerSlots____RepeatableInterviewerSlots_Are_Deleted_And_Status_204_Is_Returned()
    {
        $interviewer = $this->createTestInterviewer('Philipp');
        if (!$interviewer instanceof Interviewer) {
            $this->fail($interviewer);
        }

        $repeatableSlots = [
            ['day_number' => 1, 'start_time' => '09:00 AM'],
            ['day_number' => 2, 'start_time' => '09:00 AM'],
        ];
        $data = [
            'interviewer_id' => $interviewer->getId(),
            'repeatable_interviewer_slots' => $repeatableSlots
        ];

        $slots = $this->createTestRepeatableInterviewerSlots($data);

        $data = [
            'repeatable_interviewer_slots' => [$slots[0]->getId(), $slots[1]->getId()]
        ];

        $response = $this->client->delete("repeatable-interviewer-slots", [
            'body' => json_encode($data)
        ]);

        // get remaining repeatable interviewer slots (there should be none after deletion)
        $container = $this->getPrivateContainer();
        $interviewer = $container->get('doctrine')
            ->getRepository(Interviewer::class)
            ->find($interviewer->getId());
        $arr = $interviewer->getRepeatableInterviewerSlots();
        $this->assertEquals(0, sizeof($arr));

        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
