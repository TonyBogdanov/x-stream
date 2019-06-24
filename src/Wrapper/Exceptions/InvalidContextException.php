<?php

/**
 * This file is part of the X-Stream package.
 *
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace XStream\Wrapper\Exceptions;

use Exception;

/**
 * Class InvalidContextException
 *
 * @package XStream\Wrapper\Exceptions
 */
class InvalidContextException extends Exception {

    /**
     * InvalidContextException constructor.
     *
     * @param string $path
     */
    public function __construct( string $path ) {

        parent::__construct( sprintf( 'Invalid or missing context for protocol: %s.', $path ) );

    }

}