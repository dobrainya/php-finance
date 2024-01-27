<?php

namespace App\Tests\Controller;

use App\Entity\Amount;
use App\Entity\Reference;
use App\Tests\AbstractControllerTest;

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
}
