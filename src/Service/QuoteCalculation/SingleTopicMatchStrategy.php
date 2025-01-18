<?php

declare(strict_types=1);

namespace App\Service\QuoteCalculation;

class SingleTopicMatchStrategy implements QuoteCalculationStrategyInterface
{
    private const HIGHEST_TOPIC_RATE = 0.20;
    private const SECOND_HIGHEST_TOPIC_RATE = 0.25;
    private const THIRD_HIGHEST_TOPIC_RATE = 0.30;

    public function supports(int $matchCount): bool
    {
        return 1 === $matchCount;
    }

    public function calculate(array $matchingTopics, array $top3Topics, array $topicRanks): float
    {
        $matchedTopic = current($matchingTopics);
        $topicRank = array_search($matchedTopic, $topicRanks);

        return match ($topicRank) {
            0 => $top3Topics[$matchedTopic] * self::HIGHEST_TOPIC_RATE,
            1 => $top3Topics[$matchedTopic] * self::SECOND_HIGHEST_TOPIC_RATE,
            2 => $top3Topics[$matchedTopic] * self::THIRD_HIGHEST_TOPIC_RATE,
            default => 0,
        };
    }
}
