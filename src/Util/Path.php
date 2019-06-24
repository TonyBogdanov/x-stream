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

namespace XStream\Util;

use Exception;

/**
 * Class Path
 *
 * @package XStream\Util
 */
class Path {

    /**
     * @param string $path
     *
     * @return string
     */
    public static function normalize( string $path ): string {

        return preg_replace(

            [ '#\\\#', '#/{2,}#', '#/(\./)+#', '#([^/\.]+/(?R)*\.{2,}/)#', '#\.\./#', '#/$#' ],
            [ '/', '/', '/', '', '', '' ],
            $path

        );

    }

    /**
     * @param string $protocol
     * @param string $path
     *
     * @return string
     * @throws Exception
     */
    public static function getRelative( string $protocol, string $path ): string {

        if ( preg_match( '/^(?P<protocol>[a-z0-9]+):[\\\\\/]{2}/i', $path, $match ) ) {

            if ( $match['protocol'] !== $protocol ) {

                throw new Exception( sprintf( 'Expected protocol: %s does not match the supplied path: %s.',
                                              $protocol, $path ) );

            }

            $path = substr( $path, strlen( $match[0] ) );

        }

        return static::normalize( $path );

    }

    /**
     * @param string $protocol
     * @param string $path
     *
     * @return string
     * @throws Exception
     */
    public static function getAbsolute( string $protocol, string $path ): string {

        return $protocol . '://' . static::getRelative( $protocol, $path );

    }

}
