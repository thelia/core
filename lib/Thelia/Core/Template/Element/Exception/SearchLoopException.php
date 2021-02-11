<?php

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thelia\Core\Template\Element\Exception;

class SearchLoopException extends \RuntimeException
{
    const UNKNOWN_EXCEPTION = 0;

    public function __construct($message, $code = null, $arguments = [], $previous = null)
    {
        if (\is_array($arguments)) {
            $this->arguments = $arguments;
        }
        if ($code === null) {
            $code = self::UNKNOWN_EXCEPTION;
        }
        parent::__construct($message, $code, $previous);
    }
}
