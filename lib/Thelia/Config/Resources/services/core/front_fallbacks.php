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
 * bundles rely on. Explicit aliases are declared here (not via #[AsAlias])
 * so that active modules such as TwigEngine and TheliaBlocks can freely
 * override them through the attribute — Symfony refuses two concurrent
 * #[AsAlias] declarations for the same id.
 */
return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->alias(FrontSecurityServiceInterface::class, NullFrontSecurityService::class);

    $services->alias(BlockRendererInterface::class, NullBlockRenderer::class);
};
