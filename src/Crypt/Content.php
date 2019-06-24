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

namespace XStream\Crypt;

/**
 * Class Content
 *
 * @package XStream\Crypt
 */
class Content {

    /**
     * @param string $value
     * @param int $offset
     * @param Key $key
     *
     * @return string
     */
    public static function encrypt( string $value, int $offset, Key $key ): string {

        if ( function_exists( 'xor_string' ) ) {

            xor_string( $value, $key->getKey(), $offset );
            return $value;

        }

        $key = $key->getKey();

        for ( $i = 0, $length = strlen( $value ); $i < $length; $i++ ) {

            $value[ $i ] = $value[ $i ] ^ $key[ ( $i + $offset ) % 64 ];

        }

        return $value;

    }

    /**
     * @param string $value
     * @param int $offset
     * @param Key $key
     *
     * @return string
     */
    public static function decrypt( string $value, int $offset, Key $key ): string {

        return static::encrypt( $value, $offset, $key );

    }

}
