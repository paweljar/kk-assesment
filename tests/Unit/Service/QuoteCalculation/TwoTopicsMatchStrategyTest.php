<?php

declare(strict_types=1);

namespace Unit\Service\QuoteCalculation;

use App\Service\QuoteCalculation\TwoTopicsMatchStrategy;
use PHPUnit\Framework\TestCase;

class TwoTopicsMatchStrategyTest extends TestCase
{
    private TwoTopicsMatchStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new TwoTopicsMatchStrategy();
    }

    /**
     * @dataProvider supportsProvider
     */
    public function testSupports(int $matchCount, bool $expectedResult): void
    {
        $result = $this->strategy->supports($matchCount);
        $this->assertSame($expectedResult, $result);
    }

    public static function supportsProvider(): array
    {
        return [
            'supports two matches' => [2, true],
            'does not support zero matches' => [0, false],
            'does not support one match' => [1, false],
            'does not support three matches' => [3, false],
        ];
    }

    /**
     * @dataProvider calculateProvider
     */
    public function testCalculate(array $matchingTopics, array $top3Topics, array $topicRanks, float $expectedResult): void
    {
        $result = $this->strategy->calculate($matchingTopics, $top3Topics, $topicRanks);
        $this->assertSame($expectedResult, $result);
    }

    public static function calculateProvider(): array
    {
        return [
            'calculates rate for two matching topics' => [
                ['topic1', 'topic2'],
                ['topic1' => 50, 'topic2' => 20, 'topic3' => 10],
                [], // topicRanks not used in current implementation
                7, // (50 + 20) * 0.10
            ],
            'calculates rate for different values' => [
                ['topic1', 'topic3'],
                ['topic1' => 70, 'topic2' => 50, 'topic3' => 30],
                [], // topicRanks not used in current implementation
                10, // (70 + 30) * 0.10
            ],
        ];
    }
}
