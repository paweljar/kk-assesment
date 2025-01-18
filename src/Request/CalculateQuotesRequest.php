<?php

declare(strict_types=1);

namespace App\Request;

use App\DTO\CalculateQuotesRequestDTO;
use Symfony\Component\Validator\Constraints as Assert;

class CalculateQuotesRequest
{
    #[Assert\NotNull]
    #[Assert\Type('array')]
    #[Assert\NotBlank]
    #[Assert\All([
        'constraints' => [
            new Assert\Type('numeric'),
            new Assert\GreaterThanOrEqual(0),
        ],
    ])]
    #[Assert\Collection(
        fields: [],
        allowExtraFields: true,
        allowMissingFields: false,
        extraFieldsMessage: 'Topic keys must be non-empty strings'
    )]
    private array $topics;

    public function __construct(array $data)
    {
        $this->topics = $data['topics'] ?? [];
    }

    public function getTopics(): array
    {
        return $this->topics;
    }

    public function toDTO(): CalculateQuotesRequestDTO
    {
        return CalculateQuotesRequestDTO::fromArray(['topics' => $this->topics]);
    }
}
