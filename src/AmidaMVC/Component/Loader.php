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
        $action    = ( isset( $loadInfo['action'] ) ) ? $loadInfo['action'] : $ctrl->getAction();
        \AmidaMVC\Framework\Event::fire( 'Loader::load', $loadInfo );
        $ctrl->setAction( $action );
        // load the file
        $data->setFileName( $file_name );
        if( $file_ext == 'php' && substr( $base_name, 0, 4 ) == '_App' ) {
            self::loadApp( $ctrl, $data, $loadInfo );
        }
        else if( $file_ext == 'php' ) {
            self::loadPhpAsCode( $ctrl, $data, $loadInfo );
        }
        else if( in_array( $file_ext, static::$ext_html ) ) {
            self::loadHtml( $ctrl, $data, $loadInfo );
        }
        else if( in_array( $file_ext, static::$ext_text ) ) {
            self::loadText( $ctrl, $data, $loadInfo );
        }
        else if( in_array( $file_ext, static::$ext_md ) ) {
            self::loadMarkdown( $ctrl, $data, $loadInfo );
        }
        else if( in_array( $file_ext, static::$ext_file ) ) {
            self::loadFile( $ctrl, $data, $loadInfo );
        }
        else if( in_array( $file_ext, static::$ext_asis ) ) {
            self::loadAsIs( $ctrl, $data, $loadInfo, $file_ext );
        }
    }
    // +-------------------------------------------------------------+
    function action_pageNotFound( $ctrl, &$data ) {
        // do something about error 404, a file not found.
        // maybe load sorry file.
    }
    // +-------------------------------------------------------------+
    function loadApp( $_ctrl, &$_siteObj, $loadInfo ) {
        include $loadInfo[ 'file' ];
    }
    // +-------------------------------------------------------------+
    function loadPhpAsExec( \AmidaMVC\Framework\Controller $_ctrl, \AmidaMVC\Component\SiteObj &$_siteObj, $loadInfo ) {
        $content = self::getContentsByOb( $_ctrl, $_siteObj, $loadInfo[ 'file' ] );
        $_siteObj->setContents( $content );
        $_siteObj->setContentType( 'html' );
    }
    // +-------------------------------------------------------------+
    function loadPhpAsCode( \AmidaMVC\Framework\Controller $_ctrl, \AmidaMVC\Component\SiteObj &$_siteObj, $loadInfo ) {
        $content = file_get_contents( $loadInfo[ 'file' ] );
        $_siteObj->setContents( $content );
        $_siteObj->setContentType( 'php' );
    }
    // +-------------------------------------------------------------+
    function loadText( \AmidaMVC\Framework\Controller $_ctrl, \AmidaMVC\Component\SiteObj &$_siteObj, $loadInfo ) {
        $content = self::getContentsByOb( $_ctrl, $_siteObj, $loadInfo[ 'file' ] );
        $_siteObj->setContents( $content );
        $_siteObj->setContentType( 'text' );
    }
    // +-------------------------------------------------------------+
    function loadHtml( \AmidaMVC\Framework\Controller $_ctrl, \AmidaMVC\Component\SiteObj &$_siteObj, $loadInfo ) {
        $content = self::getContentsByOb( $_ctrl, $_siteObj, $loadInfo[ 'file' ] );
        $_siteObj->setContents( $content );
        $_siteObj->setContentType( 'html' );
    }
    // +-------------------------------------------------------------+
    function loadMarkdown( \AmidaMVC\Framework\Controller $_ctrl, \AmidaMVC\Component\SiteObj &$_siteObj, $loadInfo ) {
        $content = self::getContentsByOb( $_ctrl, $_siteObj, $loadInfo[ 'file' ] );
        $_siteObj->setContents( $content );
        $_siteObj->setContentType( 'markdown' );
    }
    // +-------------------------------------------------------------+
    function getContentsByOb( \AmidaMVC\Framework\Controller $_ctrl, \AmidaMVC\Component\SiteObj &$_siteObj, $file_name ) {
        $_site = $_siteObj->get( 'siteObj' );
        ob_start();
        ob_implicit_flush(0);
        require( $file_name );
        $content = ob_get_clean();
        return $content;
    }
    // +-------------------------------------------------------------+
    function loadAsIs( \AmidaMVC\Framework\Controller $_ctrl, \AmidaMVC\Component\SiteObj &$_siteObj, $loadInfo, $_file_ext ) {
        $_siteObj->setHttpContent( file_get_contents( $loadInfo[ 'file' ] ) );
        $_siteObj->setFileName( $loadInfo[ 'file' ] );
        $_siteObj->setContentType( 'as_is' );
    }
    // +-------------------------------------------------------------+
    function loadFile( \AmidaMVC\Framework\Controller $_ctrl, \AmidaMVC\Component\SiteObj &$_siteObj, $loadInfo ) {
        $_siteObj->setContents( file_get_contents( $loadInfo[ 'file' ] ) );
        $_siteObj->setFileName( $loadInfo[ 'file' ] );
    }
    // +-------------------------------------------------------------+
}

