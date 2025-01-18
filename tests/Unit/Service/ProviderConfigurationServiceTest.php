<?php

declare(strict_types=1);

namespace Unit\Service;

use App\Service\ProviderConfigurationService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ProviderConfigurationServiceTest extends TestCase
{
    private ParameterBagInterface $parameterBag;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $configPath = __DIR__.'/../../fixtures/provider_topics.json';
        $this->configData = json_decode(file_get_contents($configPath), true);

        $this->parameterBag = $this->createMock(ParameterBagInterface::class);
        $this->parameterBag->method('get')
            ->with('app.provider_topics_json_path')
            ->willReturn($configPath);
    }

    public function testGetProviderTopics(): void
    {
        $service = new ProviderConfigurationService($this->parameterBag);

        $expectedTopics1 = ['topicA', 'topicB', 'topicC'];
        $this->assertEquals($expectedTopics1, $service->getProviderTopics('provider1'));

        $expectedTopics2 = ['topicA', 'topicB'];
        $this->assertEquals($expectedTopics2, $service->getProviderTopics('provider2'));
    }

    public function testGetAllProviders(): void
    {
        $service = new ProviderConfigurationService($this->parameterBag);

        $expectedProviders = ['provider1', 'provider2'];
        $this->assertEquals($expectedProviders, $service->getAllProviders());
    }

    public function testGetProviderTopicsThrowsExceptionForInvalidProvider(): void
    {
        $service = new ProviderConfigurationService($this->parameterBag);

        $this->expectException(\InvalidArgumentException::class);
        $service->getProviderTopics('non_existent_provider');
    }
}
