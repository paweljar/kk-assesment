<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

readonly class ProviderConfigurationService
{
    private array $providerTopics;

    public function __construct(ParameterBagInterface $params)
    {
        $path = $params->get('app.provider_topics_json_path');
        $configFile = json_decode(file_get_contents($path), true);
        $this->providerTopics = $configFile['provider_topics'];
    }

    public function getProviderTopics(string $provider): array
    {
        if (!isset($this->providerTopics[$provider])) {
            throw new \InvalidArgumentException(sprintf('Provider "%s" not found', $provider));
        }

        return explode('+', $this->providerTopics[$provider]);
    }

    public function getAllProviders(): array
    {
        return array_keys($this->providerTopics);
    }
}
