<?php

namespace LegacyFighter\Dietary\NewProducts;

use Ramsey\Uuid\UuidInterface;

interface OldProductRepository
{
    public function getOne(UuidInterface $productId): ?OldProduct;

    public function save(OldProduct $product): void;

    /**
     * @return OldProduct[]
     */
    public function findAll(): array;
}
