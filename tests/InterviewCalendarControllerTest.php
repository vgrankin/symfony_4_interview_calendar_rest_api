<?php

namespace App\Controller;


use App\Entity\Interviewer;
use App\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class InterviewCalendarControllerTest extends BaseTestCase
{
    public function testGetInterviewCalendar____when_Getting_Interview_Calendar_With_Correct_Data____Calendar_Data_Is_Returned_With_Correct_Response_Status()
    {
        $philipp = $this->createTestInterviewer("Philipp");
        $sarah = $this->createTestInterviewer("Sarah");

        $carl = $this->createTestCandidate("Carl");

        // create repeatable slots for Philipp

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

        $repeatableSlots = [];
        for ($i = 1; $i <= 5; $i++)
        {
            $repeatableSlots[] = ['day_number' => $i, 'start_time' => '09:00 AM'];
            $repeatableSlots[] = ['day_number' => $i, 'start_time' => '10:00 AM'];
            $repeatableSlots[] = ['day_number' => $i, 'start_time' => '11:00 AM'];
            $repeatableSlots[] = ['day_number' => $i, 'start_time' => '12:00 PM'];
            $repeatableSlots[] = ['day_number' => $i, 'start_time' => '01:00 PM'];
            $repeatableSlots[] = ['day_number' => $i, 'start_time' => '02:00 PM'];
            $repeatableSlots[] = ['day_number' => $i, 'start_time' => '03:00 PM'];
        }

        $data = [
            'interviewer_id' => $philipp->getId(),
            'repeatable_interviewer_slots' => $repeatableSlots
        ];

        $philippSlots = $this->createTestRepeatableInterviewerSlots($data);

        // create single slots for Sarah

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
        ];

        $data = [
            'interviewer_id' => $sarah->getId(),
            'single_interviewer_slots' => $singleSlots
        ];

        $sarahSlots = $this->createTestSingleInterviewerSlots($data);

        // create candidate slots

        $singleSlots = [
            ['date' => '2018-10-01 09:00 AM'], // id = 1
            ['date' => '2018-10-02 09:00 AM'],
            ['date' => '2018-10-03 09:00 AM'],
            ['date' => '2018-10-03 10:00 AM'], // id = 4
            ['date' => '2018-10-03 11:00 AM'],
            ['date' => '2018-10-04 09:00 AM'],
            ['date' => '2018-10-05 09:00 AM'], // id = 7
        ];

        $data = [
            'candidate_id' => $carl->getId(),
            'candidate_slots' => $singleSlots
        ];

        $carlSlots = $this->createTestCandidateSlots($data);

        $response = $this->client->get("interview-calendar/{$carl->getId()}");
        $responseData = json_decode($response->getBody(), true);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $this->assertTrue(isset($responseData['data']['intersections']));

        $data = $responseData['data']['intersections'];
        $this->assertEquals(9, sizeof($data));

        foreach ($data as $entry) {
            $this->assertArrayHasKey('candidate', $entry);
            $this->assertArrayHasKey('interviewer', $entry);
            $this->assertArrayHasKey('weekday', $entry);
            $this->assertArrayHasKey('start_time', $entry);
            $this->assertArrayHasKey('date', $entry);
        }


        $this->assertEquals(1, $data[0]['candidate']['id']);
        $this->assertEquals('Carl', $data[0]['candidate']['name']);
        $this->assertEquals(1, $data[0]['interviewer']['id']);
        $this->assertEquals('Philipp', $data[0]['interviewer']['name']);
        $this->assertEquals(1, $data[0]['weekday']);
        $this->assertEquals('09:00 AM', $data[0]['start_time']);
        $this->assertEquals('2018-10-01 09:00 AM', $data[0]['date']);

        $this->assertEquals(1, $data[2]['candidate']['id']);
        $this->assertEquals('Carl', $data[2]['candidate']['name']);
        $this->assertEquals(2, $data[2]['interviewer']['id']);
        $this->assertEquals('Sarah', $data[2]['interviewer']['name']);
        $this->assertEquals(2, $data[2]['weekday']);
        $this->assertEquals('09:00 AM', $data[2]['start_time']);
        $this->assertEquals('2018-10-02 09:00 AM', $data[2]['date']);

        $this->assertEquals(1, $data[8]['candidate']['id']);
        $this->assertEquals('Carl', $data[8]['candidate']['name']);
        $this->assertEquals(1, $data[8]['interviewer']['id']);
        $this->assertEquals('Philipp', $data[8]['interviewer']['name']);
        $this->assertEquals(5, $data[8]['weekday']);
        $this->assertEquals('09:00 AM', $data[8]['start_time']);
        $this->assertEquals('2018-10-05 09:00 AM', $data[8]['date']);
    }
}
