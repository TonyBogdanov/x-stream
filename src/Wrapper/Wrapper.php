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

use Exception;
use RuntimeException;
use XStream\Crypt\Content;
use XStream\Crypt\FileName;
use XStream\Util\Path;
use XStream\Wrapper\Exceptions\CannotGuessContextException;
use XStream\Wrapper\Exceptions\InvalidContextException;

/**
 * Class Wrapper
 *
 * @package XStream\Wrapper
 */
class Wrapper {

    /**
     * @var Context
     */
    protected $wrapperContext;

    /**
     * @var resource
     */
    protected $directoryHandle;

    /**
     * @var string
     */
    protected $directoryPath;

    /**
     * @var resource
     */
    protected $fileHandle;

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @param string|null $hint
     *
     * @return Context
     * @throws CannotGuessContextException
     * @throws InvalidContextException
     */
    protected function getWrapperContext( string $hint = null ): Context {

        if ( ! isset( $this->wrapperContext ) ) {

            if ( ! isset( $hint ) || ! preg_match( '/^(?P<protocol>[a-z0-9]+):[\\\\\/]{2}/', $hint, $match ) ) {

                throw new CannotGuessContextException();

            }

            $this->wrapperContext = Factory::getContext( $match['protocol'] );

        }

        return $this->wrapperContext;

    }

    /**
     * @param string $path
     *
     * @return string
     * @throws Exception
     */
    protected function getPhysicalPath( string $path ): string {

        if ( in_array( $path, [ '.', '..' ] ) ) {

            return $path;

        }

        $context = $this->getWrapperContext( $path );

        $physical = Path::getRelative( $context->getProtocol(), $path );
        $physical = FileName::encryptPath( $physical, $context->getKey() );

        return $context->getPath() . '/' . $physical;

    }

    /**
     * @param string $path
     *
     * @return string
     * @throws CannotGuessContextException
     * @throws InvalidContextException
     */
    protected function getVirtualPath( string $path ): string {

        if ( in_array( $path, [ '.', '..' ] ) ) {

            return $path;

        }

        // Assumes context was already determined at least once before.
        return FileName::decryptPath( $path, $this->getWrapperContext()->getKey() );

    }

    /**
     * @return bool
     */
    public function dir_closedir(): bool {

        if ( ! $this->directoryHandle ) {

            return false;

        }

        closedir( $this->directoryHandle );

        unset( $this->directoryHandle );
        unset( $this->directoryPath );

        return true;

    }

    /**
     * @param string $path
     *
     * @return bool
     * @throws Exception
     */
    public function dir_opendir( string $path ): bool {

        $this->directoryHandle = opendir( $this->getPhysicalPath( $path ) );
        if ( ! $this->directoryHandle ) {

            return false;

        }

        $this->directoryPath = $path;
        return true;

    }

    /**
     * @return bool|string
     * @throws CannotGuessContextException
     * @throws InvalidContextException
     */
    public function dir_readdir() {

        if ( ! $this->directoryHandle ) {

            return false;

        }

        $read = readdir( $this->directoryHandle );
        if ( ! $read ) {

            return false;

        }

        return $this->getVirtualPath( $read );

    }

    public function dir_rewinddir() {

        if ( ! $this->directoryHandle ) {

            return;

        }

        rewinddir( $this->directoryHandle );

    }

    /**
     * @param string $path
     * @param int $mode
     * @param int $options
     *
     * @return bool
     * @throws Exception
     */
    public function mkdir( string $path , int $mode , int $options ): bool {

        return mkdir( $this->getPhysicalPath( $path ), $mode, $options );

    }

    /**
     * @param string $path_from
     * @param string $path_to
     *
     * @return bool
     * @throws Exception
     */
    public function rename( string $path_from , string $path_to ): bool {

        return rename(

            $this->getPhysicalPath( $path_from ),
            $this->getPhysicalPath( $path_to )

        );

    }

    /**
     * @param string $path
     *
     * @return bool
     * @throws Exception
     */
    public function rmdir( string $path ): bool {

        return rmdir( $this->getPhysicalPath( $path ) );

    }

    /**
     * @param int $cast_as
     *
     * @return bool|resource
     */
    public function stream_cast( int $cast_as ) {

        if ( $this->fileHandle ) {

            return $this->fileHandle;

        }

        if ( $this->directoryHandle ) {

            return $this->directoryHandle;

        }

        return false;

    }

    /**
     * @return bool
     */
    public function stream_close(): bool {

        if ( ! $this->fileHandle ) {

            return false;

        }

        return fclose( $this->fileHandle );

    }

    /**
     * @return bool
     */
    public function stream_eof(): bool {

        if ( ! $this->fileHandle ) {

            return false;

        }

        return feof( $this->fileHandle );

    }

    /**
     * @return bool
     */
    public function stream_flush(): bool {

        if ( ! $this->fileHandle ) {

            return false;

        }

        return fflush( $this->fileHandle );

    }

    /**
     * @param int $operation
     *
     * @return bool
     */
    public function stream_lock( int $operation ): bool {

        trigger_error( 'Locking is not implemented.', E_WARNING );
        return false;

    }

    /**
     * @param string $path
     * @param string $mode
     *
     * @return bool
     * @throws Exception
     */
    public function stream_open( string $path , string $mode ): bool {

        $this->fileHandle = fopen( $this->getPhysicalPath( $path ), $mode );
        if ( ! $this->fileHandle ) {

            return false;

        }

        $this->filePath = $path;
        return true;

    }

    /**
     * @param int $count
     *
     * @return bool|string
     * @throws CannotGuessContextException
     * @throws InvalidContextException
     */
    public function stream_read( int $count ) {

        if ( ! $this->fileHandle ) {

            return false;

        }

        $offset = ftell( $this->fileHandle );

        return Content::decrypt(

            fread( $this->fileHandle, $count ),
            $offset,
            $this->getWrapperContext( $this->filePath )->getKey()

        );

    }

    /**
     * @param int $offset
     * @param int $whence
     *
     * @return bool
     */
    public function stream_seek( int $offset , int $whence = SEEK_SET ): bool {

        if ( ! $this->fileHandle ) {

            return false;

        }

        return fseek( $this->fileHandle, $offset, $whence );

    }

    /**
     * @param int $option
     * @param int $arg1
     * @param int $arg2
     *
     * @return bool
     */
    public function stream_set_option( int $option , int $arg1 , int $arg2 ): bool {

        return false;

    }

    /**
     * @return array|bool
     */
    public function stream_stat() {

        if ( ! $this->fileHandle ) {

            return false;

        }

        return fstat( $this->fileHandle );

    }

    /**
     * @return bool|int
     */
    public function stream_tell() {

        if ( ! $this->fileHandle ) {

            return false;

        }

        return ftell( $this->fileHandle );

    }

    /**
     * @param string $data
     *
     * @return int
     * @throws CannotGuessContextException
     * @throws InvalidContextException
     */
    public function stream_write( string $data ): int {

        if ( ! $this->fileHandle ) {

            return 0;

        }

        return fwrite(

            $this->fileHandle,
            Content::encrypt(

                $data,
                ftell( $this->fileHandle ),
                $this->getWrapperContext( $this->filePath )->getKey()

            )

        );

    }

    /**
     * @param string $path
     *
     * @return bool
     * @throws Exception
     */
    public function unlink( string $path ): bool {

        return unlink( $this->getPhysicalPath( $path ) );

    }

    /**
     * @param string $path
     *
     * @return string
     * @throws Exception
     */
    public function url_stat( string $path ) {

        return @stat( $this->getPhysicalPath( $path ) );

    }

}
