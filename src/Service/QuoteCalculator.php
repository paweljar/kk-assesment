<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CalculateQuotesRequestDTO;
use App\Service\QuoteCalculation\QuoteCalculationStrategyResolver;

readonly class QuoteCalculator
{
    public function __construct(
        private ProviderConfigurationService $providerConfig,
        private QuoteCalculationStrategyResolver $strategyResolver,
    ) {
    }

    public function calculateQuotes(CalculateQuotesRequestDTO $request): array
    {
        $quotes = [];
        $top3Topics = $request->getTop3Topics();
        $topicRanks = array_keys($top3Topics);

        foreach ($this->providerConfig->getAllProviders() as $provider) {
            $providerTopics = $this->providerConfig->getProviderTopics($provider);
            $matchingTopics = array_intersect($providerTopics, array_keys($top3Topics));
            $quote = $this->calculateProviderQuote($matchingTopics, $top3Topics, $topicRanks);

            if ($quote > 0) {
                $quotes[$provider] = $quote;
            }
        }

        return $quotes;
    }

    private function calculateProviderQuote(array $matchingTopics, array $top3Topics, array $topicRanks): float
    {
        $matchCount = count($matchingTopics);
        $strategy = $this->strategyResolver->getStrategy($matchCount);

        return $strategy?->calculate($matchingTopics, $top3Topics, $topicRanks) ?? 0;
    }
}
