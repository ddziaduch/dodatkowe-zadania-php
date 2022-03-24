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
        if ($countryCode === null || $countryCode == "" || strlen($countryCode) == 1) {
            throw new \Exception("Invalid country code");
        }
        if ($aFactor == 0) {
            throw new \Exception("Invalid aFactor");
        }

        $taxRule = new TaxRule();

        $taxRule->setaFactor($aFactor);
        $taxRule->setbFactor($bFactor);
        $taxRule->setLinear(true);
        $year = (int)date('Y');
        $taxRule->setTaxCode("A. 899. " . $year . $taxCode);
        $taxConfig = $this->taxConfigRepository->findByCountryCode($countryCode);

        if ($taxConfig == null) {
            $this->createTaxConfigWithRule($countryCode, $taxRule);

            return;
        }

        $taxConfig->addRule($taxRule);

        $this->taxConfigRepository->save($taxConfig);
    }

    /**
     * @param string $countryCode
     * @param TaxRule $taxRule
     * @return TaxConfig
     * @throws \Exception
     */
    public function createTaxConfigWithRule(string $countryCode, TaxRule $taxRule): TaxConfig
    {
        if ($countryCode == null || $countryCode == "" || strlen($countryCode) == 1) {
            throw new \Exception("Invalid country code");
        }

        $taxConfig = TaxConfig::withDefaultMaxRuleCount($countryCode, $taxRule);

        $this->taxConfigRepository->save($taxConfig);

        return $taxConfig;
    }

    public function createTaxConfigWithRuleAndMaxCount(string $countryCode, int $maxRulesCount, TaxRule $taxRule): TaxConfig
    {
        if ($countryCode == null || $countryCode == "" || strlen($countryCode) == 1) {
            throw new \Exception("Invalid country code");
        }

        $taxConfig = TaxConfig::withCustomMaxRulesCount($countryCode, $maxRulesCount, $taxRule);

        $this->taxConfigRepository->save($taxConfig);

        return $taxConfig;
    }

    public function addTaxRuleToCountry2(string $countryCode, int $aFactor, int $bFactor, int $cFactor, string $taxCode): void
    {
        if ($aFactor == 0) {
            throw new \Exception("Invalid aFactor");
        }

        if ($countryCode == null || $countryCode == "" || strlen($countryCode) == 1) {
            throw new \Exception("Invalid country code");
        }

        $taxRule = new TaxRule();
        $taxRule->setaSquareFactor($aFactor);
        $taxRule->setbSquareFactor($bFactor);
        $taxRule->setcSquareFactor($cFactor);
        $taxRule->setSquare(true);
        $year = (int)date('Y');
        $taxRule->setTaxCode("A. 899. " . $year . $taxCode);

        $taxConfig = $this->taxConfigRepository->findByCountryCode($countryCode);

        if ($taxConfig == null) {
            $taxConfig = $this->createTaxConfigWithRule($countryCode, $taxRule);
        }

        $taxConfig->addRule($taxRule);

        $this->taxConfigRepository->save($taxConfig);
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
        return $this->taxConfigRepository->findByCountryCode($countryCode)->getTaxRules();
    }

    /**
     * @param string $countryCode
     * @return int
     */
    public function rulesCount(string $countryCode): int
    {
        return $this->taxConfigRepository->findByCountryCode($countryCode)->getCurrentRulesCount();
    }

    /**
     * @return array
     */
    public function findAllConfigs(): array
    {
        return $this->taxConfigRepository->findAll();
    }
}
