<?php

declare(strict_types=1);

namespace App\DTO;

readonly class CalculateQuotesRequestDTO
{
    /**
     * @param array<string, int> $topics Topic scores indexed by topic name
     */
    private function __construct(
        private array $topics,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * @return array<string, int>
     */
    public function getTopics(): array
    {
        return $this->topics;
    }

    /**
     * @return array<string, int>
     */
    public function getTop3Topics(): array
    {
        $topics = $this->topics;
        arsort($topics);

        return array_slice($topics, 0, 3, true);
    }
}
