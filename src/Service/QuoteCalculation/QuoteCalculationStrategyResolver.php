<?php

declare(strict_types=1);

namespace App\Service\QuoteCalculation;

class QuoteCalculationStrategyResolver
{
    /**
     * @var QuoteCalculationStrategyInterface[]
     */
    private array $strategies;

    public function __construct(iterable $strategies)
    {
        $this->strategies = is_array($strategies) ? $strategies : iterator_to_array($strategies);
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
