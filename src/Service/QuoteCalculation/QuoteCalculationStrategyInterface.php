<?php

declare(strict_types=1);

namespace App\Service\QuoteCalculation;

interface QuoteCalculationStrategyInterface
{
    public function supports(int $matchCount): bool;

    public function calculate(array $matchingTopics, array $top3Topics, array $topicRanks): float;
}
