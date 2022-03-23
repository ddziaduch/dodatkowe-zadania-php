<?php

declare(strict_types=1);

namespace LegacyFighter\Dietary\Tests;

use LegacyFighter\Dietary\Repository\InMemoryOrderRepository;
use LegacyFighter\Dietary\Repository\InMemoryTaxConfigRepository;
use LegacyFighter\Dietary\Repository\InMemoryTaxRuleRepository;
use LegacyFighter\Dietary\TaxRule;
use LegacyFighter\Dietary\TaxRuleService;
use PHPUnit\Framework\TestCase;

final class TaxRuleServiceTest extends TestCase
{
    private const COUNTRY_CODE = 'PL';

    /**
     * @var TaxRuleService
     */
    private $taxRuleService;

    public function testCanAddLinearTaxRuleToCountry(): void
    {
        $this->aTaxConfigWithRules(1);

        $this->taxRuleService->addTaxRuleToCountry(self::COUNTRY_CODE, 1, 2, 'abc');

        $this->assertRulesCount(2);
        $this->assertLastRuleIsLinear(1, 2, 'A. 899. 2022abc');
    }

    public function testCannotAddLinearTaxRuleToCountryWithZeroAsFactorA(): void
    {
        $this->aTaxConfigWithRules(1);

        $this->expectExceptionObject(new \Exception('Invalid aFactor'));
        $this->taxRuleService->addTaxRuleToCountry(self::COUNTRY_CODE, 0, 2, 'abc');
    }

    public function testCanCreateTaxConfigWith10MaxRulesCountAsByDefault(): void
    {
        $this->aTaxConfigWithRules(10);

        $this->expectExceptionObject(new \Exception('Too many rules'));
        $this->taxRuleService->addTaxRuleToCountry(self::COUNTRY_CODE, 1, 2, 'abc');
    }

    public function testCanAddLinearTaxRuleToCountryThatHaveNoRulesYet(): void
    {
        $this->taxRuleService->addTaxRuleToCountry(self::COUNTRY_CODE, 3, 4, 'efg');

        $this->assertRulesCount(1);
        $this->assertLastRuleIsLinear(3, 4, 'A. 899. 2022efg');
    }

    public function canCreateTaxConfigWithCustomMaxRuleCount(): void
    {
        $this->taxRuleService->createTaxConfigWithRuleAndMaxCount(self::COUNTRY_CODE, 15, new TaxRule());
        $this->aTaxConfigWithRules(14);

        $this->expectExceptionObject(new \Exception('Too many rules'));
        $this->taxRuleService->addTaxRuleToCountry(self::COUNTRY_CODE, 1, 1, 'abc');
    }

    private function aTaxConfigWithRules(int $numberOfRules): void
    {
        for ($i = 0; $i < $numberOfRules; $i++) {
            $this->taxRuleService->addTaxRuleToCountry(self::COUNTRY_CODE, $i, $i, (string) $i);
        }
    }

    private function assertRulesCount(int $count): void
    {
        $this->assertSame($count, $this->taxRuleService->rulesCount(self::COUNTRY_CODE));
    }

    private function assertLastRuleIsLinear(int $factorA, int $factorB, string $taxCode): void
    {
        $rules = $this->taxRuleService->findRules(self::COUNTRY_CODE)->toArray();
        $rule = end($rules);
        assert($rule instanceof TaxRule);
        self::assertTrue($rule->isLinear());
        self::assertSame($factorA, $rule->getaFactor());
        self::assertSame($factorB, $rule->getbFactor());
        self::assertSame($taxCode, $rule->getTaxCode());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $taxConfigRepository = new InMemoryTaxConfigRepository();
        $orderRepository = new InMemoryOrderRepository();
        $taxRuleRepository = new InMemoryTaxRuleRepository($taxConfigRepository);

        $this->taxRuleService = new TaxRuleService($taxRuleRepository, $taxConfigRepository, $orderRepository);
    }
}
