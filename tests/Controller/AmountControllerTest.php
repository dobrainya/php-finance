<?php

namespace App\Tests\Controller;

use App\Entity\Amount;
use App\Entity\Reference;
use App\Tests\AbstractControllerTest;
use Symfony\Component\HttpFoundation\Response;

class AmountControllerTest extends AbstractControllerTest
{
    public function testGetAmounts(): void
    {
        $category = (new Reference())->setName('Incomes')->setCode('inc');

        $this->entityManager->persist($category);

        $amounts = [
            (new Amount())->setName('Amount 1')->setAmount(50.00)->setCreatedAt(new \DateTime())->setType($category),
            (new Amount())->setName('Amount 2')->setAmount(150.00)->setCreatedAt(new \DateTime())->setType($category),
            (new Amount())->setName('Amount 3')->setAmount(250.00)->setCreatedAt(new \DateTime())->setType($category),
        ];

        foreach ($amounts as $amount) {
            $this->entityManager->persist($amount);
        }

        $this->entityManager->flush();

        $this->client->request('GET', '/api/v1/amount/inc');
        $responseText = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();

        $this->assertJsonDocumentMatchesSchema($responseText, [
            'type' => 'object',
            'required' => ['items'],
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'name', 'amount', 'createdAt', 'type'],
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'name' => ['type' => 'string'],
                            'amount' => ['type' => 'number'],
                            'createdAt' => ['type' => 'number'],
                            'type' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testCreate(): void
    {
        $content = json_encode(['name' => 'test', 'amount' => 500.0, 'type' => 'exp']);

        $this->client->request('POST', '/api/v1/amount/', [], [], [], $content);
        $responseText = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();

        $this->assertJsonDocumentMatchesSchema($responseText, [
            'type' => 'object',
            'required' => ['item'],
            'properties' => [
                'item' => [
                    'type' => 'object',
                    'required' => ['id', 'name', 'amount', 'createdAt', 'type'],
                    'properties' => [
                        'id' => ['type' => 'integer'],
                        'name' => ['type' => 'string'],
                        'amount' => ['type' => 'number'],
                        'createdAt' => ['type' => 'number'],
                        'type' => ['type' => 'string'],
                    ],
                ],
            ],
        ]);
    }

    public function testCreateReturnViolationWithNameField(): void
    {
        $content = json_encode(['amount' => 500.0, 'type' => 'exp']);

        $this->client->request('POST', '/api/v1/amount/', [], [], [], $content);
        $responseText = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $this->assertJsonDocumentMatches($responseText, [
            '$.message' => 'validation failed',
            '$.details.violations' => self::countOf(1),
            '$.details.violations[0].field' => 'name',
        ]);
    }

    public function testCreateReturnViolationWithAmountField(): void
    {
        $content = json_encode(['name' => 'test', 'type' => 'exp']);

        $this->client->request('POST', '/api/v1/amount/', [], [], [], $content);
        $responseText = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $this->assertJsonDocumentMatches($responseText, [
            '$.message' => 'validation failed',
            '$.details.violations' => self::countOf(1),
            '$.details.violations[0].field' => 'amount',
        ]);
    }

    public function testCreateReturnViolationWithTypeField(): void
    {
        $content = json_encode(['name' => 'test', 'amount' => 500.0]);

        $this->client->request('POST', '/api/v1/amount/', [], [], [], $content);
        $responseText = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $this->assertJsonDocumentMatches($responseText, [
            '$.message' => 'validation failed',
            '$.details.violations' => self::countOf(1),
            '$.details.violations[0].field' => 'type',
        ]);
    }

    public function testCreateReturnViolationWithAllField(): void
    {
        $content = json_encode([]);

        $this->client->request('POST', '/api/v1/amount/', [], [], [], $content);
        $responseText = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $this->assertJsonDocumentMatches($responseText, [
            '$.message' => 'validation failed',
            '$.details.violations' => self::countOf(3),
        ]);
    }

    public function testCreateReturnErrorResponse(): void
    {
        $content = 'invalid request body';

        $this->client->request('POST', '/api/v1/amount/', [], [], [], $content);
        $responseText = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $this->assertJsonDocumentMatches($responseText, [
            '$.message' => 'error while unmarshalling request body',
        ]);
    }
}
