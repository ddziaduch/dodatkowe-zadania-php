<?php

declare(strict_types=1);

namespace LegacyFighter\Dietary;

class TaxRuleDto
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $taxCode;

    /**
     * @var bool
     */
    public $isLinear;

    /**
     * @var int
     */
    public $aFactor;

    /**
     * @var int
     */
    public $bFactor;

    /**
     * @var bool
     */
    public $isSquare;

    /**
     * @var int
     */
    public $aSquareFactor;

    /**
     * @var int
     */
    public $bSquareFactor;

    /**
     * @var int
     */
    public $cSquareFactor;

    public static function fromArray(array $data): self
    {
        $dto = new self();

        $dto->id = $data['id'];
        $dto->taxCode = $data['taxCode'];
        $dto->isLinear = $data['isLinear'];
        $dto->aFactor = $data['aFactor'];
        $dto->bFactor = $data['bFactor'];
        $dto->isSquare = $data['isSquare'];
        $dto->aSquareFactor = $data['aSquareFactor'];
        $dto->bSquareFactor = $data['bSquareFactor'];
        $dto->cSquareFactor = $data['cSquareFactor'];

        return $dto;
    }
}
