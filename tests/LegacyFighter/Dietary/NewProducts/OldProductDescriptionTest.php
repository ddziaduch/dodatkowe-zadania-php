<?php

declare(strict_types=1);

namespace Tests\LegacyFighter\Dietary\NewProducts;

use LegacyFighter\Dietary\NewProducts\OldProductDescription;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class OldProductDescriptionTest extends TestCase
{
    /** @test */
    public function descriptionCannotBeEmpty(): void
    {
        $this->expectException(\TypeError::class);
        $this->oldProductDescription(null, 'long desc');
    }

    /** @test */
    public function longDescriptionCannotBeEmpty(): void
    {
        $this->expectException(\TypeError::class);
        $this->oldProductDescription('desc', null);
    }

    /**
     * @test
     */
    public function canFormatDescription(): void
    {
        //expect
        $this->assertEquals("short *** long", $this->oldProductDescription("short", "long")->formatted());
        $this->assertEquals("", $this->oldProductDescription("short", "")->formatted());
        $this->assertEquals("", $this->oldProductDescription("", "long2")->formatted());
    }

    /**
     * @test
     */
    public function canChangeCharInDescription(): void
    {
        //given
        $p = $this->oldProductDescription("short", "long");

        //when
        $p = $p->replace('s', 'z');

        //expect
        $this->assertEquals("zhort *** long", $p->formatted());
    }

    private function oldProductDescription(?string $desc, ?string $longDesc): OldProductDescription
    {
        return new OldProductDescription(Uuid::uuid4(), $desc, $longDesc);
    }


}
