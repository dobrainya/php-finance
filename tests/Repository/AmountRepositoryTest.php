<?php

namespace App\Tests\Repository;

use App\Entity\Amount;
use App\Entity\Reference;
use App\Repository\AmountRepository;
use App\Tests\AbstractRepositoryTest;

class AmountRepositoryTest extends AbstractRepositoryTest
{
    private ?AmountRepository $amountRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->amountRepository = $this->getRepository(Amount::class);
    }

    public function testFindByCategoryCodeMethod()
    {
        $category = (new Reference())->setName('Test')->setCode('test');

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

        $this->assertCount(3, $this->amountRepository->findByCategoryCode('test'));
    }
}
