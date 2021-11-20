<?php

declare(strict_types=1);

namespace Pheature\InMemory\Toggle;

use Webmozart\Assert\Assert;

/**
 * @psalm-import-type WriteStrategy from \Pheature\Core\Toggle\Write\Strategy
 * @psalm-type InMemoryFeature array{id: string, enabled: bool, strategies?: WriteStrategy[]}
 */
final class InMemoryConfig
{
    /** @var array<string, InMemoryFeature> */
    private array $config = [];

    /** @param InMemoryFeature[] $config */
    public function __construct(array $config = [])
    {
        $this->assertConfig($config);
        foreach ($config as $feature) {
            $this->config[$feature['id']] = $feature;
        }
    }

    /** @return InMemoryFeature */
    public function get(string $featureId): array
    {
        return $this->config[$featureId];
    }

    public function has(string $featureId): bool
    {
        return array_key_exists($featureId, $this->config);
    }

    /**
     * @psalm-suppress RedundantConditionGivenDocblockType
     * @psalm-suppress RedundantCondition
     * @param InMemoryFeature[] $config
     */
    private function assertConfig(array $config): void
    {
        foreach ($config as $toggleConfig) {
            Assert::isArray($toggleConfig);
            Assert::keyExists($toggleConfig, 'id');
            Assert::string($toggleConfig['id']);
            Assert::keyExists($toggleConfig, 'enabled');
            Assert::boolean($toggleConfig['enabled']);


            if (false === array_key_exists('strategies', $toggleConfig)) {
                continue;
            }

            Assert::IsArray($toggleConfig['strategies']);
            foreach ($toggleConfig['strategies'] as $strategy) {
                Assert::keyExists($strategy, 'strategy_id');
                Assert::string($strategy['strategy_id']);
                Assert::keyExists($strategy, 'strategy_type');
                Assert::string($strategy['strategy_type']);
                Assert::IsArray($strategy['segments']);
                foreach ($strategy['segments'] as $segment) {
                    Assert::keyExists($segment, 'segment_id');
                    Assert::string($segment['segment_id']);
                    Assert::keyExists($segment, 'segment_type');
                    Assert::string($segment['segment_type']);
                }
            }
        }
    }

    /** @return array<string, InMemoryFeature> */
    public function all(): array
    {
        return $this->config;
    }
}
