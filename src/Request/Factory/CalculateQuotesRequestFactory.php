<?php

declare(strict_types=1);

namespace App\Request\Factory;

use App\Request\CalculateQuotesRequest;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CalculateQuotesRequestFactory
{
    public function createFromArray(?array $data): CalculateQuotesRequest
    {
        if (empty($data['topics']) || !is_array($data['topics'])) {
            throw new BadRequestHttpException('Missing or invalid topics field');
        }

        foreach ($data['topics'] as $topic => $value) {
            if (!is_string($topic) || empty(trim($topic))) {
                throw new BadRequestHttpException('Topic keys must be non-empty strings');
            }
        }

        return new CalculateQuotesRequest($data);
    }
}
