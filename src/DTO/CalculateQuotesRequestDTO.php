<?php

declare(strict_types=1);

namespace App\DTO;

readonly class CalculateQuotesRequestDTO
{
    private array $topics;

    private function __construct(array $topics)
    {
        $this->topics = $topics;
    }

    public static function fromArray(array $data): self
    {
        return new self($data['topics']);
    }

    public function getTopics(): array
    {
        return $this->topics;
    }

    public function getTop3Topics(): array
    {
        $topics = $this->topics;
        arsort($topics);

        return array_slice($topics, 0, 3, true);
    }
}
