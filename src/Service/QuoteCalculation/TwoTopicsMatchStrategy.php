<?php

declare(strict_types=1);

namespace App\Service\QuoteCalculation;

readonly class TwoTopicsMatchStrategy implements QuoteCalculationStrategyInterface
{
    private const float TWO_TOPICS_RATE = 0.10;

    public function supports(int $matchCount): bool
    {
        return 2 === $matchCount;
    }

    public function calculate(array $matchingTopics, array $top3Topics, array $topicRanks): float
    {
        $sum = 0;
        foreach ($matchingTopics as $topic) {
            $sum += $top3Topics[$topic];
        }

        return $sum * self::TWO_TOPICS_RATE;
    }
}
