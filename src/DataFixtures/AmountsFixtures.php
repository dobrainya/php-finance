<?php

namespace App\DataFixtures;

use App\Entity\Amount;
use App\Entity\Reference;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AmountsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $incomeRef = (new Reference())->setName('Income')->setCode('inc');
        $expenseRef = (new Reference())->setName('Expense')->setCode('exp');

        $manager->persist($incomeRef);
        $manager->persist($expenseRef);

        $manager->persist((new Amount())->setName('Sell own car')->setAmount(500000.00)->setType($incomeRef)->setCreatedAt(new \DateTime()));
        $manager->persist((new Amount())->setName('Sell own phone')->setAmount(5000.00)->setType($incomeRef)->setCreatedAt(new \DateTime()));
        $manager->persist((new Amount())->setName('Salary')->setAmount(50000.00)->setType($incomeRef)->setCreatedAt(new \DateTime()));

        $manager->persist((new Amount())->setName('Taxes')->setAmount(500.00)->setType($expenseRef)->setCreatedAt(new \DateTime()));
        $manager->persist((new Amount())->setName('Meals')->setAmount(200.00)->setType($expenseRef)->setCreatedAt(new \DateTime()));
        $manager->persist((new Amount())->setName('Groceries')->setAmount(60.00)->setType($expenseRef)->setCreatedAt(new \DateTime()));

        $manager->flush();
    }
}
