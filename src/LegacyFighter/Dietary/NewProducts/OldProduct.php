<?php

namespace LegacyFighter\Dietary\NewProducts;

use Brick\Math\BigDecimal;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * odpowiedzialności:
 * - cena nie może być pusta [null]
 * - cena nie może być ujemna
 * - licznik musi być nie ujemny
 * - opis i długi opis nie moga być puste [null]
 * - licznik można pomniejszyć tylko gdy cena jest niezerowa i licznik jest większy od zera
 * - licznik można powiększyć tylko gdy cena jest niezerowa
 * - cenę można zmienić tylko gdy licznik jest większy niż zero
 * - można zmienić znak w opisie i długim opisie
 * - można sformatować opis
 *
 * problemy:
 * - licznik i cena są ze sobą silnie związane
 * - opis i długi opis można wynieść do osobnego obiektu
 * - konstruktor ma parametry które mogą być null co nie pozwoli na stworzenie poprawnego obiektu
 */
class OldProduct
{
    /**
     * @var UuidInterface
     */
    private $serialNumber;

    /**
     * @var Price
     */
    private $price;

    /**
     * @var OldProductDescription
     */
    private $desc;

    /**
     * @var Counter
     */
    private $counter;

    /**
     * OldProduct constructor.
     * @param BigDecimal|null $price
     * @param string|null $desc
     * @param string|null $longDesc
     * @param int|null $counter
     */
    public function __construct(?BigDecimal $price, ?string $desc, ?string $longDesc, ?int $counter)
    {
        $this->serialNumber = Uuid::uuid4();
        $this->price = Price::of($price);
        $this->desc = new OldProductDescription($desc, $longDesc);
        $this->counter = new Counter($counter);
    }

    /**
     * @throws \Exception
     */
    public function decrementCounter(): void
    {
        if ($this->price->isNotZero()) {
            $this->counter = $this->counter->decrement();
        } else {
            throw new \Exception("price is zero");
        }
    }

    /**
     * @throws \Exception
     */
    public function incrementCounter(): void
    {
        if ($this->price->isNotZero()) {
            $this->counter = $this->counter->increment();
        } else {
            throw new \Exception("price is zero");
        }

    }

    /**
     * @param BigDecimal|null $newPrice
     * @throws \Exception
     */
    public function changePriceTo(?BigDecimal $newPrice): void
    {
        if ($this->counter->hasAny()) {
            $this->price = Price::of($newPrice);
        }
    }

    /**
     * @param string|null $charToReplace
     * @param string|null $replaceWith
     * @throws \Exception
     */
    public function replaceCharFromDesc(?string $charToReplace, ?string $replaceWith): void
    {
        $this->desc = $this->desc->replace($charToReplace, $replaceWith);
    }

    /**
     * @return string
     */
    public function formatDesc(): string
    {
        return $this->desc->formatted();
    }

    /**
     * @return BigDecimal
     */
    public function getPrice(): BigDecimal
    {
        return $this->price->getAsBigDecimal();
    }

    /**
     * @return int
     */
    public function getCounter(): int
    {
        return $this->counter->getIntValue();
    }

    /**
     * @return UuidInterface
     */
    public function serialNumber(): UuidInterface
    {
        return $this->serialNumber;
    }
}
