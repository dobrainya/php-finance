<?php

namespace App\Tests;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AbstractRepositoryTest extends KernelTestCase
{
    protected ?EntityManagerInterface $entityManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
    }

    protected function getRepository(string $entityClass): ?ServiceEntityRepositoryInterface
    {
        return $this->entityManager->getRepository($entityClass);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
