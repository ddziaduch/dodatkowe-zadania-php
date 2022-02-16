<?php

namespace LegacyFighter\Dietary\Tests\NewProducts;

use Brick\Math\BigDecimal;
use LegacyFighter\Dietary\NewProducts\OldProduct;
use PHPUnit\Framework\TestCase;

class OldProductTest extends TestCase
{
    /** @test */
    public function canNotDecrementCounterWhenPriceIsNotSet(): void
    {
        $oldProduct = new OldProduct(null, null, null, null);
        $this->expectException(\Exception::class);
        $oldProduct->decrementCounter();
    }

    /** @test */
    public function canNotDecrementCounterWhenPriceSignIsNegative(): void
    {
        $oldProduct = new OldProduct(BigDecimal::of(-1), null, null, null);
        $this->expectException(\Exception::class);
        $oldProduct->decrementCounter();
    }

    /** @test */
    public function canNotDecrementCounterWhenCounterIsNotSet(): void
    {
        $oldProduct = new OldProduct(BigDecimal::one(), null, null, null);
        $this->expectException(\Exception::class);
        $oldProduct->decrementCounter();
    }

    /** @test */
    public function canNotDecrementCounterWhenNewCounterIsNegative(): void
    {
        $oldProduct = new OldProduct(BigDecimal::one(), null, null, 0);
        $this->expectException(\Exception::class);
        $oldProduct->decrementCounter();
    }

    /**
     * @test
     */
    public function canDecrementCounter(): void
    {
        $oldProduct = new OldProduct(BigDecimal::one(), null, null, 1);
        $this->expectNotToPerformAssertions();
        $oldProduct->decrementCounter();
    }

    /** @test */
    public function canNotIncrementCounterWhenPriceIsNotSet(): void
    {
        $oldProduct = new OldProduct(null, null, null, null);
        $this->expectException(\Exception::class);
        $oldProduct->incrementCounter();
    }

    /** @test */
    public function canNotIncrementCounterWhenPriceSignIsNegative(): void
    {
        $oldProduct = new OldProduct(BigDecimal::of(-1), null, null, null);
        $this->expectException(\Exception::class);
        $oldProduct->incrementCounter();
    }

    /** @test */
    public function canNotIncrementCounterWhenCounterIsNotSet(): void
    {
        $oldProduct = new OldProduct(BigDecimal::one(), null, null, null);
        $this->expectException(\Exception::class);
        $oldProduct->incrementCounter();
    }

    /** @test */
    public function canNotIncrementCounterWhenNewCounterIsNegative(): void
    {
        $oldProduct = new OldProduct(BigDecimal::one(), null, null, -2);
        $this->expectException(\Exception::class);
        $oldProduct->incrementCounter();
    }

    /**
     * @test
     */
    public function canIncrementCounter(): void
    {
        $oldProduct = new OldProduct(BigDecimal::one(), null, null, 0);
        $this->expectNotToPerformAssertions();
        $oldProduct->incrementCounter();
    }
}
