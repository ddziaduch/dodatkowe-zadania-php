<?php

namespace LegacyFighter\Dietary\Repository;

use LegacyFighter\Dietary\CountryCode;
use LegacyFighter\Dietary\TaxConfig;
use LegacyFighter\Dietary\TaxConfigRepository;

class InMemoryTaxConfigRepository implements TaxConfigRepository
{
    /**
     * @var TaxConfig[]
     */
    private $taxConfigs = [];

    public function findByCountryCode(CountryCode $countryCode): ?TaxConfig
    {
        foreach ($this->taxConfigs as $taxConfig) {
            if ($countryCode->isEqual($taxConfig->getCountryCode())) {
                return $taxConfig;
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        return array_values($this->taxConfigs);
    }

    /**
     * @param TaxConfig $taxConfig
     * @return TaxConfig
     */
    public function save(TaxConfig $taxConfig): TaxConfig
    {
        $this->taxConfigs[$taxConfig->getId()] = $taxConfig;

        return $taxConfig;
    }

    /**
     * @param int $configId
     * @return TaxConfig
     */
    public function getOne(int $configId): TaxConfig
    {
        return $this->taxConfigs[$configId];
    }
}
