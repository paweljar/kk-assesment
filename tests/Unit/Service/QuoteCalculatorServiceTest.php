<?php

declare(strict_types=1);

namespace Unit\Service;

use App\DTO\CalculateQuotesRequestDTO;
use App\Service\ProviderConfigurationService;
use App\Service\QuoteCalculation\QuoteCalculationStrategyInterface;
use App\Service\QuoteCalculation\QuoteCalculationStrategyResolver;
use App\Service\QuoteCalculatorService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class QuoteCalculatorServiceTest extends TestCase
{
    private QuoteCalculatorService $service;
    private ProviderConfigurationService|MockObject $providerConfig;
    private QuoteCalculationStrategyResolver|MockObject $strategyResolver;
    private QuoteCalculationStrategyInterface|MockObject $strategy;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->providerConfig = $this->createMock(ProviderConfigurationService::class);
        $this->strategyResolver = $this->createMock(QuoteCalculationStrategyResolver::class);
        $this->strategy = $this->createMock(QuoteCalculationStrategyInterface::class);

        $this->service = new QuoteCalculatorService(
            $this->providerConfig,
            $this->strategyResolver
        );
    }

    /**
     * @throws Exception
     */
    public function testCalculateQuotesWithMatchingTopics(): void
    {
        // Arrange
        $top3Topics = ['topic1' => 80, 'topic2' => 60, 'topic3' => 40];
        $request = $this->createMock(CalculateQuotesRequestDTO::class);
        $request->expects($this->once())
            ->method('getTop3Topics')
            ->willReturn($top3Topics);

        $this->providerConfig->expects($this->once())
            ->method('getAllProviders')
            ->willReturn(['provider1', 'provider2']);

        $this->providerConfig
            ->expects($this->exactly(2))
            ->method('getProviderTopics')
            ->willReturnMap([
                ['provider1', ['topic1', 'topic2']],
                ['provider2', ['topic3']],
            ]);

        $this->strategyResolver->expects($this->exactly(2))
            ->method('getStrategy')
            ->willReturn($this->strategy);

        $this->strategy->expects($this->exactly(2))
            ->method('calculate')
            ->willReturnCallback(function ($providerTopics) {
                if ($providerTopics === ['topic1', 'topic2']) {
                    return 14;
                }
                if ($providerTopics === ['topic3']) {
                    return 12;
                }

                return 0.0;
            });

        $result = $this->service->calculateQuotes($request);

        $this->assertEquals([
            'provider1' => 14,
            'provider2' => 12,
        ], $result);
    }

    /**
     * @throws Exception
     */
    public function testCalculateQuotesWithNoMatchingTopics(): void
    {
        // Arrange
        $top3Topics = ['topic1' => 80, 'topic2' => 60, 'topic3' => 40];
        $request = $this->createMock(CalculateQuotesRequestDTO::class);
        $request->expects($this->once())->method('getTop3Topics')->willReturn($top3Topics);

        $this->providerConfig->expects($this->once())->method('getAllProviders')
            ->willReturn(['provider1']);

        $this->providerConfig->expects($this->once())->method('getProviderTopics')
            ->willReturn(['topic4', 'topic5']);

        $this->strategyResolver->expects($this->once())->method('getStrategy')
            ->willReturn($this->strategy);

        $this->strategy->expects($this->once())->method('calculate')
            ->willReturn(0.0);

        $result = $this->service->calculateQuotes($request);

        $this->assertEmpty($result);
    }

    /**
     * @throws Exception
     */
    public function testCalculateQuotesWithNullStrategy(): void
    {
        // Arrange
        $top3Topics = ['topic1' => 80, 'topic2' => 60, 'topic3' => 40];
        $request = $this->createMock(CalculateQuotesRequestDTO::class);
        $request->method('getTop3Topics')->willReturn($top3Topics);

        $this->providerConfig->method('getAllProviders')
            ->willReturn(['provider1']);

        $this->providerConfig->method('getProviderTopics')
            ->willReturn(['topic1']);

        $this->strategyResolver->method('getStrategy')
            ->willReturn(null);

        $result = $this->service->calculateQuotes($request);

        $this->assertEmpty($result);
    }
}
