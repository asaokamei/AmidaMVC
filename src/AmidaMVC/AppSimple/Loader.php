<?php
namespace AmidaMVC\AppSimple;

class Loader
{
    static $isView   = array( '\AmidaMVC\Tools\Load', 'isView' );
    static $isText   = array( '\AmidaMVC\Tools\Load', 'isText' );
    static $isAsIs   = array( '\AmidaMVC\Tools\Load', 'isAsIs' );
    static $fileGet  = array( '\AmidaMVC\Tools\Load', 'isAsIs' );
    static $fileBuf  = array( '\AmidaMVC\Tools\Load', 'isAsIs' );
    static $fileType = array( '\AmidaMVC\Tools\Load', 'getFileType' );
    // +-------------------------------------------------------------+
    /**
     * loads file based on $loadInfo, determined by Router.
     * specify absolute path of a file as $loadInfo[ 'file' ].
     * setMyAction for _src and _raw mode
     * @static
     * @param \AmidaMVC\AppSimple\Controller $_ctrl
     * @param \AmidaMVC\AppSimple\SiteObj $_siteObj
     * @param array $loadInfo    info about file to load from Router.
     * @return array
     */
    static function actionDefault( $_ctrl, &$_siteObj, $loadInfo )
    {
        if( !isset( $loadInfo[ 'file' ] ) ) {
            return FALSE;
        }
        $file_name = $loadInfo[ 'file' ];
        $loadInfo[ 'base_name' ] = basename( $file_name );
        $loadInfo[ 'file_ext'  ]  = pathinfo( $file_name, PATHINFO_EXTENSION );
        $loadInfo[ 'file_type' ] = call_user_func( self::$fileType, $file_name );

        // load the file
        if( call_user_func( static::$isView, $file_name ) ) {
            $_siteObj->setContent( call_user_func( static::$fileBuf, $file_name ) );
            $loadInfo[ 'loadMode' ] = '_view';
        }
        else if( call_user_func( static::$isAsIs, $file_name ) ) {
            $_siteObj->getContent( call_user_func( static::$fileGet, $file_name ) );
            $loadInfo[ 'loadMode' ] = '_asIs';
        }
        $action    = ( isset( $loadInfo['action'] ) ) ? $loadInfo['action'] : $_ctrl->getAction();
        $_ctrl->setAction( $action );
        $_siteObj->loadInfo = $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * action for page not found; this action is invoked only from
     * _App.php or some other models... reload pageNofFound file if
     * set in siteObj. if not, generate simple err404 contents.
     * @static
     * @param \AmidaMVC\AppSimple\Controller $_ctrl
     * @param \AmidaMVC\AppSimple\SiteObj $_siteObj
     * @return array
     */
    static function action_PageNotFound( $_ctrl, $_siteObj )
    {
        // show some excuses, or blame user for not finding a page.
        $contents  = "#Error 404\n\npage not found...";
        $_siteObj->status( '404' );
        $_siteObj->title( 'Page Not Found' );
        $_siteObj->setContent( $contents );
    }
    // +-------------------------------------------------------------+
}