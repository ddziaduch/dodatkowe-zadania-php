<?php

declare(strict_types=1);

namespace Tests\LegacyFighter\Dietary\NewProducts;

use Brick\Math\BigDecimal;
use LegacyFighter\Dietary\NewProducts\OldProduct;
use LegacyFighter\Dietary\NewProducts\OldProductDescriptionRepository;
use LegacyFighter\Dietary\NewProducts\OldProductDescriptionRepository\InMemory as InMemoryOldProductDescriptionRepository;
use LegacyFighter\Dietary\NewProducts\OldProductRepository;
use LegacyFighter\Dietary\NewProducts\OldProductRepository\InMemory as InMemoryOldProductRepository;
use LegacyFighter\Dietary\NewProducts\OldProductService;
use PHPUnit\Framework\TestCase;

class OldProductServiceIntegrationTest extends TestCase
{
    /** @var OldProductDescriptionRepository */
    private $descriptionRepository;

    /** @var OldProductRepository */
    private $productRepository;

    /** @var OldProductService */
    private $service;

    /** @test */
    public function canFindAllDescriptions(): void
    {
        $this->aProduct(1);
        $this->aProduct(2);

        $allDescriptions = $this->service->findAllDescriptions();

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

        $this->service->replaceCharInDesc($product->serialNumber(), 'desc', 'ipsum');

        $description = $this->descriptionRepository->getOne($product->serialNumber());

        self::assertSame('ipsum1 *** long ipsum1', $description->formatted());
    }

    /** @test */
    public function canIncrementCounter(): void
    {
        $product = $this->aProduct(1);

        $this->service->incrementCounter($product->serialNumber());

        $product = $this->productRepository->getOne($product->serialNumber());

        self::assertSame(2, $product->getCounter());
    }

    /** @test */
    public function canChangePriceOf(): void
    {
        $product = $this->aProduct(1);

        $this->service->changePriceOf($product->serialNumber(), BigDecimal::of(100));

        $product = $this->productRepository->getOne($product->serialNumber());

        self::assertSame(100, $product->getPrice()->toInt());
    }

    /** @test */
    public function canReturnCounterOf(): void
    {
        $product = $this->aProduct(1);

        $counter = $this->service->getCounterOf($product->serialNumber());

        self::assertSame($product->getCounter(), $counter);
    }

    /** @test */
    public function canReturnPriceOf(): void
    {
        $product = $this->aProduct(1);

        $price = $this->service->getPriceOf($product->serialNumber());

        self::assertSame(1, $price->toInt());
    }

    private function aProduct(int $number): OldProduct
    {
        $product = new OldProduct(
            BigDecimal::of($number),
            'desc' . $number,
            'long desc' . $number,
            $number
        );

        $this->productRepository->save($product);

        return $product;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->descriptionRepository = new InMemoryOldProductDescriptionRepository();
        $this->productRepository = new InMemoryOldProductRepository($this->descriptionRepository);
        $this->service = new OldProductService($this->productRepository, $this->descriptionRepository);
    }
}
