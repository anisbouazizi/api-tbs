<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionTest extends WebTestCase
{
    public function testGetSubscriptionByValidContact()
    {
        $client = static::createClient();
        $client->request('GET', '/api/subscription/1');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testGetSubscriptionByInvalidContact()
    {
        $client = static::createClient();
        $client->request('GET', '/api/subscription/999'); // Assuming contact ID 999 doesn't exist

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertJson($client->getResponse()->getContent());
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame(Response::HTTP_NOT_FOUND, $responseData['status']);
        $this->assertSame('contact not found', $responseData['message']);
    }

    public function testCreateSubscription()
    {
        $client = static::createClient();

        $postData = [
            'contact' => 1,
            'product' => 1,
            "begineDate" => "2026-02-15 07:36:55",
            "endDate" => "2027-02-15 07:36:55"
        ];

        $client->request('POST', '/api/subscription', [], [], [], json_encode($postData));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testUpdateSubscription()
    {
        $client = static::createClient();

        $existingSubscriptionId = 1;

        $postData = [
            'contact' => 2,
            'product' => 2,
            "begineDate" => "2026-02-15 07:36:55",
            "endDate" => "2027-02-15 07:36:55"
        ];

        $client->request('PUT', '/api/subscription/' . $existingSubscriptionId, [], [], [], json_encode($postData));

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testDeleteSubscription()
    {
        $client = static::createClient();

        $subscriptionId = 1;

        $client->request('DELETE', '/api/subscription/'.$subscriptionId);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testDeleteNonExistingSubscription()
    {
        $client = static::createClient();

        $subscriptionId = 999;

        $client->request('DELETE', '/api/subscription/'.$subscriptionId);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertJson($client->getResponse()->getContent());
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame(Response::HTTP_NOT_FOUND, $responseData['status']);
        $this->assertSame('Subscription not found', $responseData['message']);
    }
}
