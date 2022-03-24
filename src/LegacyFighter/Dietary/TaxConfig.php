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
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $countryReason;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var \DateTime
     */
    private $lastModifiedDate;

    /**
     * @var int
     */
    private $currentRulesCount;

    /**
     * @var int
     */
    private $maxRulesCount;

    /**
     * @var GenericList
     */
    private $taxRules;

    /**
     * TaxConfig constructor.
     */
    private function __construct(string $countryCode, TaxRule $taxRule, int $maxRulesCount = 10)
    {
        $this->id = random_int(0, PHP_INT_MAX); // SHORTCUT
        $this->taxRules = GenericList::of($taxRule);
        $this->countryCode = $countryCode;
        $this->currentRulesCount = 1;
        $this->maxRulesCount = $maxRulesCount;
        $this->lastModifiedDate = new \DateTime();
    }

    public static function withDefaultMaxRuleCount(
        string $countryCode,
        TaxRule $taxRule
    ): self {
        return new self($countryCode, $taxRule);
    }

    public static function withCustomMaxRulesCount(
        string $countryCode,
        int $maxRulesCount,
        TaxRule $taxRule
    ): self {
        return new self($countryCode, $taxRule, $maxRulesCount);
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getCountryReason(): string
    {
        return $this->countryReason;
    }

    /**
     * @param string $countryReason
     */
    public function setCountryReason(string $countryReason): void
    {
        $this->countryReason = $countryReason;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return \DateTime
     */
    public function getLastModifiedDate(): \DateTime
    {
        return $this->lastModifiedDate;
    }

    /**
     * @param \DateTime $lastModifiedDate
     */
    public function setLastModifiedDate(\DateTime $lastModifiedDate): void
    {
        $this->lastModifiedDate = $lastModifiedDate;
    }

    /**
     * @return int
     */
    public function getCurrentRulesCount(): int
    {
        return $this->currentRulesCount;
    }

    /**
     * @param int $currentRulesCount
     */
    public function setCurrentRulesCount(int $currentRulesCount): void
    {
        $this->currentRulesCount = $currentRulesCount;
    }

    /**
     * @return int
     */
    public function getMaxRulesCount(): int
    {
        return $this->maxRulesCount;
    }

    /**
     * @return GenericList
     */
    public function getTaxRules(): GenericList
    {
        return $this->taxRules;
    }

    /**
     * @param GenericList $taxRules
     */
    public function setTaxRules(GenericList $taxRules): void
    {
        $this->taxRules = $taxRules;
    }

    /**
     * @return int
     */
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
        $this->currentRulesCount = $this->currentRulesCount + 1;
        $this->lastModifiedDate = new \DateTime();
    }
}
