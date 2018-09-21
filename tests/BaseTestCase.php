<?php

namespace App\Tests;

use App\Entity\City;
use App\Entity\Interviewer;
use App\Entity\Listing;
use App\Entity\Period;
use App\Entity\Section;
use App\Entity\User;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BaseTestCase extends KernelTestCase
{
    /**
     * @var Client
     */
    protected $client;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;
    /**
     * @var User
     */
    protected $testUser;

    /**
     * setUp() method executes before every test
     */
    public function setUp()
    {
        $container = $this->getPrivateContainer();
        $this->em = $container
            ->get('doctrine')
            ->getManager();


        $this->client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8000/api/',
            'exceptions' => false
        ]);

        $this->truncateTables();
    }

    private function truncateTables()
    {
        $entityManager = $this->em;
        $connection = $entityManager->getConnection();
        $connection->getConfiguration()->setSQLLogger(null);
        $connection->prepare("SET FOREIGN_KEY_CHECKS = 0;")->execute();

        $schemaManager = $connection->getSchemaManager();
        $tables = $schemaManager->listTables();
        $query = '';
        foreach ($tables as $table) {
            $name = $table->getName();
            $query .= 'TRUNCATE ' . $name . ';';
        }
        $connection->executeQuery($query, array(), array());

        $entityManager->getConnection()->prepare("SET FOREIGN_KEY_CHECKS = 1;")->execute();
    }

    protected function createTestInterviewer($name)
    {
        $interviewer = new Interviewer();
        $interviewer->setName($name);
        try {
            $this->em->persist($interviewer);
            $this->em->flush();

            return $interviewer;
        } catch (\Exception $ex) {
            return "Unable to create interviewer";
        }
    }

    protected function createTestRepeatableInterviewerSlots($data)
    {
        $container = $this->getPrivateContainer();
        $service = $container
            ->get('App\Service\RepeatableInterviewerSlotService');
        $slots = $service->createRepeatableInterviewerSlots($data);
        if (!is_array($slots)) {
            $this->fail("Unable to create test repeatable interviewer slots!");
        }

        return $slots;
    }

    protected function getPrivateContainer()
    {
        self::bootKernel();
        // returns the real and unchanged service container
        $container = self::$kernel->getContainer();
        // gets the special container that allows fetching private services
        $container = self::$container;
        return $container;
    }

    protected
    function tearDown()
    {
        $this->truncateTables();
        parent::tearDown();
    }
}