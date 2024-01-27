<?php

namespace App\DataFixtures;

use App\Entity\Amount;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AmountsFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $incomeRef = $this->getReference(ReferencesFixtures::INCOME);
        $expenseRef = $this->getReference(ReferencesFixtures::EXPENSE);

        $createdAt = \DateTime::createFromFormat('U', '1706346528', new \DateTimeZone('Europe/Moscow'));

        $manager->persist((new Amount())->setName('Sell own car')->setAmount(500000.00)->setType($incomeRef)->setCreatedAt(clone $createdAt));
        $manager->persist((new Amount())->setName('Sell own phone')->setAmount(5000.00)->setType($incomeRef)->setCreatedAt(clone $createdAt));
        $manager->persist((new Amount())->setName('Salary')->setAmount(50000.00)->setType($incomeRef)->setCreatedAt(clone $createdAt));

        $manager->persist((new Amount())->setName('Taxes')->setAmount(500.00)->setType($expenseRef)->setCreatedAt(clone $createdAt));
        $manager->persist((new Amount())->setName('Meals')->setAmount(200.00)->setType($expenseRef)->setCreatedAt(clone $createdAt));
        $manager->persist((new Amount())->setName('Groceries')->setAmount(60.00)->setType($expenseRef)->setCreatedAt(clone $createdAt));

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ReferencesFixtures::class,
        ];
    }
}
