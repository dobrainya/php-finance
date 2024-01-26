<?php

namespace App\DataFixtures;

use App\Entity\Reference;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ReferencesFixtures extends Fixture
{
    public const INCOME = 'income';
    public const EXPENSE = 'expense';

    public function load(ObjectManager $manager): void
    {
        $refs = [
            self::INCOME => (new Reference())->setName('Income')->setCode('inc'),
            self::EXPENSE => (new Reference())->setName('Expense')->setCode('exp'),
        ];

        foreach ($refs as $code => $ref) {
            $manager->persist($ref);
            $this->addReference($code, $ref);
        }
        $manager->flush();
    }
}
