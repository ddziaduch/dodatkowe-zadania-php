<?php

declare(strict_types=1);

namespace LegacyFighter\Dietary\NewProducts;

use Ramsey\Uuid\UuidInterface;

interface OldProductDescriptionRepository
{
    public function getOne(UuidInterface $productId): ?OldProductDescription;

    public function save(OldProductDescription $description): void;

    /** @return OldProductDescription[] */
    public function findAll(): array;
}
