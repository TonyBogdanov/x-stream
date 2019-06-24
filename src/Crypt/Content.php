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

        xor_string( $value, $key->getKey(), $offset );
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
