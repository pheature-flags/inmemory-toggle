<?php

declare(strict_types=1);

namespace Pheature\InMemory\Toggle;

use Pheature\Core\Toggle\Read\Feature;
use Pheature\Core\Toggle\Read\FeatureFinder;

use function array_map;
use function array_values;

final class InMemoryFeatureFinder implements FeatureFinder
{
    private InMemoryConfig $config;
    private InMemoryFeatureFactory $featureFactory;

    public function __construct(InMemoryConfig $config, InMemoryFeatureFactory $featureFactory)
    {
        $this->config = $config;
        $this->featureFactory = $featureFactory;
    }

    public function get(string $featureId): Feature
    {
        if (false === $this->config->has($featureId)) {
            throw InMemoryFeatureNotFound::withId($featureId);
        }

        return $this->featureFactory->create($this->config->get($featureId));
    }

    public function all(): array
    {
        return array_values(
            array_map(
                /** @param array<string, string|bool|array<string, mixed>> $feature */
                fn(array $feature): Feature => $this->featureFactory->create($feature),
                $this->config->all()
            )
        );
    }
}
