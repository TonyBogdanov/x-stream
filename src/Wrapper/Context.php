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

/**
 * Class Context
 *
 * @package XStream\Wrapper
 */
class Context {

    /**
     * @var string
     */
    protected $protocol;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var Key
     */
    protected $key;

    /**
     * Context constructor.
     *
     * @param string $protocol
     * @param string $path
     * @param Key $key
     */
    public function __construct( string $protocol, string $path, Key $key ) {

        $this
            ->setProtocol( $protocol )
            ->setPath( $path )
            ->setKey( $key );

    }

    /**
     * @return string
     */
    public function getProtocol(): string {

        return $this->protocol;

    }

    /**
     * @param string $protocol
     *
     * @return Context
     */
    public function setProtocol( string $protocol ): Context {

        $this->protocol = $protocol;
        return $this;

    }

    /**
     * @return string
     */
    public function getPath(): string {

        return $this->path;

    }

    /**
     * @param string $path
     *
     * @return Context
     */
    public function setPath( string $path ): Context {

        $this->path = $path;
        return $this;

    }

    /**
     * @return Key
     */
    public function getKey(): Key {

        return $this->key;

    }

    /**
     * @param Key $key
     *
     * @return Context
     */
    public function setKey( Key $key ): Context {

        $this->key = $key;
        return $this;

    }

}
