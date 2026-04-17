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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Thelia\Core\Content\BlockRendererInterface;
use Thelia\Core\Content\NullBlockRenderer;
use Thelia\Core\Security\Front\FrontSecurityServiceInterface;
use Thelia\Core\Security\Front\NullFrontSecurityService;

/*
 * Register Null Object fallbacks for front-office contracts that Flexy-based
 * bundles rely on. Modules such as TwigEngine and TheliaBlocks override these
 * aliases when active; when absent, the kernel still boots because autowire
 * resolves to the no-op implementations. The concrete classes are loaded by
 * the `Thelia\\` PSR-4 autowire in services.php, so only the aliases are
 * needed here.
 */
return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->alias(FrontSecurityServiceInterface::class, NullFrontSecurityService::class);

    $services->alias(BlockRendererInterface::class, NullBlockRenderer::class);
};
