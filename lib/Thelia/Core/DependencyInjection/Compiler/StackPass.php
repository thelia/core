<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Thelia\Core\DependencyInjection\Compiler;

use Stack\Builder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class StackPass
 * @package Thelia\Core\DependencyInjection\Compiler
 * @author manuel raynaud <manu@raynaud.io>
 */
class StackPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(Builder::class)) {
            return;
        }

        $stackFactory = $container->getDefinition(Builder::class);
        $stackPriority = [];

        foreach ($container->findTaggedServiceIds('stack_middleware') as $id => $attributes) {
            $priority = isset($attributes[0]['priority']) ? $attributes[0]['priority'] : 0;
            $stackPriority[$priority][] = $this->retrieveArguments($container, $id);
        }

        if (false === empty($stackPriority)) {
            $this->addMiddlewares($stackFactory, $stackPriority);
        }
    }

    protected function addMiddlewares(Definition $stackFactory, $stackMiddlewares)
    {
        krsort($stackMiddlewares);

        foreach ($stackMiddlewares as $priority => $stacks) {
            foreach ($stacks as $arguments) {
                $stackFactory->addMethodCall('push', $arguments);
            }
        }
    }

    protected function retrieveArguments(ContainerBuilder $container, $id)
    {
        $definition = $container->getDefinition($id);
        $arguments = $definition->getArguments();
        array_unshift($arguments, $definition->getClass());

        return $arguments;
    }
}
