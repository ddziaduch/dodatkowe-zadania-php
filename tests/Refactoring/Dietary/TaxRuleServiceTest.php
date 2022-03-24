<?php

declare(strict_types=1);

namespace LegacyFighter\Dietary\Tests;

use LegacyFighter\Dietary\Repository\InMemoryOrderRepository;
use LegacyFighter\Dietary\Repository\InMemoryTaxConfigRepository;
use LegacyFighter\Dietary\Repository\InMemoryTaxRuleRepository;
use LegacyFighter\Dietary\TaxConfig;
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

    public function testCanCreateTaxConfigWithRule(): void
    {
        $taxRule = new TaxRule();
        $taxConfig = $this->taxRuleService->createTaxConfigWithRule(self::COUNTRY_CODE, $taxRule);
        self::assertSame(self::COUNTRY_CODE, $taxConfig->getCountryCode());
        $rules = $taxConfig->getTaxRules()->toArray();
        self::assertEquals([$taxRule], $rules);
        self::assertSame(1, $taxConfig->getCurrentRulesCount());
        self::assertSame(10, $taxConfig->getMaxRulesCount());
    }

    public function testCanCreateTaxConfigWithRuleAndCustomMaxRuleCount(): void
    {
        $taxRule = new TaxRule();
        $taxConfig = $this->taxRuleService->createTaxConfigWithRuleAndMaxCount(self::COUNTRY_CODE, 15, $taxRule);
        self::assertSame(self::COUNTRY_CODE, $taxConfig->getCountryCode());
        $rules = $taxConfig->getTaxRules()->toArray();
        self::assertEquals([$taxRule], $rules);
        self::assertSame(1, $taxConfig->getCurrentRulesCount());
        self::assertSame(15, $taxConfig->getMaxRulesCount());
    }

    public function testCanAddLinearTaxRuleToCountryThatHaveNoRulesYet(): void
    {
        $this->taxRuleService->addTaxRuleToCountry(self::COUNTRY_CODE, 3, 4, 'efg');

        $this->assertRulesCount(1);
        $this->assertRuleIsLinear(
            3,
            4,
            'A. 899. 2022efg',
            $this->getLastRule()
        );
    }

    public function testCanAddLinearTaxRuleToCountry(): void
    {
        $this->aTaxConfigWithLinearRules(1);

        $this->taxRuleService->addTaxRuleToCountry(self::COUNTRY_CODE, 1, 2, 'abc');

        $this->assertRulesCount(2);
        $this->assertRuleIsLinear(
            1,
            2,
            'A. 899. 2022abc',
            $this->getLastRule()
        );
    }

    public function testCannotAddLinearTaxRuleToCountryWithZeroAsFactorA(): void
    {
        $this->aTaxConfigWithLinearRules(1);

        $this->expectExceptionObject(new \Exception('Invalid aFactor'));
        $this->taxRuleService->addTaxRuleToCountry(self::COUNTRY_CODE, 0, 2, 'abc');
    }

    public function testCannotAddLinearRuleWhenCountryMaxRules(): void
    {
        $this->aTaxConfigWithLinearRules(10);
        $this->expectExceptionObject(new \Exception('Too many rules'));
        $this->taxRuleService->addTaxRuleToCountry(self::COUNTRY_CODE, 1, 2, 'abc');
    }

    public function testCannotAddSquareRuleWhenCountryMaxRules(): void
    {
        $this->aTaxConfigWithLinearRules(10);
        $this->expectExceptionObject(new \Exception('Too many rules'));
        $this->taxRuleService->addTaxRuleToCountry2(self::COUNTRY_CODE, 1, 1, 1, 'abc');
    }

    public function testCanAddSquareTaxRuleToCountryThatHaveNoRulesYet(): void
    {
        $this->taxRuleService->addTaxRuleToCountry2(self::COUNTRY_CODE, 5, 6, 7, 'xyz');

        $this->assertRulesCount(2);
        $rules = $this->getRules();
        $this->assertRuleIsSquare(
            5,
            6,
            7,
            'A. 899. 2022xyz',
            $rules[0]
        );
        // wtf, this is definitely a bug!
        $this->assertRuleIsSquare(
            5,
            6,
            7,
            'A. 899. 2022xyz',
            $rules[1]
        );
    }

    public function testCanAddSquareTaxRuleToCountry(): void
    {
        $this->aTaxConfigWithLinearRules(1);

        $this->taxRuleService->addTaxRuleToCountry2(self::COUNTRY_CODE, 8, 9, 10, 'ccc');

        $rules = $this->getRules();
        $this->assertRuleIsSquare(
            8,
            9,
            10,
            'A. 899. 2022ccc',
            $rules[1]
        );
    }

    public function testCanDeleteRuleIfCountryHasMoreThanOneRule(): void
    {
        $this->aTaxConfigWithLinearRules(2);

        $rule = $this->getLastRule();
        $taxConfig = $this->getTaxConfig();
        $this->taxRuleService->deleteRule($rule->getId(), $taxConfig->getId());
        self::assertCount(1, $this->getRules());
    }

    public function testCannotDeleteLastRule(): void
    {
        $this->aTaxConfigWithLinearRules(1);

        $rule = $this->getLastRule();
        $taxConfig = $this->getTaxConfig();
        $this->expectExceptionObject(new \Exception('Last rule in country config'));
        $this->taxRuleService->deleteRule($rule->getId(), $taxConfig->getId());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $taxConfigRepository = new InMemoryTaxConfigRepository();
        $orderRepository = new InMemoryOrderRepository();
        $taxRuleRepository = new InMemoryTaxRuleRepository($taxConfigRepository);

        $this->taxRuleService = new TaxRuleService($taxRuleRepository, $taxConfigRepository, $orderRepository);
    }

    private function aTaxConfigWithLinearRules(int $numberOfRules): void
    {
        for ($i = 1; $i <= $numberOfRules; $i++) {
            $this->taxRuleService->addTaxRuleToCountry(self::COUNTRY_CODE, $i, $i, (string) $i);
        }
    }

    private function assertRulesCount(int $count): void
    {
        $this->assertSame($count, $this->taxRuleService->rulesCount(self::COUNTRY_CODE));
    }

    private function assertRuleIsLinear(
        int $factorA,
        int $factorB,
        string $taxCode,
        TaxRule $rule
    ): void {
        self::assertTrue($rule->isLinear());
        self::assertSame($factorA, $rule->getaFactor());
        self::assertSame($factorB, $rule->getbFactor());
        self::assertSame($taxCode, $rule->getTaxCode());
    }

    /**
     * @return TaxRule[]
     */
    private function getRules(): array
    {
        return $this->taxRuleService->findRules(self::COUNTRY_CODE)->toArray();
    }

    private function getLastRule(): TaxRule
    {
        $rules = $this->getRules();
        $rule = end($rules);
        assert($rule instanceof TaxRule);

        return $rule;
    }

    private function assertRuleIsSquare(
        int $expectedFactorA,
        int $expectedFactorB,
        int $expectedFactorC,
        string $expectedTaxCode,
        TaxRule $rule
    ): void {
        self::assertTrue($rule->isSquare());
        self::assertSame($expectedFactorA, $rule->getaSquareFactor());
        self::assertSame($expectedFactorB, $rule->getbSquareFactor());
        self::assertSame($expectedFactorC, $rule->getcSquareFactor());
        self::assertSame($expectedTaxCode, $rule->getTaxCode());
    }

    private function getTaxConfig(): TaxConfig
    {
        $configs = $this->taxRuleService->findAllConfigs();

        return end($configs);
    }
}
