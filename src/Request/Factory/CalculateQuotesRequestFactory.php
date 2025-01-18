<?php

declare(strict_types=1);

namespace App\Request\Factory;

use App\Request\CalculateQuotesRequest;

readonly class CalculateQuotesRequestFactory
{
    public function createFromArray(array $data): CalculateQuotesRequest
    {
        return new CalculateQuotesRequest($data);
    }
}
