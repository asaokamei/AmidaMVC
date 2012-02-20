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
        if( $loadMode != '_view' ) {
            $_ctrl->setMyAction( $loadMode );
            return $loadInfo;
        }
        $file_name = $loadInfo['file'];
        $base_name = basename( $file_name );
        $file_ext  = pathinfo( $file_name, PATHINFO_EXTENSION );
        
        $loadInfo[ 'ext' ] = $file_ext;
        $loadInfo[ 'loadMode' ] = $loadMode;
        $action    = ( isset( $loadInfo['action'] ) ) ? $loadInfo['action'] : $_ctrl->getAction();
        // load the file
        static::fireLoad( $loadInfo );
        if( $file_ext == 'php' && substr( $base_name, 0, 4 ) == '_App' ) {
            static::loadApp( $_ctrl, $_siteObj, $loadInfo );
            $loadInfo[ 'loadMode' ] = '_App';
        }
        else if( isset( static::$ext_type[$file_ext] ) ) {
            $loadInfo[ 'loadMode' ] = $loadMode;
            static::load_view( $_ctrl, $_siteObj, $loadInfo );
        }
        else if( in_array( $file_ext, static::$ext_asis ) ) {
            static::loadAsIs( $_ctrl, $_siteObj, $loadInfo );
            $loadInfo[ 'loadMode' ] = 'AsIs';
        }
        $_ctrl->setAction( $action );
        $_siteObj->set( 'loadInfo', $loadInfo );
        $_siteObj->setFileName( $file_name );
    }
    // +-------------------------------------------------------------+
    static function action_pageNotFound( $_ctrl, &$_siteObj ) {
        // do something about error 404, a file not found.
        // maybe load sorry file.
    }
    // +-------------------------------------------------------------+
    /**
     * re-edit contents when edit fails to put content.
     * put content as $loadInfo[ 'content' ] to use.
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param SiteObj $_siteObj
     * @param $loadInfo
     */
    static function action_reedit(
        \AmidaMVC\Framework\Controller $_ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj,
        $loadInfo ) {
        $_siteObj->setContents( $loadInfo[ 'content' ] );
        $_siteObj->setContentType( 'as_is' );
    }
    // +-------------------------------------------------------------+
    static function action_edit(
        \AmidaMVC\Framework\Controller $_ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj,
        array $loadInfo )
    {
        $file_ext  = pathinfo( $loadInfo[ 'file' ], PATHINFO_EXTENSION );
        if( static::loadAsIs( $_ctrl, $_siteObj, $loadInfo ) ) {
            $_ctrl->setAction( $_ctrl->defaultAct() );
        }
        else if( isset( static::$ext_type[ $file_ext ] ) ) {
            $content = static::getContentsByGet( $_ctrl, $_siteObj, $loadInfo[ 'file' ] );
            $_siteObj->setContents( $content );
            $_siteObj->setContentType( 'text' );
            $_siteObj->setFileName( $loadInfo[ 'file' ] );
        }
        else {
            // ignore this type of file.  
        }
    }
    // +-------------------------------------------------------------+
    static function action_src(
        \AmidaMVC\Framework\Controller $_ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj,
        array $loadInfo )
    {
        $file_ext  = pathinfo( $loadInfo[ 'file' ], PATHINFO_EXTENSION );
        if( static::loadAsIs( $_ctrl, $_siteObj, $loadInfo ) ) {
            $_ctrl->setAction( $_ctrl->defaultAct() );
        }
        else if( isset( static::$ext_type[ $file_ext ] ) ) {
            $content = static::getContentsByGet( $_ctrl, $_siteObj, $loadInfo[ 'file' ] );
            $_siteObj->setContents( $content );
            $_siteObj->setContentType( 'php' );
            $_siteObj->setFileName( $loadInfo[ 'file' ] );
        }
        else {
            // ignore this type of file.  
        }
    }
    // +-------------------------------------------------------------+
    static function action_raw(
        \AmidaMVC\Framework\Controller $_ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj,
        array $loadInfo )
    {
        $file_ext  = pathinfo( $loadInfo[ 'file' ], PATHINFO_EXTENSION );
        if( static::loadAsIs( $_ctrl, $_siteObj, $loadInfo ) ) {
            $_ctrl->setAction( $_ctrl->defaultAct() );
        }
        else if( isset( static::$ext_type[ $file_ext ] ) ) {
            $content = static::getContentsByOb( $_ctrl, $_siteObj, $loadInfo[ 'file' ] );
            $_siteObj->setHttpContent( $content );
            $_siteObj->setContentType( 'text' );
            $_siteObj->setEmitAsIs();
        }
        else {
            // ignore this type of file.  
        }
    }
    // +-------------------------------------------------------------+
    static function findLoadMode( &$_siteObj ) {
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
    static function loadApp(
        \AmidaMVC\Framework\Controller $_ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj, 
        $loadInfo ) {
        include $loadInfo[ 'file' ];
    }
    // +-------------------------------------------------------------+
    static function load_view(
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
    static function load_edit(
        \AmidaMVC\Framework\Controller $_ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj,
        $loadInfo ) {
        $content = static::getContentsByGet( $_ctrl, $_siteObj, $loadInfo[ 'file' ] );
        $_siteObj->setContents( $content );
        $_siteObj->setContentType( 'as_is' );
    }
    // +-------------------------------------------------------------+
    static function getContentsByGet(
        \AmidaMVC\Framework\Controller $_ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj, 
        $file_name ) {
        return file_get_contents( $file_name );
    }
    // +-------------------------------------------------------------+
    static function getContentsByOb(
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
    static function loadAsIs(
        \AmidaMVC\Framework\Controller $_ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj, 
        $loadInfo ) 
    {
        $file_ext  = pathinfo( $loadInfo[ 'file' ], PATHINFO_EXTENSION );
        if( in_array( $file_ext, static::$ext_asis ) ) {
            $responseObj = $_siteObj->get( 'responseObj' );
            $responseObj->content = static::getContentsByGet( $_ctrl, $_siteObj, $loadInfo['file'] );
            $responseObj->mime_type = '';
            $_siteObj->setFileName( $loadInfo[ 'file' ] );
            $_siteObj->setContentType( 'as_is' );
            $_siteObj->set( 'responseObj', $responseObj );
            $_siteObj->setEmitAsIs();
            return TRUE;
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    static function fireLoad( $loadInfo ) {
    }
    // +-------------------------------------------------------------+
}

