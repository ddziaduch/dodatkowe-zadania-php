<?php

declare(strict_types=1);

namespace Tests\LegacyFighter\Dietary\NewProducts;

use Brick\Math\BigDecimal;
use LegacyFighter\Dietary\NewProducts\OldProduct;
use LegacyFighter\Dietary\NewProducts\OldProductRepository;
use LegacyFighter\Dietary\NewProducts\OldProductRepository\InMemory;
use LegacyFighter\Dietary\NewProducts\OldProductService;
use PHPUnit\Framework\TestCase;

class OldProductServiceIntegrationTest extends TestCase
{
    /** @test */
    public function canFindAllDescriptions(): void
    {
        $product1 = $this->aProduct(1);
        $product2 = $this->aProduct(2);
        $oldProductRepository = $this->aRepository($product1, $product2);
        $oldProductService = new OldProductService($oldProductRepository);

        $allDescriptions = $oldProductService->findAllDescriptions();

        self::assertSame(
            [
                'desc1 *** long desc1',
                'desc2 *** long desc2',
            ],
            $allDescriptions
        );
    }

    /** @test */
    public function canReplaceCharInDesc(): void
    {
        $product = $this->aProduct(1);
        $repository = $this->aRepository($product);
        $service = new OldProductService($repository);

        $service->replaceCharInDesc($product->serialNumber(), 'desc', 'ipsum');

        $product = $repository->getOne($product->serialNumber());

        self::assertSame('ipsum1 *** long ipsum1', $product->formatDesc());
    }

    /** @test */
    public function canIncrementCounter(): void
    {
        $product = $this->aProduct(1);
        $repository = $this->aRepository($product);
        $service = new OldProductService($repository);

        $service->incrementCounter($product->serialNumber());

        $product = $repository->getOne($product->serialNumber());

        self::assertSame(2, $product->getCounter());
    }

    /** @test */
    public function canChangePriceOf(): void
    {
        $product = $this->aProduct(1);
        $repository = $this->aRepository($product);
        $service = new OldProductService($repository);

        $service->changePriceOf($product->serialNumber(), BigDecimal::of(100));

        $product = $repository->getOne($product->serialNumber());

        self::assertSame(100, $product->getPrice()->toInt());
    }

    /** @test */
    public function canReturnCounterOf(): void
    {
        $product = $this->aProduct(1);
        $repository = $this->aRepository($product);
        $service = new OldProductService($repository);

        $counter = $service->getCounterOf($product->serialNumber());

        self::assertSame($product->getCounter(), $counter);
    }

    /** @test */
    public function canReturnPriceOf(): void
    {
        $product = $this->aProduct(1);
        $repository = $this->aRepository($product);
        $service = new OldProductService($repository);

        $price = $service->getPriceOf($product->serialNumber());

        self::assertSame(1, $price->toInt());
    }

    private function aProduct(int $number): OldProduct
    {
        return new OldProduct(
            BigDecimal::of($number),
            'desc'.$number,
            'long desc'.$number,
            $number
        );
    }

    private function aRepository(OldProduct ...$products): OldProductRepository
    {
        $oldProductRepository = new InMemory();

        foreach ($products as $product) {
            $oldProductRepository->save($product);
        }

        return $oldProductRepository;
    }
}
