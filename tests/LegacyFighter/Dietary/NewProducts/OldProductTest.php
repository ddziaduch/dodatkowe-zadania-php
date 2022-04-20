<?php

declare(strict_types=1);

namespace Tests\LegacyFighter\Dietary\NewProducts;

use Brick\Math\BigDecimal;
use PHPUnit\Framework\TestCase;
use LegacyFighter\Dietary\NewProducts\OldProduct;
use LegacyFighter\Dietary\NewProducts\Price;

class OldProductTest extends TestCase
{
    /**
     * @test
     */
    public function priceCannotBeNull()
    {
        $this->expectException(\Exception::class);

        Price::of(null);
    }

    /** @test */
    public function priceCannotBeNegative(): void
    {
        $this->expectException(\Exception::class);

        $this->productWithPriceAndCounter(BigDecimal::of(-100), 0);
    }

    /** @test */
    public function counterCannotBeNegative(): void
    {
        $this->expectException(\Exception::class);

        // todo: fix this
        $this->productWithPriceAndCounter(BigDecimal::one(), -100);
    }

    /** @test */
    public function descriptionCannotBeEmpty(): void
    {
        $this->expectException(\Exception::class);
        new OldProduct(BigDecimal::one(), null, 'long desc', 1);
    }

    /** @test */
    public function longDescriptionCannotBeEmpty(): void
    {
        $this->expectException(\Exception::class);
        new OldProduct(BigDecimal::one(), 'desc', null, 1);
    }

    /**
     * @test
     */
    public function canIncrementCounterIfPriceIsPositive(): void
    {
        //given
        $p = $this->productWithPriceAndCounter(BigDecimal::ten(), 10);

        //when
        $p->incrementCounter();

        //then
        $this->assertEquals(11, $p->getCounter());
    }

    /**
     * @test
     */
    public function cannotChangePriceIfCounterIsNotPositive(): void
    {
        //given
        $p = $this->productWithPriceAndCounter(BigDecimal::zero(), 0);

        //when
        $p->changePriceTo(BigDecimal::ten());

        //then
        $this->assertEquals(BigDecimal::zero(), $p->getPrice());
    }

    /** @test */
    public function canDecreaseCounter(): void
    {
        $p = $this->productWithPriceAndCounter(BigDecimal::one(), 100);

        $p->decrementCounter();

        $this->assertSame(99, $p->getCounter());
    }

    /** @test */
    public function cannotDecreaseCounterWhenPriceIsZero(): void
    {
        $p = $this->productWithPriceAndCounter(BigDecimal::zero(), 100);

        $this->expectException(\Exception::class);
        $p->decrementCounter();
    }

    /** @test */
    public function cannotDecreaseCounterWhenCounterIsLessThanOne(): void
    {
        $p = $this->productWithPriceAndCounter(BigDecimal::one(), 0);

        $this->expectException(\Exception::class);
        // todo: fix this
        $p->decrementCounter();
    }

    /** @test */
    public function canIncrementCounter(): void
    {
        $p = $this->productWithPriceAndCounter(BigDecimal::one(), 0);

        $p->incrementCounter();

        self::assertSame(1, $p->getCounter());
    }

    /** @test */
    public function cannotIncrementCounterWhenPriceIsZero(): void
    {
        $p = $this->productWithPriceAndCounter(BigDecimal::zero(), 0);
        $this->expectException(\Exception::class);
        $p->incrementCounter();
    }

    /**
     * @test
     */
    public function canFormatDescription(): void
    {
        //expect
        $this->assertEquals("short *** long", $this->productWithDesc("short", "long")->formatDesc());
        $this->assertEquals("", $this->productWithDesc("short", "")->formatDesc());
        $this->assertEquals("", $this->productWithDesc("", "long2")->formatDesc());
    }

    /**
     * @test
     */
    public function canChangeCharInDescription(): void
    {
        //given
        $p = $this->productWithDesc("short", "long");

        //when
        $p->replaceCharFromDesc('s', 'z');

        //expect
        $this->assertEquals("zhort *** long", $p->formatDesc());
    }

    /**
     * @param BigDecimal $price
     * @param int $counter
     * @return OldProduct
     */
    private function productWithPriceAndCounter(BigDecimal $price, int $counter): OldProduct
    {
        return new OldProduct($price, "desc", "longDesc", $counter);
    }

    /**
     * @param string $desc
     * @param string $longDesc
     * @return OldProduct
     */
    private function productWithDesc(string $desc, string $longDesc): OldProduct
    {
        return new OldProduct(BigDecimal::ten(), $desc, $longDesc, 10);
    }


}
