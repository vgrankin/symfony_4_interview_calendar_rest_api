<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ApiTest
 *
 * Misc REST API tests to ensure general behavior
 *
 * @package App\Tests
 */
class ApiTest extends BaseTestCase
{
    public function test404____when_Trying_To_Access_Nonexistent_Endpoint____Error_Response_Is_Returned()
    {
        $response = $this->client->get("nonexistent-endpoint");
        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey("error", $responseData);
        $this->assertArrayHasKey("code", $responseData['error']);
        $this->assertArrayHasKey("message", $responseData['error']);
        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $responseData['error']['code']);
        $this->assertEquals("Not Found", $responseData['error']['message']);
    }
}