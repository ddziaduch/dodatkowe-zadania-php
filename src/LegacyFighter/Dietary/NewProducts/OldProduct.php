<?php

namespace LegacyFighter\Dietary\NewProducts;

use Brick\Math\BigDecimal;
use Ramsey\Uuid\Uuid;

class OldProduct
{
    private $serialNumber;

    private $price;

    private $desc;

    private $longDesc;

    private $counter;

    /**
     * @throws \Exception
     */
    public function decrementCounter(): void
    {
        $this->modifyCounter($this->counter - 1);
    }

    public function __construct(?BigDecimal $price, ?string $desc, ?string $longDesc, ?int $counter)
    {
        $this->serialNumber = Uuid::uuid4();
        $this->price = $price;
        $this->desc = $desc;
        $this->longDesc = $longDesc;
        $this->counter = $counter;
    }

    /**
     * @throws \Exception
     */
    public function incrementCounter(): void
    {
        $this->modifyCounter($this->counter + 1);
    }

    public function changePriceTo(?BigDecimal $newPrice): void
    {
        if ($this->counter === null) {
            throw new \Exception("null counter");
        }
        if
        ($this->counter > 0) {
            if ($newPrice === null) {
                throw new \Exception("new price null");
            }
            $this->price = $newPrice;
        }
    }

    /**
     * @param string|null $charToReplace
     * @param string|null $replaceWith
     * @throws \Exception
     */
    public function replaceCharFromDesc(?string $charToReplace, ?string $replaceWith): void
    {
        if ($this->longDesc === null || empty($this->longDesc) ||

            $this->desc === null || empty($this->desc)) {
            throw new \Exception("null or empty desc");
        }
        $this->longDesc = str_replace($charToReplace, $replaceWith, $this->longDesc);
        $this->desc = str_replace($charToReplace, $replaceWith, $this->desc);
    }

    /**
     * @return string
     */
    public function formatDesc(): string {
        if ($this->longDesc === null ||
               empty($this->longDesc) ||
               $this->desc === null
               || empty($this->desc)) {
            return "";
        }
        return $this->desc . " *** " . $this->longDesc;
    }

    /**
     * @throws \Exception
     */
    private function modifyCounter(int $newValue): void
    {
        if ($this->price == null || $this->price->getSign() <= 0) {
            throw new \Exception("Invalid price");
        }

        if ($this->counter === null) {
            throw new \Exception("null counter");
        }

        if ($newValue < 0) {
            throw new \Exception("Negative counter");
        }

        $this->counter = $newValue;
    }
}
