<?php

declare(strict_types=1);

namespace unit\Service;

use App\DTO\CalculateQuotesRequestDTO;
use App\Service\ProviderConfigurationService;
use App\Service\QuoteCalculation\QuoteCalculationStrategyResolver;
use App\Service\QuoteCalculation\SingleTopicMatchStrategy;
use App\Service\QuoteCalculation\TwoTopicsMatchStrategy;
use App\Service\QuoteCalculator;
use PHPUnit\Framework\TestCase;

class QuoteCalculatorTest extends TestCase
{
    private QuoteCalculator $calculator;
    private ProviderConfigurationService $providerConfig;
    private QuoteCalculationStrategyResolver $strategyResolver;

    protected function setUp(): void
    {
        $this->providerConfig = $this->createMock(ProviderConfigurationService::class);
        $this->strategyResolver = new QuoteCalculationStrategyResolver([
            new SingleTopicMatchStrategy(),
            new TwoTopicsMatchStrategy(),
        ]);
        $this->calculator = new QuoteCalculator($this->providerConfig, $this->strategyResolver);
    }

    public function testCalculateQuotes(): void
    {
        $request = CalculateQuotesRequestDTO::fromArray([
            'topics' => [
                'reading' => 20,
                'math' => 50,
                'science' => 30,
                'history' => 15,
                'art' => 10,
            ],
        ]);

        $this->providerConfig->method('getAllProviders')
            ->willReturn(['provider_a', 'provider_b', 'provider_c']);

        $this->providerConfig->method('getProviderTopics')
            ->willReturnMap([
                ['provider_a', ['math', 'science']],
                ['provider_b', ['reading', 'science']],
                ['provider_c', ['history', 'math']],
            ]);

        $quotes = $this->calculator->calculateQuotes($request);

        $this->assertEquals([
            'provider_a' => 8.0,    // 10% of 80 (math 50 + science 30)
            'provider_b' => 5.0,    // 25% of 20 (reading - third highest)
            'provider_c' => 12.5,   // 25% of 50 (math - highest)
        ], $quotes);
    }

    public function testZeroQuotesAreNotReturned(): void
    {
        $request = CalculateQuotesRequestDTO::fromArray([
            'topics' => [
                'reading' => 20,
                'math' => 50,
                'art' => 10,        // art is not in top 3, so providers matching only art should get 0
            ],
        ]);

        $this->providerConfig->method('getAllProviders')
            ->willReturn(['provider_a', 'provider_b', 'provider_art']);

        $this->providerConfig->method('getProviderTopics')
            ->willReturnMap([
                ['provider_a', ['math', 'science']],
                ['provider_b', ['reading', 'science']],
                ['provider_art', ['art']],  // this provider should get 0 and be excluded
            ]);

        $quotes = $this->calculator->calculateQuotes($request);

        $this->assertArrayNotHasKey('provider_art', $quotes, 'Provider with zero quote should not be included');
        $this->assertCount(2, $quotes, 'Only non-zero quotes should be returned');
    }
}
