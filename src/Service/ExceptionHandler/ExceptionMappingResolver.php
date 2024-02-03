<?php

namespace App\Service\ExceptionHandler;

class ExceptionMappingResolver
{
    /**
     * @var ExceptionMapping[]
     */
    private array $mappings = [];

    /**
     * @param ExceptionMapping[] $mappings
     */
    public function __construct(array $mappings)
    {
        foreach ($mappings as $class => $mapper) {
            if (empty($mapper['code'])) {
                throw new \InvalidArgumentException('code is mandatory for class '.$class);
            }

            $this->addMapping(
                $class,
                $mapper['code'],
                $mapper['hidden'] ?? true,
                $mapper['loggable'] ?? false
            );
        }
    }

    private function addMapping(string $class, int $code, bool $hidden, bool $loggable): void
    {
        $this->mappings[$class] = new ExceptionMapping($code, $hidden, $loggable);
    }

    public function resolve(string $throwableClass): ?ExceptionMapping
    {
        $foundedMapping = null;

        foreach ($this->mappings as $class => $mapping) {
            if ($throwableClass === $class || is_subclass_of($throwableClass, $class)) {
                $foundedMapping = $mapping;
                break;
            }
        }

        return $foundedMapping;
    }
}
