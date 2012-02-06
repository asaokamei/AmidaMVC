<?php
namespace AmidaMVC\Component;

/**
 * Loader class to load file.
 */
class Loader
{
    // extensions to determine which load types. 
    static $ext_asis = array( 'css', 'js', 'pdf', 'png', 'jpg', 'gif' );
    static $ext_type = array(
        'php'      => 'html',
        'html'     => 'html',
        'html'     => 'html',
        'md'       => 'markdown',
        'markdown' => 'markdown',
        'text'     => 'text',
        'txt'      => 'text',
    );
    // +-------------------------------------------------------------+
    /**
     * loads file based on $loadInfo, determined by Router.
     * specify absolute path of a file as $loadInfo[ 'file' ].
     * @static
     * @param $_ctrl
     * @param $_siteObj
     * @param array $loadInfo    info about file to load from Router.
     */
    static function actionDefault( 
        \AmidaMVC\Framework\Controller $_ctrl, 
        \AmidaMVC\Component\SiteObj &$_siteObj, 
        $loadInfo ) 
    {
        $loadMode  = static::findLoadMode( $_siteObj );
        $file_name = $loadInfo['file'];
        $base_name = basename( $file_name );
        $file_ext  = pathinfo( $file_name, PATHINFO_EXTENSION );
        
        $loadInfo[ 'ext' ] = $file_ext;
        $loadInfo[ 'loaadMode' ] = $loadMode;
        $action    = ( isset( $loadInfo['action'] ) ) ? $loadInfo['action'] : $_ctrl->getAction();
        $_ctrl->setAction( $action );
        $_siteObj->set( 'loadInfo', $loadInfo );
        $_siteObj->setFileName( $file_name );
        // load the file
        static::fireLoad( $loadInfo );
        if( $file_ext == 'php' && substr( $base_name, 0, 4 ) == '_App' ) {
            static::loadApp( $_ctrl, $_siteObj, $loadInfo );
        }
        else if( isset( static::$ext_type[$file_ext] ) ) {
            $method = 'load' . $loadMode;
            static::$method( $_ctrl, $_siteObj, $loadInfo );
        }
        else if( in_array( $file_ext, static::$ext_asis ) ) {
            static::loadAsIs( $_ctrl, $_siteObj, $loadInfo );
        }
    }
    // +-------------------------------------------------------------+
    function action_pageNotFound( $_ctrl, &$_siteObj ) {
        // do something about error 404, a file not found.
        // maybe load sorry file.
    }
    // +-------------------------------------------------------------+
    function findLoadMode( &$_siteObj ) {
        $modes = array( '_raw', '_src' );
        $loadMode  = '_view';
        foreach( $modes as $mode ) {
            if( in_array( $mode, $_siteObj->siteObj->command ) ) {
                $loadMode = $mode;
                break;
            }
        }
        return $loadMode;
    }
    // +-------------------------------------------------------------+
    function loadApp(
        \AmidaMVC\Framework\Controller $_ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj, 
        $loadInfo ) {
        include $loadInfo[ 'file' ];
    }
    // +-------------------------------------------------------------+
    function load_view(
        \AmidaMVC\Framework\Controller $_ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj, 
        $loadInfo ) {
        $content = static::getContentsByOb( $_ctrl, $_siteObj, $loadInfo[ 'file' ] );
        $_siteObj->setContents( $content );
        $file_ext  = pathinfo( $loadInfo[ 'file' ], PATHINFO_EXTENSION );
        $file_type = static::$ext_type[ $file_ext ];
        $_siteObj->setContentType( $file_type );
    }
    // +-------------------------------------------------------------+
    function load_src(
        \AmidaMVC\Framework\Controller $_ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj, 
        $loadInfo ) {
        $content = static::getContentsByGet( $_ctrl, $_siteObj, $loadInfo[ 'file' ] );
        $_siteObj->setContents( $content );
        $_siteObj->setContentType( 'php' );
    }
    // +-------------------------------------------------------------+
    function load_raw(
        \AmidaMVC\Framework\Controller $_ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj, 
        $loadInfo ) {
        $content = static::getContentsByOb( $_ctrl, $_siteObj, $loadInfo[ 'file' ] );
        $_siteObj->setHttpContent( $content );
        $_siteObj->setContentType( 'text' );
        $_siteObj->setEmitAsIs();
    }
    // +-------------------------------------------------------------+
    function getContentsByGet(
        \AmidaMVC\Framework\Controller $_ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj, 
        $file_name ) {
        return file_get_contents( $file_name );
    }
    // +-------------------------------------------------------------+
    function getContentsByOb(
        \AmidaMVC\Framework\Controller $_ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj, 
        $file_name ) {
        ob_start();
        ob_implicit_flush(0);
        require( $file_name );
        $content = ob_get_clean();
        return $content;
    }
    // +-------------------------------------------------------------+
    function loadAsIs(
        \AmidaMVC\Framework\Controller $_ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj, 
        $loadInfo ) {
        $responseObj = $_siteObj->get( 'responseObj' );
        $responseObj->content = static::getContentsByGet( $_ctrl, $_siteObj, $loadInfo['file'] );
        $responseObj->mime_type = '';
        $_siteObj->setFileName( $loadInfo[ 'file' ] );
        $_siteObj->setContentType( 'as_is' );
        $_siteObj->set( 'responseObj', $responseObj );
        $_siteObj->setEmitAsIs();
    }
    // +-------------------------------------------------------------+
    function fireLoad( $loadInfo ) {
    }
    // +-------------------------------------------------------------+
}

