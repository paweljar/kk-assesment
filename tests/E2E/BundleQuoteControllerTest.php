<?php

declare(strict_types=1);

namespace E2E;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BundleQuoteControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    /**
     * @dataProvider exampleProvider
     */
    public function testExampleFromTask(array $topics, array $quotes): void
    {
        $this->client->jsonRequest(
            'POST',
            '/api/bundle-quotes',
            [
                'topics' => $topics,
            ]
        );

        $this->assertResponseIsSuccessful();
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertCount(1, $responseData);
        $this->assertEquals([
            'quotes' => $quotes,
        ], $responseData);
    }

    public function testEmptyQuotesInRequest(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/api/bundle-quotes',
            [],
        );

        $this->assertResponseStatusCodeSame(400);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('error', $responseData);
        $this->assertEquals('topics: This value should not be blank.', $responseData['error']);
    }

    public static function exampleProvider(): array
    {
        return [
            'exampleFromTask' => [
                [
                    'reading' => 20,
                    'math' => 50,
                    'science' => 30,
                    'history' => 15,
                    'art' => 10,
                ],
                [
                    'provider_a' => 8,
                    'provider_b' => 5,
                    'provider_c' => 10,
                ],
            ],
            'example1' => [
                [
                    'history' => 15,
                    'art' => 20,
                    'science' => 10,
                ],
                [
                    'provider_a' => 3,
                    'provider_b' => 3,
                    'provider_c' => 3.75,
                ],
            ],
            'example2' => [
                [
                    'art' => 15,
                    'science' => 30,
                ],
                [
                    'provider_a' => 6,
                    'provider_b' => 6,
                ],
            ],
        ];
    }
}
