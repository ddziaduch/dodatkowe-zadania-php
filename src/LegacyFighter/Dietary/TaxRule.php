<?php

namespace LegacyFighter\Dietary;

class TaxRule
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $taxCode;

    /**
     * @var bool
     */
    private $isLinear;

    /**
     * @var int
     */
    private $aFactor;

    /**
     * @var int
     */
    private $bFactor;

    /**
     * @var bool
     */
    private $isSquare;

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

    private function __construct(int $year, string $taxCode)
    {
        $this->id = random_int(0, PHP_INT_MAX); // SHORTCUT
        $this->taxCode = "A. 899. " . $year . $taxCode;
    }

    /**
     * @throws \Exception
     */
    public static function linear(
        int $aFactor,
        int $bFactor,
        int $year,
        string $taxCode
    ): self {
        if ($aFactor == 0) {
            throw new \Exception("Invalid aFactor");
        }

        $self = new self($year, $taxCode);

        $self->isLinear = true;
        $self->aFactor = $aFactor;
        $self->bFactor = $bFactor;

        return $self;
    }

    /**
     * @throws \Exception
     */
    public static function square(int $aFactor, int $bFactor, int $cFactor, int $year, string $taxCode): self
    {
        if ($aFactor == 0) {
            throw new \Exception("Invalid aFactor");
        }

        $self = new self($year, $taxCode);

        $self->aSquareFactor = $aFactor;
        $self->bSquareFactor = $bFactor;
        $self->cSquareFactor = $cFactor;
        $self->isSquare = true;

        return $self;
    }

    /**
     * @return bool
     */
    public function isLinear(): bool
    {
        return $this->isLinear;
    }

    /**
     * @return string
     */
    public function getTaxCode(): string
    {
        return $this->taxCode;
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

    /**
     * @return bool
     */
    public function isSquare(): bool
    {
        return $this->isSquare;
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

    /**
     * @param $o
     * @return bool
     */
    public function equals($o): bool
    {
        // :)

        if ($this == $o) {
            return true;
        }

        if (!($o instanceof TaxRule)) {
            return false;
        }

        return $this->taxCode == $o->getTaxCode();
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
            'isLinear' => $this->isLinear,
            'aFactor' => $this->aFactor,
            'bFactor' => $this->bFactor,
            'isSquare' => $this->isSquare,
            'aSquareFactor' => $this->aSquareFactor,
            'bSquareFactor' => $this->bSquareFactor,
            'cSquareFactor' => $this->cSquareFactor,
        ];
    }
}
