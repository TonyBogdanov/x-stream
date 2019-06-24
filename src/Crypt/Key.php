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

use Exception;

/**
 * Class Key
 *
 * @package XStream\Crypt
 */
class Key {

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $vector;

    /**
     * @param string $password
     *
     * @return Key
     * @throws Exception
     */
    public static function create( string $password ): Key {

        for ( $i = 0, $it = $iterations = crc32( $password ) % 256; $i <= $it; $i++ ) {

            $password = hash( 'sha512', $password, true );

        }

        return new static( $password );

    }

    /**
     * Key constructor.
     *
     * @param string $key
     *
     * @throws Exception
     */
    public function __construct( string $key ) {

        if ( 64 !== ( $length = strlen( $key ) ) ) {

            throw new Exception( sprintf( 'Invalid key length, expected 64 bytes, got: %d.', $length ) );

        }
        $this->key = $key;

    }

    /**
     * @return string
     */
    public function getKey(): string {

        return $this->key;

    }

    /**
     * @param string $key
     *
     * @return Key
     */
    public function setKey( string $key ): Key {

        $this->key = $key;
        $this->vector = null;

        return $this;

    }

    /**
     * @return string
     */
    public function getVector(): string {

        if ( ! isset( $this->vector ) ) {

            $this->vector = hash( 'md5', $this->key, true );

        }

        return $this->vector;

    }

}
