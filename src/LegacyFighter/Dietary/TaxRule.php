<?php

declare(strict_types=1);

namespace LegacyFighter\Dietary;

abstract class TaxRule
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $taxCode;

    public function __construct(int $year, string $taxCode)
    {
        $this->id = random_int(0, PHP_INT_MAX); // SHORTCUT
        $this->taxCode = "A. 899. " . $year . $taxCode;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'taxCode' => $this->taxCode,
        ];
    }
}
