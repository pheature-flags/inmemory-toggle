<?php

declare(strict_types=1);

namespace Pheature\InMemory\Toggle;

use Pheature\Core\Toggle\Read\Feature;
use Pheature\Core\Toggle\Read\FeatureFinder;

use function array_map;
use function array_values;

/**
 * @psalm-import-type InMemoryFeature from InMemoryConfig
 */
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

    /**
     * @return Feature[]
     */
    public function all(): array
    {
        /** @var callable $configCallback */
        $configCallback = function (array $feature): Feature {
            /** @var InMemoryFeature $feature */
            return $this->featureFactory->create($feature);
        };

        /** @var Feature[] $features */
        $features = array_values(array_map($configCallback, $this->config->all()));

        return $features;
    }
}
