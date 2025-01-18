<?php

declare(strict_types=1);

namespace Unit\DTO;

use App\DTO\CalculateQuotesRequestDTO;
use PHPUnit\Framework\TestCase;

class CalculateQuotesRequestDTOTest extends TestCase
{
    public function testCreateClassInstance(): void
    {
        $instance = CalculateQuotesRequestDTO::fromArray([]);

        $this->assertInstanceOf(CalculateQuotesRequestDTO::class, $instance);
    }

    public function testCreateWithProvidedTopics(): void
    {
        $instance = CalculateQuotesRequestDTO::fromArray([
            'reading' => 20,
            'math' => 50,
            'science' => 30,
            'history' => 15,
            'art' => 10,
        ]);

        $this->assertEquals([
            'reading' => 20,
            'math' => 50,
            'science' => 30,
            'history' => 15,
            'art' => 10,
        ], $instance->getTopics());
    }

    public function testGetTop3Topics(): void
    {
        $instance = CalculateQuotesRequestDTO::fromArray([
            'reading' => 20,
            'math' => 50,
            'science' => 30,
            'history' => 15,
            'art' => 10,
        ]);

        $this->assertEquals([
            'math' => 50,
            'science' => 30,
            'reading' => 20,
        ], $instance->getTop3Topics());
    }
}
