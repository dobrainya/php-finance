<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    public function setEntityId(object $entity, int $value, string $idField = 'id'): void
    {
        $reflectionClass = new \ReflectionClass($entity);
        $property = $reflectionClass->getProperty($idField);
        $property->setAccessible(true);
        $property->setValue($entity, $value);
        $property->setAccessible(false);
    }
}
