<?php

declare(strict_types=1);

namespace App\Service\QuoteCalculation;

readonly class QuoteCalculationStrategyResolver
{
    /** @param iterable<QuoteCalculationStrategyInterface> $strategies */
    public function __construct(private iterable $strategies)
    {
    }

    public function getStrategy(int $matchCount): ?QuoteCalculationStrategyInterface
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($matchCount)) {
                return $strategy;
            }
        }

        return null;
    }
}
