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
 * Class InvalidMountPathException
 *
 * @package XStream\Wrapper\Exceptions
 */
class InvalidMountPathException extends Exception {

    /**
     * InvalidProtocolException constructor.
     *
     * @param string $path
     */
    public function __construct( string $path ) {

        parent::__construct( sprintf( 'Invalid mount path: %s.', $path ) );

    }

}