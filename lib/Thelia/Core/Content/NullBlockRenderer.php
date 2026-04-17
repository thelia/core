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

namespace Thelia\Core\Content;

/**
 * Default no-op implementation used when no block renderer module
 * (eg. TheliaBlocks) is installed or active. Returns an empty list so
 * templates can safely iterate.
 */
final class NullBlockRenderer implements BlockRendererInterface
{
    public function findAndRenderBlocks(array $filters): array
    {
        return [];
    }
}
