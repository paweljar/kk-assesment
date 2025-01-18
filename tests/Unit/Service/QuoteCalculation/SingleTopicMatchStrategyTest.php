<?php

declare(strict_types=1);

namespace Unit\Service\QuoteCalculation;

use App\Service\QuoteCalculation\SingleTopicMatchStrategy;
use PHPUnit\Framework\TestCase;

class SingleTopicMatchStrategyTest extends TestCase
{
    private SingleTopicMatchStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new SingleTopicMatchStrategy();
    }

    /**
     * @dataProvider supportsProvider
     */
    public function testSupports(int $matchCount, bool $expectedResult): void
    {
        $this->assertSame($expectedResult, $this->strategy->supports($matchCount));
    }

    /**
     * @dataProvider calculateProvider
     */
    public function testCalculate(
        array $matchingTopics,
        array $top3Topics,
        array $topicRanks,
        float $expectedResult,
    ): void {
        $result = $this->strategy->calculate($matchingTopics, $top3Topics, $topicRanks);
        $this->assertSame($expectedResult, $result);
    }

    public static function supportsProvider(): array
    {
        return [
            'supports single match' => [1, true],
            'does not support zero matches' => [0, false],
            'does not support multiple matches' => [2, false],
        ];
    }

    public static function calculateProvider(): array
    {
        return [
            'highest ranked topic' => [
                ['topic1'],
                ['topic1' => 100],
                ['topic1', 'topic2', 'topic3'],
                20.0, // 100 * 0.20
            ],
            'second highest ranked topic' => [
                ['topic2'],
                ['topic2' => 80],
                ['topic1', 'topic2', 'topic3'],
                20.0, // 80 * 0.25
            ],
            'third highest ranked topic' => [
                ['topic3'],
                ['topic3' => 60],
                ['topic1', 'topic2', 'topic3'],
                18.0, // 60 * 0.30
            ],
            'unranked topic' => [
                ['topic4'],
                ['topic4' => 40],
                ['topic1', 'topic2', 'topic3'],
                0.0,
            ],
        ];
    }
}
