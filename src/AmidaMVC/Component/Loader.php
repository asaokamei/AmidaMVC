<?php
namespace AmidaMVC\Component;

/**
 * Loader class to load file.
 */
class Loader
{
    // extensions to determine which load types. 
    static $ext_php  = array( 'php' );
    static $ext_html = array( 'html', 'html' );
    static $ext_md   = array( 'md', 'markdown' );
    static $ext_text = array( 'text', 'txt' );
    static $ext_file = array( '' );
    static $ext_asis = array( 'css', 'js', 'pdf', 'png', 'jpg', 'gif' );
    // +-------------------------------------------------------------+
    /**
     * loads file based on $loadInfo, determined by Router.
     * specify absolute path of a file as $loadInfo[ 'file' ].
     * @static
     * @param $ctrl
     * @param $data
     * @param $loadInfo    info about file to load from Router.
     */
    static function actionDefault( 
        \AmidaMVC\Framework\Controller $ctrl, 
        \AmidaMVC\Component\SiteObj &$data, 
        $loadInfo ) 
    {
        $file_name = $loadInfo['file'];
        $base_name = basename( $file_name );
        $file_ext  = pathinfo( $file_name, PATHINFO_EXTENSION );
        $loadInfo[ 'ext' ] = $file_ext;
        $action    = ( $loadInfo['action'] ) ? $loadInfo['action'] : $ctrl->getAction();
        \AmidaMVC\Framework\Event::fire( 'Loader::load', $loadInfo );
        $ctrl->setAction( $action );
        // load the file
        $data->setFileName( $file_name );
        if( $file_ext == 'php' && substr( $base_name, 0, 4 ) == '_App' ) {
            self::loadApp( $data, $loadInfo );
        }
        else if( $file_ext == 'php' ) {
            self::loadPhpAsCode( $data, $loadInfo );
        }
        else if( in_array( $file_ext, static::$ext_html ) ) {
            self::loadHtml( $data, $loadInfo );
        }
        else if( in_array( $file_ext, static::$ext_text ) ) {
            self::loadText( $data, $loadInfo );
        }
        else if( in_array( $file_ext, static::$ext_md ) ) {
            self::loadMarkdown( $data, $loadInfo );
        }
        else if( in_array( $file_ext, static::$ext_file ) ) {
            self::loadFile( $data, $loadInfo );
        }
        else if( in_array( $file_ext, static::$ext_asis ) ) {
            self::loadAsIs( $data, $loadInfo, $file_ext );
        }
    }
    // +-------------------------------------------------------------+
    function action_pageNotFound( $ctrl, &$data ) {
        // do something about error 404, a file not found.
        // maybe load sorry file.
    }
    // +-------------------------------------------------------------+
    function loadApp( &$data, $loadInfo ) {
        include $loadInfo[ 'file' ];
    }
    // +-------------------------------------------------------------+
    function loadPhpAsCode( \AmidaMVC\Component\SiteObj &$data, $loadInfo ) {
        $content = file_get_contents( $loadInfo[ 'file' ] );
        $data->setContents( $content );
        $data->setContentType( 'php' );
    }
    // +-------------------------------------------------------------+
    function loadText( \AmidaMVC\Component\SiteObj &$data, $loadInfo ) {
        $content = file_get_contents( $loadInfo[ 'file' ] );
        $data->setContents( $content );
        $data->setContentType( 'text' );
    }
    // +-------------------------------------------------------------+
    function loadHtml( \AmidaMVC\Component\SiteObj &$data, $loadInfo ) {
        $content = file_get_contents( $loadInfo[ 'file' ] );
        $data->setContents( $content );
    }
    // +-------------------------------------------------------------+
    function loadMarkdown( \AmidaMVC\Component\SiteObj &$data, $loadInfo ) {
        $content = file_get_contents( $loadInfo[ 'file' ] );
        $data->setContents( $content );
        $data->setContentType( 'markdown' );
    }
    // +-------------------------------------------------------------+
    function loadAsIs( \AmidaMVC\Component\SiteObj &$data, $loadInfo, $_file_ext ) {
        $data->setHttpContent( file_get_contents( $loadInfo[ 'file' ] ) );
        $data->setFileName( $loadInfo[ 'file' ] );
        $data->setContentType( 'as_is' );
    }
    // +-------------------------------------------------------------+
    function loadFile( \AmidaMVC\Component\SiteObj &$data, $loadInfo ) {
        $data->setContents( file_get_contents( $loadInfo[ 'file' ] ) );
        $data->setFileName( $loadInfo[ 'file' ] );
    }
    // +-------------------------------------------------------------+
}

