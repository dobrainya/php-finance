<?php

namespace App\Tests\Service;

use App\Entity\Amount;
use App\Entity\Reference;
use App\Model\AmountItem;
use App\Model\AmountListResponse;
use App\Repository\AmountRepository;
use App\Repository\ReferenceRepository;
use App\Service\AmountService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class AmountTest extends TestCase
{
    public function testGetIncomesByCategory(): void
    {
        $incomeRef = (new Reference())->setId(1)->setName('Income')->setCode('inc');
        $amounts = [
            (new Amount())->setId(1)->setName('Sell own car')->setAmount(500000.00)->setType($incomeRef)->setCreatedAt(new \DateTime()),
            (new Amount())->setId(2)->setName('Sell own phone')->setAmount(5000.00)->setType($incomeRef)->setCreatedAt(new \DateTime()),
            (new Amount())->setId(3)->setName('Salary')->setAmount(50000.00)->setType($incomeRef)->setCreatedAt(new \DateTime()),
        ];

        $amountRepository = $this->createMock(AmountRepository::class);

        $amountRepository->expects($this->once())
        ->method('findBy')
        ->with(['type' => $incomeRef])
        ->willReturn($amounts);

        $refRepository = $this->createMock(ReferenceRepository::class);

        $refRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'inc'])
            ->willReturn($incomeRef);

        $entityManager = $this->createMock(EntityManager::class);

        $entityManager->method('persist')->withAnyParameters();
        $entityManager->method('flush')->withAnyParameters();

        $service = new AmountService($amountRepository, $refRepository, $entityManager);

        $expected = new AmountListResponse(array_map(
            fn (Amount $amount) => AmountItem::createFromEntity($amount),
            $amounts
        ));

        $this->assertEquals($expected, $service->getAmountsByCategory('inc'));
    }

    public function testGetExpensesByCategory(): void
    {
        $expenseRef = (new Reference())->setId(1)->setName('Expense')->setCode('exp');
        $amounts = [
            (new Amount())->setId(1)->setName('Expense 1')->setAmount(100.00)->setType($expenseRef)->setCreatedAt(new \DateTime()),
            (new Amount())->setId(2)->setName('Expense 2')->setAmount(2.00)->setType($expenseRef)->setCreatedAt(new \DateTime()),
            (new Amount())->setId(3)->setName('Expense 3')->setAmount(34.00)->setType($expenseRef)->setCreatedAt(new \DateTime()),
        ];

        $amountRepository = $this->createMock(AmountRepository::class);

        $amountRepository->expects($this->once())
            ->method('findBy')
            ->with(['type' => $expenseRef])
            ->willReturn($amounts);

        $refRepository = $this->createMock(ReferenceRepository::class);

        $refRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'exp'])
            ->willReturn($expenseRef);

        $entityManager = $this->createMock(EntityManager::class);

        $entityManager->method('persist')->withAnyParameters();
        $entityManager->method('flush')->withAnyParameters();

        $service = new AmountService($amountRepository, $refRepository, $entityManager);

        $expected = new AmountListResponse(array_map(
            fn (Amount $amount) => AmountItem::createFromEntity($amount),
            $amounts
        ));

        $this->assertEquals($expected, $service->getAmountsByCategory('exp'));
    }
}
