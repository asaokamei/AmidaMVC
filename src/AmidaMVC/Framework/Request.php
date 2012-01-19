<?php
namespace AmidaMVC\Framework;

class Request
{
    // +-------------------------------------------------------------+
    /**
     * TODO: change this implementation
     * @return string
     */
    static  function getPathInfo() {
        $uri = self::getUriList();
        $path = implode( '/', $uri );
        return $path;
    }
    // +-------------------------------------------------------------+
    /**
     * returns URI list.
     * @param null $uri
     * @param null $script
     * @return array     returns routes.
     */
    static function getUriList( $uri=NULL, $script=NULL ) {
        if( $uri === NULL ) {
            $uri = preg_replace('@[\/]{2,}@', '/', $_SERVER[ 'REQUEST_URI' ] );
            $uri = explode( '/', $uri );
        }
        if( $script === NULL ) {
            $script = explode( '/', $_SERVER[ 'SCRIPT_NAME' ] );
        }
        for( $i = 0; $i < sizeof( $script ); $i++ ) {
            if( $uri[$i] == $script[$i] ) {
                unset( $uri[$i] );
            }
        }
        return array_values( $uri );
    }
    // +-------------------------------------------------------------+
}
