<?php

declare(strict_types=1);

namespace App\Controller;

use App\Request\CalculateQuotesRequest;
use App\Service\QuoteCalculatorService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

readonly class BundleQuoteController
{
    public function __construct(
        private QuoteCalculatorService $quoteCalculator,
    ) {
    }

    #[Route('/api/bundle-quotes', name: 'calculate_bundle_quotes', methods: ['POST'])]
    public function calculateQuotes(CalculateQuotesRequest $request): JsonResponse
    {
        $quotes = $this->quoteCalculator->calculateQuotes($request->toDTO());

        return new JsonResponse(['quotes' => $quotes]);
    }
}
