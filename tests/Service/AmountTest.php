<?php

namespace App\Tests\Service;

use App\Entity\Amount;
use App\Entity\Reference;
use App\Exception\CategoryNotFoundException;
use App\Model\AmountItem;
use App\Model\AmountListResponse;
use App\Repository\AmountRepository;
use App\Repository\ReferenceRepository;
use App\Service\AmountService;
use App\Tests\AbstractTestCase;
use Doctrine\ORM\EntityManager;

class AmountTest extends AbstractTestCase
{
    public function testGetCategoryNotFoundException()
    {
        $refRepository = $this->createMock(ReferenceRepository::class);
        $refRepository->expects($this->once())
            ->method('existsByCode')
            ->withAnyParameters()
            ->willReturn(false);

        $service = new AmountService(
            $this->createMock(AmountRepository::class),
            $refRepository,
            $this->createMock(EntityManager::class)
        );

        $this->expectException(CategoryNotFoundException::class);
        $service->getAmountsByCategory('incorrect_cat_code');
    }

    public function testGetIncomesByCategory(): void
    {
        $amounts = [
            (new Amount())->setName('Amount 1')->setAmount(500000.00)->setCreatedAt(new \DateTime()),
            (new Amount())->setName('Amount 2')->setAmount(5000.00)->setCreatedAt(new \DateTime()),
            (new Amount())->setName('Amount 3')->setAmount(50000.00)->setCreatedAt(new \DateTime()),
        ];

        $this->amountListTest($amounts, 'inc');
    }

    public function testGetExpensesByCategory(): void
    {
        $amounts = [
            (new Amount())->setName('Amount 1')->setAmount(500000.00)->setCreatedAt(new \DateTime()),
            (new Amount())->setName('Amount 2')->setAmount(5000.00)->setCreatedAt(new \DateTime()),
            (new Amount())->setName('Amount 3')->setAmount(50000.00)->setCreatedAt(new \DateTime()),
        ];

        $this->amountListTest($amounts, 'exp');
    }

    /**
     * @param array<Amount> $amounts
     *
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    private function amountListTest(array $amounts, string $categoryCode)
    {
        $categoryCode = 'inc';

        $reference = (new Reference())->setId(1)->setName('Income')->setCode($categoryCode);
        foreach ($amounts as $idx => $amount) {
            $this->setEntityId($amount, $idx + 1);
            $amount->setType($reference);
        }

        $amountRepository = $this->createMock(AmountRepository::class);

        $amountRepository->expects($this->once())
            ->method('findByCategoryCode')
            ->with($categoryCode)
            ->willReturn($amounts);

        $refRepository = $this->createMock(ReferenceRepository::class);

        $refRepository->expects($this->once())
            ->method('existsByCode')
            ->with($categoryCode)
            ->willReturn(true);

        $entityManager = $this->createMock(EntityManager::class);

        $entityManager->method('persist')->withAnyParameters();
        $entityManager->method('flush')->withAnyParameters();

        $service = new AmountService($amountRepository, $refRepository, $entityManager);

        $expected = new AmountListResponse(array_map(
            fn (Amount $amount) => AmountItem::createFromEntity($amount),
            $amounts
        ));

        $this->assertEquals($expected, $service->getAmountsByCategory($categoryCode));
    }
}
