<?php

namespace App\Controller;


use App\Entity\Candidate;
use App\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class CandidateSlotControllerTest extends BaseTestCase
{
    public function testCreateCandidateSlots____when_Creating_New_CandidateSlots____CandidateSlots_Are_Created_And_Returned_With_Correct_Response_Status()
    {
        $candidate = $this->createTestCandidate('Carl');
        if (!$candidate instanceof Candidate) {
            $this->fail($candidate);
        }

        $newSlots = [
            ['date' => '2018-10-01 09:00 AM'], // id = 1
            ['date' => '2018-10-02 09:00 AM'],
            ['date' => '2018-10-03 09:00 AM'],
            ['date' => '2018-10-03 10:00 AM'], // id = 4
            ['date' => '2018-10-03 11:00 AM'],
            ['date' => '2018-10-04 09:00 AM'],
            ['date' => '2018-10-05 09:00 AM'], // id = 7
        ];

        $data = [
            'candidate_id' => $candidate->getId(),
            'candidate_slots' => $newSlots
        ];

        $response = $this->client->post("candidate-slots", [
            'body' => json_encode($data)
        ]);
        $responseData = json_decode($response->getBody(), true);

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey("data", $responseData);
        $this->assertArrayHasKey("candidate_slots", $responseData['data']);

        // get just created candidate slots
        $container = $this->getPrivateContainer();
        $candidate = $container->get('doctrine')
            ->getRepository(Candidate::class)
            ->find((int)$responseData['data']["candidate_slots"][0]['candidate_id']);
        $arr = $candidate->getCandidateSlots();
        $this->assertEquals(sizeof($newSlots), sizeof($arr));

        $slots = [];
        foreach ($arr as $slot) {
            $slots[$slot->getId()] = [
                'id' => $slot->getId(),
                'candidate_id' => $slot->getCandidate()->getId(),
                'date' => $slot->getDate()->format("Y-m-d H:i A"),
            ];
        }

        // checking ids 1, 7

        $response = $responseData['data']["candidate_slots"];

        $id = 1;
        $this->assertEquals($id, $response[0]['id']);
        $this->assertEquals($data['candidate_id'], $response[0]['candidate_id']);
        $this->assertEquals('2018-10-01 09:00 AM', $response[0]['date']);

        $id = 4;
        $this->assertEquals($id, $response[3]['id']);
        $this->assertEquals($data['candidate_id'], $response[3]['candidate_id']);
        $this->assertEquals('2018-10-03 10:00 AM', $response[3]['date']);

        $id = 7;
        $this->assertEquals($id, $response[6]['id']);
        $this->assertEquals($data['candidate_id'], $response[6]['candidate_id']);
        $this->assertEquals('2018-10-05 09:00 AM', $response[6]['date']);
    }

    public function testDeleteCandidateSlot____when_Deleting_Existing_CandidateSlot____CandidateSlot_Is_Deleted_And_Status_204_Is_Returned()
    {
        $candidate = $this->createTestCandidate('Philipp');
        if (!$candidate instanceof Candidate) {
            $this->fail($candidate);
        }

        $singleSlots = [
            ['date' => '2018-10-01 12:00 PM'],
        ];
        $data = [
            'candidate_id' => $candidate->getId(),
            'candidate_slots' => $singleSlots
        ];

        $slots = $this->createTestCandidateSlots($data);

        $response = $this->client->delete("candidate-slot/{$slots[0]->getId()}", []);
        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
