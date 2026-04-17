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
 * Minimal contract consumed by front-office bundles (eg. Flexy) that need to
 * render JSON content blocks. Implementations encapsulate both the query and
 * the template rendering so consumers stay decoupled from any concrete
 * storage layer.
 *
 * A no-op default implementation is registered in the core so the container
 * can build even when no block renderer module (eg. TheliaBlocks) is active.
 */
interface BlockRendererInterface
{
    /**
     * Look up block groups matching the given filters and render each to HTML.
     *
     * Supported filter keys: id, slug, item_type, item_id, visible, locale.
     * Implementations MUST ignore unknown keys.
     *
     * @param array<string, mixed> $filters
     *
     * @return array<int, string> HTML fragments in render order, empty when no match or no renderer is registered
     */
    public function findAndRenderBlocks(array $filters): array;
}
