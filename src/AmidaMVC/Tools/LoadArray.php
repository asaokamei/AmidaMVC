<?php
namespace AmidaMVC\Tools;


class LoadArray extends Load
{
    var $fileSys = array();

    function setFiles( $files )
    {
        $this->fileSys = $files;
    }
    /**
     * @static
     * @param $file_name
     * @return bool
     */
    function getContentsByGet( $file_name )
    {
        return ( isset( $this->fileSys[ $file_name ] ) ) ? $this->fileSys[ $file_name ] : FALSE;
    }

    function getContentsByBuffer( $file_name )
    {
        return static::getContentsByGet( $file_name );
    }

    function findFile( $file_name )
    {
        $found = FALSE;
        if ( empty( $this->_loadFolder ) ) return $found;
        foreach ( $this->_loadFolder as $folder ) {
            $check_file = $folder . '/' . $file_name;
            if ( isset( $this->fileSys[ $check_file ] ) ) {
                $found = $check_file;
                break;
            }
        }
        return $found;
    }

    function putContents( $filename, $content )
    {
        $this->fileSys[ $filename ] = $content;
        return TRUE;
    }

    function exists( $filename )
    {
        return array_key_exists(  $filename, $this->fileSys );
    }

    function mkDir( $dir, $permission )
    {
        $this->fileSys[ $dir ] = NULL;
        return TRUE;
    }

    function rename( $file1, $file2 )
    {
        $this->fileSys[ $file2 ] = $this->fileSys[ $file1 ];
        unset( $this->fileSys[ $file1 ] );
        return TRUE;
    }

    function isDir( $dir )
    {
        if ( array_key_exists( $dir, $this->fileSys ) && !isset( $this->fileSys[ $dir ] ) ) {
            return TRUE;
        }
        return FALSE;
    }

    function search( $folder, $pattern, $flag=NULL ) {
        if( is_array( $pattern ) ) {
            $pattern = '[' .implode( '|', $pattern ) . ']';
        }
        $found = array();
        $pattern = preg_quote( $folder, '/' ) . $pattern;
        foreach ( $this->fileSys as $file => $content ) {
            if ( preg_match( "/{$pattern}/", $file ) ) {
                $found[] = $file;
            }
        }
        return $found;
    }

    function glob( $pattern, $flag = NULL )
    {
        $found = array();
        if ( strpos( $pattern, '{' ) !== FALSE ) {
            $pattern = substr( $pattern, 0, strpos( $pattern, '{' ) );
            $pattern .= substr( $pattern, strpos( $pattern, '{' ) + 1, strpos( $pattern, ',' ) );
        }
        foreach ( $this->fileSys as $file => $content ) {
            if ( substr( $file, 0, strlen( $pattern ) ) == $pattern ) {
                $found[ ] = $file;
            }
        }
        return $found;
    }

    function unlink( $filename )
    {
        if ( array_key_exists( $filename, $this->fileSys ) ) {
            unset( $this->fileSys[ $filename ] );
            return TRUE;
        }
        return FALSE;
    }
}

