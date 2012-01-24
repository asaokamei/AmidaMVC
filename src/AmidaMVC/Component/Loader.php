<?php
namespace AmidaMVC\Component;

/**
 * Loader class to load file.
 */
class Loader
{
    // extensions to determine which load types. 
    static $ext_file = array( 'php', 'html', 'html', 'md', 'markdown', 'text', 'txt' );
    static $ext_asis = array( 'css', 'js', 'pdf', 'png', 'jpg', 'gif' );
    // +-------------------------------------------------------------+
    static function _init() {
    }
    // +-------------------------------------------------------------+
    /**
     * loads file.
     * specify absolute path of a file to load in $loadInfo[ 'file' ].
     * @static
     * @param $ctrl
     * @param $data
     * @param $loadInfo    info about file to load from Router.
     */
    static function actionDefault( $ctrl, &$data, $loadInfo ) {
        $file_name = $loadInfo['file'];
        $base_name  = basename( $file_name );
        $file_ext  = pathinfo( $file_name, PATHINFO_EXTENSION );
        $action    = ( $loadInfo['action'] ) ? $loadInfo['action'] : $ctrl->currAct();
        \AmidaMVC\Component\Debug::bug( 'head', "loading file: ".$file_name );
        \AmidaMVC\Framework\Event::fire( 'Loader::load', $loadInfo );
        $ctrl->currAct( $action );
        if( $file_ext == 'php' && substr( $base_name, 0, 4 ) == '_App' ) {
            include $loadInfo[ 'file' ];
        }
        else if( in_array( $file_ext, static::$ext_file ) ) {
            self::loadFile( $data, $loadInfo );
        }
        else if( in_array( $file_ext, static::$ext_asis ) ) {
            self::loadAsIs( $data, $loadInfo, $file_ext );
        }
    }
    // +-------------------------------------------------------------+
    function actionPageNotFound( $ctrl, &$data ) {
        // do something about error 404, a file not found.
        // maybe load sorry file.
    }
    // +-------------------------------------------------------------+
    function loadFile( &$data, $loadInfo ) {
        $data->setContents( file_get_contents( $loadInfo[ 'file' ] ) );
        $data->setFileName( $loadInfo[ 'file' ] );
    }
    // +-------------------------------------------------------------+
    function findMimeType( $_file_ext ) {
        switch( strtolower( $_file_ext ) ) {
            case 'css':
                $mime = 'text/css';
                break;
            case 'js':
            case 'javascript':
                $mime = 'text/javascript';
                break;
            case 'jpg':
            case 'jpeg':
                $mime = 'image/jpeg';
                break;
            case 'gif':
                $mime = 'image/gif';
                break;
            case 'png':
                $mime = 'image/png';
                break;
        }
        return $mime;
    }
    // +-------------------------------------------------------------+
    function loadAsIs( &$data, $loadInfo, $_file_ext ) {
        $data->setHttpContent( file_get_contents( $loadInfo[ 'file' ] ) );
        $mime  = self::findMimeType( $_file_ext );
        $data->setMimeType( $mime );
    }
    // +-------------------------------------------------------------+
    static function getAction( $string ) {
        if( is_array( $string ) ) $string = $string[0];
        $action = preg_replace( '/[^._a-zA-Z0-9]/m', '', $string );
        return $action;
    }
    // +-------------------------------------------------------------+
}


