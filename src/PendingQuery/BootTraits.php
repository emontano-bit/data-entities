<?php

namespace BitMx\DataEntities\PendingQuery;

use BitMx\DataEntities\PendingQuery;

readonly class BootTraits
{
    public function __invoke(PendingQuery $pendingQuery): PendingQuery
    {
        $dataEntity = $pendingQuery->getDataEntity();

        $traits = $this->classUses($dataEntity);

        $this->bootTraits($traits, $pendingQuery);

        return $pendingQuery;
    }

    /**
     * @param  object|class-string  $class
     * @return array<class-string>
     */
    protected function classUses(object|string $class): array
    {
        return class_uses_recursive($class);
    }

    /**
     * @param  array<class-string>  $traits
     */
    protected function bootTraits(array $traits, PendingQuery $pendingQuery): void
    {
        foreach ($traits as $trait) {
            $this->bootTrait($trait, $pendingQuery);
        }
    }

    /**
     * @param  class-string|object  $trait
     */
    protected function bootTrait(string|object $trait, PendingQuery $pendingQuery): void
    {
        $traitReflection = new \ReflectionClass($trait);

        $bootMethodName = 'boot'.$traitReflection->getShortName();

        $dataEntity = $pendingQuery->getDataEntity();

        if (! method_exists($dataEntity, $bootMethodName)) {
            return;
        }

        $dataEntity->{$bootMethodName}($pendingQuery);
    }
}
