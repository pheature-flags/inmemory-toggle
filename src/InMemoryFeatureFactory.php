<?php

declare(strict_types=1);

namespace Pheature\InMemory\Toggle;

use Pheature\Core\Toggle\Read\ChainToggleStrategyFactory;
use Pheature\Core\Toggle\Read\Feature as IFeature;
use Pheature\Core\Toggle\Read\ToggleStrategies;
use Pheature\Model\Toggle\Feature;

/**
 * @psalm-import-type InMemoryFeature from InMemoryConfig
 */
final class InMemoryFeatureFactory
{
    private ChainToggleStrategyFactory $toggleStrategyFactory;

    public function __construct(ChainToggleStrategyFactory $toggleStrategyFactory)
    {
        $this->toggleStrategyFactory = $toggleStrategyFactory;
    }

    /**
     * @param InMemoryFeature $data
     */
    public function create(array $data): IFeature
    {
        return new Feature(
            $data['id'],
            new ToggleStrategies(...array_map(
                fn(array $strategy) => $this->toggleStrategyFactory->createFromArray($strategy),
                $data['strategies'] ?? []
            )),
            $data['enabled']
        );
    }
}
