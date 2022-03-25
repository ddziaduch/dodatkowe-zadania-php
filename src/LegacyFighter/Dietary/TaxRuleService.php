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
    public function addTaxRuleToCountry(string $countryCode, int $aFactor, int $bFactor, string $taxCode)
    {
        $countryCodeValueObject = new CountryCode($countryCode);

        if ($aFactor == 0) {
            throw new \Exception("Invalid aFactor");
        }

        $taxRule = TaxRule::linear($aFactor, $bFactor, $this->year, $taxCode);
        $taxConfig = $this->taxConfigRepository->findByCountryCode($countryCodeValueObject);

        if ($taxConfig === null) {
            $this->createTaxConfigWithRule((string) $countryCodeValueObject, $taxRule);
        } else {
            $this->addTaxRuleToConfig($taxConfig, $taxRule);
        }
    }

    /**
     * @throws \Exception
     */
    public function createTaxConfigWithRule(string $countryCode, TaxRule $taxRule): TaxConfig
    {
        $countryCodeValueObject = new CountryCode($countryCode);

        $taxConfig = TaxConfig::withDefaultMaxRuleCount($countryCodeValueObject, $taxRule);

        $this->taxConfigRepository->save($taxConfig);

        return $taxConfig;
    }

    /**
     * @throws \Exception
     */
    public function createTaxConfigWithRuleAndMaxCount(string $countryCode, int $maxRulesCount, TaxRule $taxRule): TaxConfig
    {
        $countryCodeValueObject = new CountryCode($countryCode);

        $taxConfig = TaxConfig::withCustomMaxRulesCount($countryCodeValueObject, $maxRulesCount, $taxRule);

        $this->taxConfigRepository->save($taxConfig);

        return $taxConfig;
    }

    /**
     * @throws \Exception
     */
    public function addTaxRuleToCountry2(string $countryCode, int $aFactor, int $bFactor, int $cFactor, string $taxCode): void
    {
        $countryCodeValueObject = new CountryCode($countryCode);

        $taxRule = TaxRule::square($aFactor, $bFactor, $cFactor, $this->year, $taxCode);
        $taxConfig = $this->taxConfigRepository->findByCountryCode($countryCodeValueObject);

        if ($taxConfig === null) {
            $this->createTaxConfigWithRule((string) $countryCodeValueObject, $taxRule);
        } else {
            $this->addTaxRuleToConfig($taxConfig, $taxRule);
        }
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

    private function addTaxRuleToConfig(TaxConfig $taxConfig, TaxRule $taxRule)
    {
        $taxConfig->addRule($taxRule);

        $this->taxConfigRepository->save($taxConfig);
    }
}
