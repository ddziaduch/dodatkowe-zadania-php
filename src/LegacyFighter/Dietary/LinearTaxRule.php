<?php

declare(strict_types=1);

namespace LegacyFighter\Dietary;

class LinearTaxRule extends TaxRule
{
    /**
     * @var int
     */
    private $aFactor;

    /**
     * @var int
     */
    private $bFactor;

    /**
     * @throws \Exception
     */
    public function __construct(
        int $aFactor,
        int $bFactor,
        int $year,
        string $taxCode
    ) {
        if ($aFactor == 0) {
            throw new \Exception("Invalid aFactor");
        }

        parent::__construct($year, $taxCode);

        $this->aFactor = $aFactor;
        $this->bFactor = $bFactor;
    }

    /**
     * @return int
     */
    public function getaFactor(): int
    {
        return $this->aFactor;
    }

    /**
     * @return int
     */
    public function getbFactor(): int
    {
        return $this->bFactor;
    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                'aFactor' => $this->aFactor,
                'bFactor' => $this->bFactor,
            ]
        );
    }
}
