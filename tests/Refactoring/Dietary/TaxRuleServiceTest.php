<?php

declare(strict_types=1);

namespace LegacyFighter\Dietary\Tests;

use LegacyFighter\Dietary\Repository\InMemoryTaxConfigRepository;
use LegacyFighter\Dietary\TaxConfigDto;
use LegacyFighter\Dietary\TaxRule;
use LegacyFighter\Dietary\TaxRuleDto;
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
        $taxRule = TaxRule::linear(1, 1, 2022, 'abc');
        $dto = $this->taxRuleService->createTaxConfigWithRule(self::COUNTRY_CODE, $taxRule);
        self::assertSame(self::COUNTRY_CODE, $dto->countryCode);
        $rules = $dto->taxRules;
        self::assertEquals([$taxRule], $rules);
        self::assertSame(10, $dto->maxRulesCount);
    }

    public function testCanCreateTaxConfigWithRuleAndCustomMaxRuleCount(): void
    {
        $taxRule = TaxRule::linear(1, 1, 2022, 'abc');
        $dto = $this->taxRuleService->createTaxConfigWithRuleAndMaxCount(self::COUNTRY_CODE, 15, $taxRule);
        self::assertSame(self::COUNTRY_CODE, $dto->countryCode);
        $rules = $dto->taxRules;
        self::assertEquals([$taxRule], $rules);
        self::assertSame(15, $dto->maxRulesCount);
    }

    public function testCanAddLinearTaxRuleToCountryThatHaveNoRulesYet(): void
    {
        $this->taxRuleService->addLinearTaxRuleToCountry(self::COUNTRY_CODE, 3, 4, 'efg');

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

        $this->taxRuleService->addLinearTaxRuleToCountry(self::COUNTRY_CODE, 1, 2, 'abc');

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
        $this->taxRuleService->addLinearTaxRuleToCountry(self::COUNTRY_CODE, 0, 2, 'abc');
    }

    public function testCannotAddLinearRuleWhenCountryMaxRules(): void
    {
        $this->aTaxConfigWithLinearRules(10);
        $this->expectExceptionObject(new \Exception('Too many rules'));
        $this->taxRuleService->addLinearTaxRuleToCountry(self::COUNTRY_CODE, 1, 2, 'abc');
    }

    public function testCannotAddSquareRuleWhenCountryMaxRules(): void
    {
        $this->aTaxConfigWithLinearRules(10);
        $this->expectExceptionObject(new \Exception('Too many rules'));
        $this->taxRuleService->addSquareTaxRuleToCountry(self::COUNTRY_CODE, 1, 1, 1, 'abc');
    }

    public function testCanAddSquareTaxRuleToCountryThatHaveNoRulesYet(): void
    {
        $this->taxRuleService->addSquareTaxRuleToCountry(self::COUNTRY_CODE, 5, 6, 7, 'xyz');

        $this->assertRulesCount(1);
        $rules = $this->getRules();
        $this->assertRuleIsSquare(
            5,
            6,
            7,
            'A. 899. 2022xyz',
            $rules[0]
        );
    }

    public function testCanAddSquareTaxRuleToCountry(): void
    {
        $this->aTaxConfigWithLinearRules(1);

        $this->taxRuleService->addSquareTaxRuleToCountry(self::COUNTRY_CODE, 8, 9, 10, 'ccc');

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
        $this->taxRuleService->deleteRule($rule->id, $taxConfig->id);
        self::assertCount(1, $this->getRules());
    }

    public function testCannotDeleteLastRule(): void
    {
        $this->aTaxConfigWithLinearRules(1);

        $rule = $this->getLastRule();
        $taxConfig = $this->getTaxConfig();
        $this->expectExceptionObject(new \Exception('Last rule in country config'));
        $this->taxRuleService->deleteRule($rule->id, $taxConfig->id);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->taxRuleService = new TaxRuleService(new InMemoryTaxConfigRepository(), 2022);
    }

    private function aTaxConfigWithLinearRules(int $numberOfRules): void
    {
        for ($i = 1; $i <= $numberOfRules; $i++) {
            $this->taxRuleService->addLinearTaxRuleToCountry(self::COUNTRY_CODE, $i, $i, (string) $i);
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
        TaxRuleDto $rule
    ): void {
        self::assertTrue($rule->isLinear);
        self::assertSame($factorA, $rule->aFactor);
        self::assertSame($factorB, $rule->bFactor);
        self::assertSame($taxCode, $rule->taxCode);
    }

    /**
     * @return TaxRuleDto[]
     */
    private function getRules(): array
    {
        return $this->taxRuleService->findRules(self::COUNTRY_CODE);
    }

    private function getLastRule(): TaxRuleDto
    {
        $rules = $this->getRules();
        $rule = end($rules);
        assert($rule instanceof TaxRuleDto);

        return $rule;
    }

    private function assertRuleIsSquare(
        int $expectedFactorA,
        int $expectedFactorB,
        int $expectedFactorC,
        string $expectedTaxCode,
        TaxRuleDto $rule
    ): void {
        self::assertTrue($rule->isSquare);
        self::assertSame($expectedFactorA, $rule->aSquareFactor);
        self::assertSame($expectedFactorB, $rule->bSquareFactor);
        self::assertSame($expectedFactorC, $rule->cSquareFactor);
        self::assertSame($expectedTaxCode, $rule->taxCode);
    }

    private function getTaxConfig(): TaxConfigDto
    {
        $configs = $this->taxRuleService->findAllConfigs();

        return end($configs);
    }
}
