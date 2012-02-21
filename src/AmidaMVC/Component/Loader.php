<?php
namespace AmidaMVC\Component;

/**
 * Loader class to load file.
 * TODO: simplify methods. 
 *  - lots of similar code?
 *  - _raw mode to save contents to contentObj?
 */
class Loader
{
    // extensions to determine which load types. 
    
    /**
     * @var array   file extensions to load as is.
     */
    static $ext_asis = array( 'css', 'js', 'pdf', 'png', 'jpg', 'gif' );
    /**
     * @var array   file extensions to load. 
     */
    static $ext_type = array(
        'php'      => 'html',
        'html'     => 'html',
        'html'     => 'html',
        'md'       => 'markdown',
        'markdown' => 'markdown',
        'text'     => 'text',
        'txt'      => 'text',
    );
    /**
     * @var array   file extensions to edit as file.
     */
    static $ext_edit = array(
        'css'   => 'css',
        'js'    => 'javascript'
    );
    // +-------------------------------------------------------------+
    /**
     * loads file based on $loadInfo, determined by Router.
     * specify absolute path of a file as $loadInfo[ 'file' ].
     * setMyAction for _src and _raw mode
     * @static
     * @param $_ctrl
     * @param $_siteObj
     * @param array $loadInfo    info about file to load from Router.
     * @return array
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
    /**
     * nothing to do for page not found case. 
     * @static
     * @param $_ctrl
     * @param $_siteObj
     */
    static function action_pageNotFound( $_ctrl, &$_siteObj ) {
        // do something about error 404, a file not found.
        // maybe load sorry file.
    }
    // +-------------------------------------------------------------+
    /**
     * re-edit contents when edit fails to put content.
     * put content as $loadInfo[ 'content' ] to use.
     * this action is called only from Filer. 
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
    /**
     * load file source for editing, set content type to text.
     * this action is called only from Filer.
     * @static
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param SiteObj $_siteObj
     * @param array $loadInfo
     */
    static function action_edit(
        \AmidaMVC\Framework\Controller $_ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj,
        array $loadInfo )
    {
        $file_ext  = pathinfo( $loadInfo[ 'file' ], PATHINFO_EXTENSION );
        if( isset( static::$ext_type[ $file_ext ] ) ||
            isset( static::$ext_edit[ $file_ext ] ))
        {
            $content = static::getContentsByGet( $_ctrl, $_siteObj, $loadInfo[ 'file' ] );
            $_siteObj->setContents( $content );
            $_siteObj->setContentType( 'text' );
            $_siteObj->setFileName( $loadInfo[ 'file' ] );
        }
        else if( static::loadAsIs( $_ctrl, $_siteObj, $loadInfo ) ) {
                $_ctrl->setAction( $_ctrl->defaultAct() );
            }
        else {
            // ignore this type of file.  
        }
    }
    // +-------------------------------------------------------------+
    /**
     * load file source for _src, set content type to php (source code).
     * @static
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param SiteObj $_siteObj
     * @param array $loadInfo
     */
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
    /**
     * load file contents for _raw mode, load contents to 
     * responseObj and set content type to text. 
     * @static
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param SiteObj $_siteObj
     * @param array $loadInfo
     */
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
    /**
     * check load mode (_raw, _src, or default as _view). 
     * note: _edit and _reedit set only by Filer. 
     * @static
     * @param $_siteObj
     * @return string
     */
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
    /**
     * loads _App.php. just include the file. all the work must
     * be done in the _App.php, though.
     * @static
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param SiteObj $_siteObj
     * @param $loadInfo
     * @return void
     */
    static function loadApp(
        \AmidaMVC\Framework\Controller $_ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj, 
        $loadInfo ) {
        include $loadInfo[ 'file' ];
    }
    // +-------------------------------------------------------------+
    /**
     * load file contents for _view. 
     * @static
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param SiteObj $_siteObj
     * @param $loadInfo
     */
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
    /**
     * get file source using file_get_contents function. 
     * @static
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param SiteObj $_siteObj
     * @param $file_name
     * @return null|string
     */
    static function getContentsByGet(
        \AmidaMVC\Framework\Controller $_ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj, 
        $file_name ) {
        return file_get_contents( $file_name );
    }
    // +-------------------------------------------------------------+
    /**
     * get file contents by executing file and ob_{start|get_clean}.
     * @static
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param SiteObj $_siteObj
     * @param $file_name
     * @return string
     */
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
    /**
     * get file source by get_file_content as is; save contents in
     * responseObj (skip html content). 
     * @static
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param SiteObj $_siteObj
     * @param $loadInfo
     * @return bool
     */
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

