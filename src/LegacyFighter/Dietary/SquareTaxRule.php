<?php

declare(strict_types=1);

namespace LegacyFighter\Dietary;

class SquareTaxRule extends TaxRule
{
    /**
     * @var int
     */
    private $aSquareFactor;

    /**
     * @var int
     */
    private $bSquareFactor;

    /**
     * @var int
     */
    private $cSquareFactor;

    /**
     * @throws \Exception
     */
    public function __construct(int $aFactor, int $bFactor, int $cFactor, int $year, string $taxCode)
    {
        if ($aFactor == 0) {
            throw new \Exception("Invalid aFactor");
        }

        parent::__construct($year, $taxCode);

        $this->aSquareFactor = $aFactor;
        $this->bSquareFactor = $bFactor;
        $this->cSquareFactor = $cFactor;
    }

    /**
     * @return int
     */
    public function getaSquareFactor(): int
    {
        return $this->aSquareFactor;
    }

    /**
     * @return int
     */
    public function getbSquareFactor(): int
    {
        return $this->bSquareFactor;
    }

    /**
     * @return int
     */
    public function getcSquareFactor(): int
    {
        return $this->cSquareFactor;
    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
               'aSquareFactor' => $this->aSquareFactor,
                'bSquareFactor' => $this->bSquareFactor,
                'cSquareFactor' => $this->cSquareFactor,
            ]
        );
    }
}
