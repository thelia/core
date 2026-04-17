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

/**
 * Minimal contract consumed by front-office bundles (eg. Flexy) that need to
 * check whether the current request is authenticated as a customer.
 *
 * A no-op default implementation is registered in the core so the container
 * can build even when no front-office security provider module is active.
 */
interface FrontSecurityServiceInterface
{
    public function isAuthenticatedFront(): bool;
}
