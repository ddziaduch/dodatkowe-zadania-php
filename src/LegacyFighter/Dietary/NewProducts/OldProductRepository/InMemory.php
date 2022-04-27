<?php

namespace LegacyFighter\Dietary\NewProducts\OldProductRepository;

use LegacyFighter\Dietary\NewProducts\OldProductDescriptionRepository;
use Ramsey\Uuid\UuidInterface;
use LegacyFighter\Dietary\NewProducts\OldProduct;
use LegacyFighter\Dietary\NewProducts\OldProductRepository;

class InMemory implements OldProductRepository
{
    /**
     * @var OldProduct[]
     */
    private $products = [];

    /** @var OldProductDescriptionRepository */
    private $descriptionRepository;

    public function __construct(
        OldProductDescriptionRepository $descriptionRepository
    ) {
        $this->descriptionRepository = $descriptionRepository;
    }

    public function getOne(UuidInterface $productId): ?OldProduct
    {
        if (!array_key_exists($productId->toString(), $this->products)) {
            return null;
        }

        $product = $this->products[$productId->toString()];
        $description = $this->descriptionRepository->getOne($productId);

        if (null === $description) {
            throw new \RuntimeException('Unable to find description for a product');
        }

        return $product->withDescription($description->getShort(), $description->getLong());
    }

    /**
     * @param OldProduct $product
     */
    public function save(OldProduct $product): void
    {
        $this->products[$product->serialNumber()->toString()] = $product;
        $this->descriptionRepository->save($product->getDescription());
    }

    /**
     * @return array
     */
    function findAll(): array
    {
        return array_values($this->products);
    }
}
