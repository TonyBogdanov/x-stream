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
 * Class FileName
 *
 * @package XStream\Crypt
 */
class FileName {

    /**
     * @param string $value
     * @param Key $key
     *
     * @return string
     */
    public static function encrypt( string $value, Key $key ): string {

        $encrypted = openssl_encrypt( $value, 'AES-256-CTR', $key->getKey(), OPENSSL_RAW_DATA, $key->getVector() );
        return str_replace( [ '/', '=' ], [ '-', '_' ], base64_encode( $encrypted ) );

    }

    /**
     * @param string $value
     * @param Key $key
     *
     * @return string
     */
    public static function decrypt( string $value, Key $key ): string {

        $value = base64_decode( str_replace( [ '-', '_' ], [ '/', '=' ], $value ) );
        return openssl_decrypt( $value, 'AES-256-CTR', $key->getKey(), OPENSSL_RAW_DATA, $key->getVector() );

    }

    /**
     * @param string $value
     * @param Key $key
     *
     * @return string
     */
    public static function encryptPath( string $value, Key $key ): string {

        $trail = explode( '/', $value );

        foreach ( $trail as &$chunk ) {

            $chunk = static::encrypt( $chunk, $key );

        }

        return implode( '/', $trail );

    }

    /**
     * @param string $value
     * @param Key $key
     *
     * @return string
     */
    public static function decryptPath( string $value, Key $key ): string {

        $trail = explode( '/', $value );

        foreach ( $trail as &$chunk ) {

            $chunk = static::decrypt( $chunk, $key );

        }

        return implode( '/', $trail );

    }

}
