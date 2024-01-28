<?php

namespace App\Tests\Controller;

use App\Entity\Reference;
use App\Tests\AbstractControllerTest;

class ReferenceControllerTest extends AbstractControllerTest
{
    public function testGetAmounts(): void
    {
        $category = (new Reference())->setName('Incomes')->setCode('inc');

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $this->client->request('GET', '/api/v1/refs');
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
                        'required' => ['id', 'name', 'code'],
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'name' => ['type' => 'string'],
                            'code' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
