<?php

namespace App\Tests\Repository;

use App\Entity\Reference;
use App\Repository\ReferenceRepository;
use App\Tests\AbstractRepositoryTest;

class ReferenceRepositoryTest extends AbstractRepositoryTest
{
    private ?ReferenceRepository $referenceRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->referenceRepository = $this->getRepository(Reference::class);
    }

    public function testExistsByCodeMethod()
    {
        $category = (new Reference())->setName('Test')->setCode('test');

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $this->assertTrue($this->referenceRepository->existsByCode('test'));
        $this->assertFalse($this->referenceRepository->existsByCode('test1'));
    }

    public function testFindOneByCodeMethod()
    {
        $category = (new Reference())->setName('Test')->setCode('test');

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $this->assertNotEmpty($this->referenceRepository->findOneByCode('test'));
    }
}
