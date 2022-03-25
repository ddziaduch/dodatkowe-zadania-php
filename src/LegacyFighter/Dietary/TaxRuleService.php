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
     * TaxRuleService constructor.
     *
     * @param TaxConfigRepository $taxConfigRepository
     */
    public function __construct(TaxConfigRepository $taxConfigRepository)
    {
        $this->taxConfigRepository = $taxConfigRepository;
    }

    public function addTaxRuleToCountry(string $countryCode, int $aFactor, int $bFactor, string $taxCode)
    {
        $countryCodeValueObject = new CountryCode($countryCode);

        if ($aFactor == 0) {
            throw new \Exception("Invalid aFactor");
        }

        $year = (int)date('Y');
        $taxRule = TaxRule::linear($aFactor, $bFactor, $year, $taxCode);
        $taxConfig = $this->taxConfigRepository->findByCountryCode($countryCodeValueObject);

        if ($taxConfig === null) {
            $this->createTaxConfigWithRule((string) $countryCodeValueObject, $taxRule);
        } else {
            $this->addTaxRuleToConfig($taxConfig, $taxRule);
        }
    }

    /**
     * @param string $countryCode
     * @param TaxRule $taxRule
     * @return TaxConfig
     * @throws \Exception
     */
    public function createTaxConfigWithRule(string $countryCode, TaxRule $taxRule): TaxConfig
    {
        $countryCodeValueObject = new CountryCode($countryCode);

        $taxConfig = TaxConfig::withDefaultMaxRuleCount($countryCodeValueObject, $taxRule);

        $this->taxConfigRepository->save($taxConfig);

        return $taxConfig;
    }

    public function createTaxConfigWithRuleAndMaxCount(string $countryCode, int $maxRulesCount, TaxRule $taxRule): TaxConfig
    {
        $countryCodeValueObject = new CountryCode($countryCode);

        $taxConfig = TaxConfig::withCustomMaxRulesCount($countryCodeValueObject, $maxRulesCount, $taxRule);

        $this->taxConfigRepository->save($taxConfig);

        return $taxConfig;
    }

    public function addTaxRuleToCountry2(string $countryCode, int $aFactor, int $bFactor, int $cFactor, string $taxCode): void
    {
        $countryCodeValueObject = new CountryCode($countryCode);

        if ($aFactor == 0) {
            throw new \Exception("Invalid aFactor");
        }

        $year = (int)date('Y');
        $taxRule = TaxRule::square($aFactor, $bFactor, $cFactor, $year, $taxCode);
        $taxConfig = $this->taxConfigRepository->findByCountryCode($countryCodeValueObject);

        if ($taxConfig === null) {
            $this->createTaxConfigWithRule((string) $countryCodeValueObject, $taxRule);
        } else {
            $this->addTaxRuleToConfig($taxConfig, $taxRule);
        }
    }

    /**
     * @param int $taxRuleId
     * @param int $configId
     * @throws \Exception
     */
    public function deleteRule(int $taxRuleId, int $configId) {
        $taxConfig = $this->taxConfigRepository->getOne($configId);

        $taxConfig->deleteRule($taxRuleId);

        $this->taxConfigRepository->save($taxConfig);
    }

    /**
     * @param string $countryCode
     * @return GenericList
     */
    public function findRules(string $countryCode): GenericList
    {
        return $this->taxConfigRepository->findByCountryCode(new CountryCode($countryCode))->getTaxRules();
    }

    /**
     * @param string $countryCode
     * @return int
     */
    public function rulesCount(string $countryCode): int
    {
        return $this->taxConfigRepository->findByCountryCode(new CountryCode($countryCode))->getCurrentRulesCount();
    }

    /**
     * @return array
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
