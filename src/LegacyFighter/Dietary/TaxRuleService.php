<?php

namespace LegacyFighter\Dietary;

class TaxRuleService
{
    /**
     * @var TaxConfigRepository
     */
    private $taxConfigRepository;

    /**
     * @var int
     */
    private $year;

    public function __construct(TaxConfigRepository $taxConfigRepository, int $year)
    {
        $this->taxConfigRepository = $taxConfigRepository;
        $this->year = $year;
    }

    /**
     * @throws \Exception
     */
    public function addLinearTaxRuleToCountry(string $countryCode, int $aFactor, int $bFactor, string $taxCode): void
    {
        $this->addTaxRuleToCountry(
            new CountryCode($countryCode),
            TaxRule::linear($aFactor, $bFactor, $this->year, $taxCode)
        );
    }

    /**
     * @throws \Exception
     */
    public function createTaxConfigWithRule(string $countryCode, TaxRule $taxRule): TaxConfigDto
    {
        $config = TaxConfig::withDefaultMaxRuleCount(new CountryCode($countryCode), $taxRule);
        $this->taxConfigRepository->save($config);

        return TaxConfigDto::fromArray($config->toArray());
    }

    /**
     * @throws \Exception
     */
    public function createTaxConfigWithRuleAndMaxCount(string $countryCode, int $maxRulesCount, TaxRule $taxRule): TaxConfigDto
    {
        $config = TaxConfig::withCustomMaxRulesCount(new CountryCode($countryCode), $maxRulesCount, $taxRule);

        $this->taxConfigRepository->save($config);

        return TaxConfigDto::fromArray($config->toArray());
    }

    /**
     * @throws \Exception
     */
    public function addSquareTaxRuleToCountry(string $countryCode, int $aFactor, int $bFactor, int $cFactor, string $taxCode): void
    {
        $this->addTaxRuleToCountry(
            new CountryCode($countryCode),
            TaxRule::square($aFactor, $bFactor, $cFactor, $this->year, $taxCode)
        );
    }

    /**
     * @throws \Exception
     */
    public function deleteRule(int $taxRuleId, int $configId) {
        $taxConfig = $this->taxConfigRepository->getOne($configId);

        $taxConfig->deleteRule($taxRuleId);

        $this->taxConfigRepository->save($taxConfig);
    }

    /**
     * @return array<TaxRuleDto>
     */
    public function findRules(string $countryCode): array
    {
        return array_map(
            function (TaxRule $rule) {
                return TaxRuleDto::fromArray($rule->toArray());
            },
            $this->taxConfigRepository->findByCountryCode(new CountryCode($countryCode))->getTaxRules()->toArray()
        );
    }

    /**
     * @return int
     */
    public function rulesCount(string $countryCode): int
    {
        return $this->taxConfigRepository->findByCountryCode(new CountryCode($countryCode))->getCurrentRulesCount();
    }

    /**
     * @return TaxConfigDto[]
     */
    public function findAllConfigs(): array
    {
        return array_map(
            function (TaxConfig $config) {
                return TaxConfigDto::fromArray($config->toArray());
            },
            $this->taxConfigRepository->findAll()
        );
    }

    /**
     * @throws \Exception
     */
    private function addTaxRuleToCountry(CountryCode $countryCode, TaxRule $taxRule): void
    {
        $taxConfig = $this->taxConfigRepository->findByCountryCode($countryCode);

        if ($taxConfig === null) {
            $taxConfig = TaxConfig::withDefaultMaxRuleCount($countryCode, $taxRule);
        } else {
            $taxConfig->addRule($taxRule);
        }

        $this->taxConfigRepository->save($taxConfig);
    }
}
