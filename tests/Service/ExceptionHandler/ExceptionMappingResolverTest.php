<?php

namespace App\Tests\Service\ExceptionHandler;

use App\Service\ExceptionHandler\ExceptionMappingResolver;
use App\Tests\AbstractTestCase;

class ExceptionMappingResolverTest extends AbstractTestCase
{
    public function testThrowsExceptionWhenEmptyCode(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new ExceptionMappingResolver(['someClass' => ['hidden' => true]]);
    }

    public function testResolvesToNullWhenNotFound(): void
    {
        $resolver = new ExceptionMappingResolver([]);

        $this->assertNull($resolver->resolve(\InvalidArgumentException::class));
    }

    public function testResolvesClassItself(): void
    {
        $resolver = new ExceptionMappingResolver([\InvalidArgumentException::class => ['code' => 400]]);
        $mapper = $resolver->resolve(\InvalidArgumentException::class);

        $this->assertEquals(400, $mapper->getCode());
    }

    public function testResolveSubClass(): void
    {
        $resolver = new ExceptionMappingResolver([\LogicException::class => ['code' => 500]]);
        $mapper = $resolver->resolve(\InvalidArgumentException::class);

        $this->assertEquals(500, $mapper->getCode());
    }

    public function testResolvesWithHidden(): void
    {
        $resolver = new ExceptionMappingResolver([\LogicException::class => ['code' => 500, 'hidden' => false]]);
        $mapper = $resolver->resolve(\LogicException::class);

        $this->assertFalse($mapper->isHidden());
    }

    public function testResolvesWithLoggable(): void
    {
        $resolver = new ExceptionMappingResolver([\LogicException::class => ['code' => 500, 'loggable' => true]]);
        $mapper = $resolver->resolve(\LogicException::class);

        $this->assertTrue($mapper->isLoggable());
    }

    public function testResolvesWithDefaultParams(): void
    {
        $resolver = new ExceptionMappingResolver([\RuntimeException::class => ['code' => 500]]);
        $mapper = $resolver->resolve(\RuntimeException::class);

        $this->assertFalse($mapper->isLoggable());
        $this->assertTrue($mapper->isHidden());
    }
}
