<?php

namespace Thelia\Api\Bridge\Propel\Extension;

use ApiPlatform\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Operation;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Psr\Container\ContainerInterface;
use Thelia\Api\Bridge\Propel\Filter\FilterInterface;
use Thelia\Api\Bridge\Propel\Filter\OrderFilter;

class FilterExtension implements QueryCollectionExtensionInterface
{
    public function __construct(private readonly ContainerInterface $filterLocator)
    {
    }

    public function applyToCollection(ModelCriteria $query, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if (null === $resourceClass) {
            throw new InvalidArgumentException('The "$resourceClass" parameter must not be null');
        }

        $resourceFilters = $operation?->getFilters();

        if (empty($resourceFilters)) {
            return;
        }

        $orderFilters = [];

        foreach ($resourceFilters as $filterId) {
            $filter = $this->filterLocator->has($filterId) ? $this->filterLocator->get($filterId) : null;
            if ($filter instanceof FilterInterface) {
                // Apply the OrderFilter after every other filter to avoid an edge case where OrderFilter would do a LEFT JOIN instead of an INNER JOIN
                if ($filter instanceof OrderFilter) {
                    $orderFilters[] = $filter;
                    continue;
                }

                $context['filters'] ??= [];
                $filter->apply($query, $resourceClass, $operation, $context);
            }
        }

        foreach ($orderFilters as $orderFilter) {
            $context['filters'] ??= [];
            $orderFilter->apply($query, $resourceClass, $operation, $context);
        }
    }
}
