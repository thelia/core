<?php

declare(strict_types=1);

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thelia\Core\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Thelia\Core\Template\Element\BaseLoop;

class LoopCompilerPass implements CompilerPassInterface
{
    private const TheliaLoopServiceId = 'TheliaSmarty\\Template\\Plugins\\TheliaLoop';

    public function process(ContainerBuilder $container): void
    {
        try {
            $xmlLoopConfig = $container->getParameter('Thelia.parser.loops');
        } catch (ParameterNotFoundException) {
            $xmlLoopConfig = [];
        }

        $loopConfig = $xmlLoopConfig;
        $taggedServices = $container->findTaggedServiceIds('thelia.loop');
        $taggedClasses = [];

        foreach (array_keys($taggedServices) as $serviceId) {
            $definition = $container->getDefinition($serviceId);
            $className = $definition->getClass();

            if ($className && is_subclass_of($className, BaseLoop::class)) {
                $loopName = $this->getLoopNameFromClass($className);

                $loopConfig[$loopName] = $className;
                $taggedClasses[$className] = true;
            }
        }

        $container->setParameter('Thelia.parser.loops', $loopConfig);

        $this->registerXmlAliases($container, $xmlLoopConfig, $taggedClasses);
    }

    /**
     * TheliaSmarty's TheliaLoop plugin derives loop type names from the class FQCN only,
     * ignoring <loop name="..." class="..." /> declarations from module config.xml files.
     * To keep those aliases working at template level (e.g. {loop type="restocking-alert"}
     * backed by StockAlert\Loop\RestockingAlertLoop), schedule a registerLoop() call on
     * the TheliaLoop service for each alias that does not collide with the auto-derived
     * kebab-cased class name.
     *
     * @param array<string, class-string> $xmlAliases
     * @param array<class-string, true>   $taggedClasses
     */
    private function registerXmlAliases(ContainerBuilder $container, array $xmlAliases, array $taggedClasses): void
    {
        if (!$container->hasDefinition(self::TheliaLoopServiceId)) {
            return;
        }

        $theliaLoopDefinition = $container->getDefinition(self::TheliaLoopServiceId);
        $seen = [];

        foreach ($xmlAliases as $alias => $className) {
            if (!\is_string($alias) || !\is_string($className)) {
                continue;
            }

            if (!isset($taggedClasses[$className])) {
                continue;
            }

            if ($alias === $this->getKebabLoopName($className)) {
                continue;
            }

            if (isset($seen[$alias])) {
                continue;
            }

            $theliaLoopDefinition->addMethodCall('registerLoop', [$className, $alias]);
            $seen[$alias] = true;
        }
    }

    private function getLoopNameFromClass(string $className): string
    {
        $parts = explode('\\', $className);
        $shortClassName = end($parts);

        return strtolower((string) preg_replace('/([a-z])([A-Z])/', '$1_$2', $shortClassName));
    }

    private function getKebabLoopName(string $className): string
    {
        return str_replace('_', '-', $this->getLoopNameFromClass($className));
    }
}
