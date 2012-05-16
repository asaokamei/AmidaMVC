<?php
namespace AmidaMVC\Tools;


class LoadArray extends Load
{
// still static!!!
    static $fileSys = array();

    static function setFiles( $files )
    {
        static::$fileSys = $files;
    }
    /**
     * @static
     * @param $file_name
     * @return bool
     */
    static function getContentsByGet( $file_name )
    {
        return ( isset( static::$fileSys[ $file_name ] ) ) ? static::$fileSys[ $file_name ] : FALSE;
    }

    static function getContentsByBuffer( $file_name )
    {
        return static::getContentsByGet( $file_name );
    }

    function findFile( $file_name )
    {
        $found = FALSE;
        if ( empty( static::$_loadFolder ) ) return $found;
        foreach ( static::$_loadFolder as $folder ) {
            $check_file = $folder . '/' . $file_name;
            if ( isset( static::$fileSys[ $check_file ] ) ) {
                $found = $check_file;
                break;
            }
        }
        return $found;
    }

    function putContents( $filename, $content )
    {
        static::$fileSys[ $filename ] = $content;
        return TRUE;
    }

    function exists( $filename )
    {
        return isset( static::$fileSys[ $filename ] );
    }

    function mkDir( $dir, $permission )
    {
        static::$fileSys[ $dir ] = NULL;
        return TRUE;
    }

    function rename( $file1, $file2 )
    {
        static::$fileSys[ $file2 ] = static::$fileSys[ $file1 ];
        unset( static::$fileSys[ $file1 ] );
        return TRUE;
    }

    function isDir( $dir )
    {
        if ( array_key_exists( $dir, static::$fileSys ) && !isset( static::$fileSys[ $dir ] ) ) {
            return TRUE;
        }
        return FALSE;
    }

    function glob( $pattern, $flag = NULL )
    {
        $found = array();
        if ( strpos( $pattern, '{' ) !== FALSE ) {
            $pattern = substr( $pattern, 0, strpos( $pattern, '{' ) );
            $pattern .= substr( $pattern, strpos( $pattern, '{' ) + 1, strpos( $pattern, ',' ) );
        }
        foreach ( static::$fileSys as $file => $content ) {
            if ( substr( $file, 0, strlen( $pattern ) ) == $pattern ) {
                $found[ ] = $file;
            }
        }
        return $found;
    }

    function unlink( $filename )
    {
        if ( array_key_exists( $filename, static::$fileSys ) ) {
            unset( static::$fileSys[ $filename ] );
            return TRUE;
        }
        return FALSE;
    }
}

