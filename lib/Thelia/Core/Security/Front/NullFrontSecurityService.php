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

namespace Thelia\Core\Security\Front;

use Symfony\Component\DependencyInjection\Attribute\AsAlias;

/**
 * Default no-op implementation used when no front-office security provider
 * (eg. TwigEngine) is installed or active. Every request is treated as anonymous.
 *
 * Modules that activate a concrete provider override this alias by carrying
 * their own `#[AsAlias(FrontSecurityServiceInterface::class)]` — because
 * modules are loaded after the core, their alias wins when active.
 */
#[AsAlias(id: FrontSecurityServiceInterface::class)]
final class NullFrontSecurityService implements FrontSecurityServiceInterface
{
    public function isAuthenticatedFront(): bool
    {
        return false;
    }
}
