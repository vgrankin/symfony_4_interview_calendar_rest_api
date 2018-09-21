<?php

namespace App\Controller;


use App\Entity\Interviewer;
use App\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class SingleInterviewerSlotControllerTest extends BaseTestCase
{
    public function testCreateSingleInterviewerSlots____when_Creating_New_SingleInterviewerSlots____SingleInterviewerSlots_Are_Created_And_Returned_With_Correct_Response_Status()
    {
        $interviewer = $this->createTestInterviewer('Sarah');
        if (!$interviewer instanceof Interviewer) {
            $this->fail($interviewer);
        }

        $singleSlots = [
            ['date' => '2018-10-01 12:00 PM', 'is_blocked_slot' => false], // id = 1
            ['date' => '2018-10-01 01:00 PM', 'is_blocked_slot' => false],
            ['date' => '2018-10-01 02:00 PM', 'is_blocked_slot' => false],
            ['date' => '2018-10-01 03:00 PM', 'is_blocked_slot' => false],
            ['date' => '2018-10-01 04:00 PM', 'is_blocked_slot' => false],
            ['date' => '2018-10-01 05:00 PM', 'is_blocked_slot' => false],

            ['date' => '2018-10-03 12:00 PM', 'is_blocked_slot' => false],
            ['date' => '2018-10-03 01:00 PM', 'is_blocked_slot' => false],
            ['date' => '2018-10-03 02:00 PM', 'is_blocked_slot' => false],
            ['date' => '2018-10-03 03:00 PM', 'is_blocked_slot' => false],
            ['date' => '2018-10-03 04:00 PM', 'is_blocked_slot' => false],
            ['date' => '2018-10-03 05:00 PM', 'is_blocked_slot' => false], // id = 12

            ['date' => '2018-10-02 09:00 AM', 'is_blocked_slot' => false],
            ['date' => '2018-10-02 10:00 AM', 'is_blocked_slot' => false],
            ['date' => '2018-10-02 11:00 AM', 'is_blocked_slot' => false], // id = 15

            ['date' => '2018-10-04 09:00 AM', 'is_blocked_slot' => false],
            ['date' => '2018-10-04 10:00 AM', 'is_blocked_slot' => false],
            ['date' => '2018-10-04 11:00 AM', 'is_blocked_slot' => false], // id = 18

            ['date' => '2018-10-05 11:00 AM', 'is_blocked_slot' => true], // id = 19
        ];

        $data = [
            'interviewer_id' => $interviewer->getId(),
            'single_interviewer_slots' => $singleSlots
        ];

        $response = $this->client->post("single-interviewer-slots", [
            'body' => json_encode($data)
        ]);
        $responseData = json_decode($response->getBody(), true);

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey("data", $responseData);
        $this->assertArrayHasKey("single_interviewer_slots", $responseData['data']);

        // get just created single interviewer slots
        $container = $this->getPrivateContainer();
        $interviewer = $container->get('doctrine')
            ->getRepository(Interviewer::class)
            ->find((int)$responseData['data']["single_interviewer_slots"][0]['interviewer_id']);
        $arr = $interviewer->getSingleInterviewerSlots();
        $this->assertEquals(sizeof($singleSlots), sizeof($arr));

        $slots = [];
        foreach ($arr as $slot) {
            $slots[$slot->getId()] = [
                'id' => $slot->getId(),
                'interviewer_id' => $slot->getInterviewer()->getId(),
                'date' => $slot->getDate()->format("Y-m-d H:i A"),
                'is_blocked_slot' => $slot->getIsBlockedSlot()
            ];
        }

        // checking ids 1, 12, 15, 19

        $response = $responseData['data']["single_interviewer_slots"];

        $id = 1;
        $this->assertEquals($id, $response[0]['id']);
        $this->assertEquals($data['interviewer_id'], $response[0]['interviewer_id']);
        $this->assertEquals('2018-10-01 12:00 PM', $response[0]['date']);
        $this->assertFalse($response[0]['is_blocked_slot']);

        $id = 12;
        $this->assertEquals($id, $response[11]['id']);
        $this->assertEquals($data['interviewer_id'], $response[11]['interviewer_id']);
        $this->assertEquals('2018-10-03 05:00 PM', $response[11]['date']);
        $this->assertFalse($response[11]['is_blocked_slot']);

        $id = 15;
        $this->assertEquals($id, $response[14]['id']);
        $this->assertEquals($data['interviewer_id'], $response[14]['interviewer_id']);
        $this->assertEquals('2018-10-02 11:00 AM', $response[14]['date']);
        $this->assertFalse($response[14]['is_blocked_slot']);

        $id = 19;
        $this->assertEquals($id, $response[18]['id']);
        $this->assertEquals($data['interviewer_id'], $response[18]['interviewer_id']);
        $this->assertEquals('2018-10-05 11:00 AM', $response[18]['date']);
        $this->assertTrue($response[18]['is_blocked_slot']);
    }

    public function testDeleteSingleInterviewerSlot____when_Deleting_Existing_SingleInterviewerSlot____SingleInterviewerSlot_Is_Deleted_And_Status_204_Is_Returned()
    {
        $interviewer = $this->createTestInterviewer('Philipp');
        if (!$interviewer instanceof Interviewer) {
            $this->fail($interviewer);
        }

        $singleSlots = [
            ['date' => '2018-10-01 12:00 PM', 'is_blocked_slot' => false],
        ];
        $data = [
            'interviewer_id' => $interviewer->getId(),
            'single_interviewer_slots' => $singleSlots
        ];

        $slots = $this->createTestSingleInterviewerSlots($data);

        $response = $this->client->delete("single-interviewer-slot/{$slots[0]->getId()}", []);
        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
