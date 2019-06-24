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
 * Class CannotGuessContextException
 *
 * @package XStream\Wrapper\Exceptions
 */
class CannotGuessContextException extends Exception {

    /**
     * CannotGuessContextException constructor.
     */
    public function __construct() {

        parent::__construct( 'Could not determine wrapper context.' );

    }

}