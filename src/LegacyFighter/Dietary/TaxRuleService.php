<?php

namespace LegacyFighter\Dietary;

use Munus\Collection\GenericList;

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
    public function addLinearTaxRuleToCountry(string $countryCode, int $aFactor, int $bFactor, string $taxCode)
    {
        $this->addTaxRuleToCountry(
            new CountryCode($countryCode),
            TaxRule::linear($aFactor, $bFactor, $this->year, $taxCode)
        );
    }

    /**
     * @throws \Exception
     */
    public function createTaxConfigWithRule(string $countryCode, TaxRule $taxRule): TaxConfig
    {
        $taxConfig = TaxConfig::withDefaultMaxRuleCount(new CountryCode($countryCode), $taxRule);

        $this->taxConfigRepository->save($taxConfig);

        return $taxConfig;
    }

    /**
     * @throws \Exception
     */
    public function createTaxConfigWithRuleAndMaxCount(string $countryCode, int $maxRulesCount, TaxRule $taxRule): TaxConfig
    {
        $taxConfig = TaxConfig::withCustomMaxRulesCount(new CountryCode($countryCode), $maxRulesCount, $taxRule);

        $this->taxConfigRepository->save($taxConfig);

        return $taxConfig;
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
     * @return GenericList<TaxRule>
     */
    public function findRules(string $countryCode): GenericList
    {
        return $this->taxConfigRepository->findByCountryCode(new CountryCode($countryCode))->getTaxRules();
    }

    /**
     * @return int
     */
    public function rulesCount(string $countryCode): int
    {
        return $this->taxConfigRepository->findByCountryCode(new CountryCode($countryCode))->getCurrentRulesCount();
    }

    /**
     * @return TaxConfig[]
     */
    public function findAllConfigs(): array
    {
        return $this->taxConfigRepository->findAll();
    }

    /**
     * @throws \Exception
     */
    private function addTaxRuleToCountry(CountryCode $countryCode, TaxRule $taxRule): void
    {
        $taxConfig = $this->taxConfigRepository->findByCountryCode($countryCode);

        if ($taxConfig === null) {
            $this->createTaxConfigWithRule((string) $countryCode, $taxRule);
        } else {
            $taxConfig->addRule($taxRule);
            $this->taxConfigRepository->save($taxConfig);
        }
    }
}
