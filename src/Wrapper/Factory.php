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

namespace XStream\Wrapper;

use XStream\Crypt\Key;
use XStream\Util\Path;
use XStream\Wrapper\Exceptions\FailedToRegisterProtocolException;
use XStream\Wrapper\Exceptions\InvalidContextException;
use XStream\Wrapper\Exceptions\InvalidMountPathException;
use XStream\Wrapper\Exceptions\InvalidProtocolException;
use XStream\Wrapper\Exceptions\ProtocolExistsException;

/**
 * Class Factory
 *
 * @package XStream\Wrapper
 */
class Factory {

    /**
     * @var Context[]
     */
    protected static $contexts = [];

    /**
     * @param string $protocol
     *
     * @return Context
     * @throws InvalidContextException
     *
     * @internal
     */
    public static function getContext( string $protocol ): Context {

        if ( ! isset( static::$contexts[ $protocol ] ) ) {

            throw new InvalidContextException( $protocol );

        }

        return static::$contexts[ $protocol ];

    }

    /**
     * @param string $protocol
     * @param string $path
     * @param string $password
     * @param string $wrapperClass
     *
     * @throws FailedToRegisterProtocolException
     * @throws InvalidMountPathException
     * @throws InvalidProtocolException
     * @throws ProtocolExistsException
     */
    public static function register(

        string $protocol,
        string $path,
        string $password,
        string $wrapperClass = Wrapper::class

    ) {

        if ( isset( static::$contexts[ $protocol ] ) || in_array( $protocol, stream_get_wrappers(), true ) ) {

            throw new ProtocolExistsException( $protocol );

        }

        if ( ! preg_match( '/^[a-z0-9]+$/i', $protocol ) ) {

            throw new InvalidProtocolException( $protocol );

        }

        if ( ! is_dir( $path ) ) {

            throw new InvalidMountPathException( $path );

        }

        static::$contexts[ $protocol ] = new Context( $protocol, Path::normalize( $path ), Key::create( $password ) );

        if ( ! stream_wrapper_register( $protocol, $wrapperClass ) ) {

            throw new FailedToRegisterProtocolException( $protocol );

        }

    }

}
