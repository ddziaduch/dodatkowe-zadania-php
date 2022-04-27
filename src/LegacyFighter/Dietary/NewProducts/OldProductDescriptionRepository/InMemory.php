<?php

declare(strict_types=1);

namespace LegacyFighter\Dietary\NewProducts\OldProductDescriptionRepository;

use LegacyFighter\Dietary\NewProducts\OldProductDescription;
use LegacyFighter\Dietary\NewProducts\OldProductDescriptionRepository;
use Ramsey\Uuid\UuidInterface;

class InMemory implements OldProductDescriptionRepository
{
    /** @var OldProductDescription[] */
    private $descriptions = [];

    public function getOne(UuidInterface $productId): ?OldProductDescription
    {
        return $this->descriptions[$productId->toString()] ?? null;
    }

    public function save(OldProductDescription $description): void
    {
        $this->descriptions[$description->serialNumber()->toString()] = $description;
    }

    public function findAll(): array
    {
        return array_values($this->descriptions);
    }
}
