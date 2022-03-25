<?php

namespace LegacyFighter\Dietary;

use Munus\Collection\GenericList;

class TaxConfig
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var CountryCode
     */
    private $countryCode;

    /**
     * @var \DateTime
     */
    private $lastModifiedDate;

    /**
     * @var int
     */
    private $maxRulesCount;

    /**
     * @var GenericList<TaxRule>
     */
    private $taxRules;

    private function __construct(CountryCode $countryCode, TaxRule $taxRule, int $maxRulesCount = 10)
    {
        $this->id = random_int(0, PHP_INT_MAX); // SHORTCUT
        $this->taxRules = GenericList::of($taxRule);
        $this->countryCode = $countryCode;
        $this->maxRulesCount = $maxRulesCount;
        $this->lastModifiedDate = new \DateTime();
    }

    public static function withDefaultMaxRuleCount(
        CountryCode $countryCode,
        TaxRule $taxRule
    ): self {
        return new self($countryCode, $taxRule);
    }

    public static function withCustomMaxRulesCount(
        CountryCode $countryCode,
        int $maxRulesCount,
        TaxRule $taxRule
    ): self {
        return new self($countryCode, $taxRule, $maxRulesCount);
    }

    public function getCountryCode(): CountryCode
    {
        return $this->countryCode;
    }

    public function getCurrentRulesCount(): int
    {
        return $this->taxRules->length();
    }

    /**
     * @return GenericList<TaxRule>
     */
    public function getTaxRules(): GenericList
    {
        return $this->taxRules;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @throws \Exception
     */
    public function addRule(TaxRule $taxRule)
    {
        if ($this->maxRulesCount <= $this->taxRules->length()) {
            throw new \Exception("Too many rules");
        }

        $this->taxRules = $this->taxRules->append($taxRule);
        $this->lastModifiedDate = new \DateTime();
    }

    /**
     * @throws \Exception
     */
    public function deleteRule(int $taxRuleId)
    {
        if ($this->taxRules->length() === 1) {
            throw new \Exception('Last rule in country config');
        }

        $this->taxRules = $this->taxRules->filter(
            function (TaxRule $taxRule) use ($taxRuleId) {
                return $taxRule->getId() !== $taxRuleId;
            }
        );
        $this->lastModifiedDate = new \DateTime();
    }

    public function toArray(): array
    {
        return [
        'id' => $this->id,
        'countryCode' => (string) $this->countryCode,
        'maxRulesCount' => $this->maxRulesCount,
        'lastModifiedDate' => clone $this->lastModifiedDate,
        'taxRules' => $this->taxRules->toArray(),
        ];
    }
}
