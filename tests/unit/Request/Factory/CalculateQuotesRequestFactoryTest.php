<?php

declare(strict_types=1);

namespace unit\Request\Factory;

use App\Request\Factory\CalculateQuotesRequestFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CalculateQuotesRequestFactoryTest extends TestCase
{
    private CalculateQuotesRequestFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new CalculateQuotesRequestFactory();
    }

    public function testCreateFromValidArray(): void
    {
        $data = [
            'topics' => [
                'math' => 50,
                'science' => 30,
            ],
        ];

        $request = $this->factory->createFromArray($data);

        $this->assertEquals($data['topics'], $request->getTopics());
    }

    public function testThrowsExceptionOnMissingTopics(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->factory->createFromArray([]);
    }

    public function testThrowsExceptionOnInvalidTopicKey(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->factory->createFromArray([
            'topics' => [
                '' => 50,  // empty topic key
            ],
        ]);
    }
}
