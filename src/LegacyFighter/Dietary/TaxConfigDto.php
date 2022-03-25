<?php

declare(strict_types=1);

namespace LegacyFighter\Dietary;

class TaxConfigDto
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $countryCode;

    /**
     * @var \DateTime
     */
    public $lastModifiedDate;

    /**
     * @var int
     */
    public $maxRulesCount;

    /**
     * @var array<TaxRule>
     */
    public $taxRules;

    public static function fromArray(array $data): self
    {
        $dto = new self();

        $dto->id = $data['id'];
        $dto->countryCode = $data['countryCode'];
        $dto->maxRulesCount = $data['maxRulesCount'];
        $dto->lastModifiedDate = $data['lastModifiedDate'];
        $dto->taxRules = $data['taxRules'];

        return $dto;
    }
}
