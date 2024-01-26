<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AmountControllerTest extends WebTestCase
{
    public function testGetIncomes(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/amount/incomes');
        $responseText = $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJsonStringEqualsJsonFile(
            __DIR__.'/Responses/AmountControllerTest_testGetIncomes.json',
            $responseText
        );
    }

    public function testGetExpenses(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/amount/expenses');
        $responseText = $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJsonStringEqualsJsonFile(
            __DIR__.'/Responses/AmountControllerTest_testGetExpenses.json',
            $responseText
        );
    }
}
