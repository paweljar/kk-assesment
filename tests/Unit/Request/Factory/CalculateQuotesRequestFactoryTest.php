<?php

declare(strict_types=1);

namespace Unit\Request\Factory;

use App\Request\CalculateQuotesRequest;
use App\Request\Factory\CalculateQuotesRequestFactory;
use PHPUnit\Framework\TestCase;

class CalculateQuotesRequestFactoryTest extends TestCase
{
    private CalculateQuotesRequestFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new CalculateQuotesRequestFactory();
    }

    public function testCreateClassInstance(): void
    {
        $calculateQuotesRequest = $this->factory->createFromArray([]);

        $this->assertInstanceOf(CalculateQuotesRequest::class, $calculateQuotesRequest);
    }
}
