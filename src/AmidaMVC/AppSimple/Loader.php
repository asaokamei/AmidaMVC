<?php
namespace AmidaMVC\AppSimple;

class Loader
{
    /**
     * @var \AmidaMVC\Tools\Load   static class for loading methods.
     */
    static $_load = '\AmidaMVC\Tools\Load';
    // +-------------------------------------------------------------+
    /**
     * initialize class.
     * @static
     * @param array $option    option to initialize.
     */
    static function _init( $option=array() ) {
        if( isset( $option[ 'loadClass' ] ) ) {
            static::$_load = $option[ 'loadClass' ];
        }
    }
    // +-------------------------------------------------------------+
    /**
     * loads file based on $loadInfo, determined by Router.
     * specify absolute path of a file as $loadInfo[ 'file' ].
     * setMyAction for _src and _raw mode
     * @static
     * @param \AmidaMVC\AppSimple\Controller $_ctrl
     * @param \AmidaMVC\AppSimple\SiteObj $_siteObj
     * @param array $loadInfo    info about file to load from Router.
     * @return bool
     */
    static function actionDefault( $_ctrl, &$_siteObj, $loadInfo )
    {
        if( !isset( $loadInfo[ 'file' ] ) ) {
            return FALSE;
        }
        $load = static::$_load;
        $file_name = $loadInfo[ 'file' ];
        $loadInfo[ 'base_name' ] = basename( $file_name );
        $loadInfo[ 'file_ext'  ]  = pathinfo( $file_name, PATHINFO_EXTENSION );
        $loadInfo[ 'file_type' ] = $load::getFileType( $file_name );

        // load the file
        if( $load::isView( $file_name ) ) {
            $_siteObj->setContent( $load::getContentsByBuffer( $file_name ) );
            $loadInfo[ 'loadMode' ] = '_view';
        }
        else if( $load::isAsIs( $file_name ) ) {
            $_siteObj->getContent( $load::getContentsByGet( $file_name ) );
            $loadInfo[ 'loadMode' ] = '_asIs';
        }
        $action    = ( isset( $loadInfo['action'] ) ) ? $loadInfo['action'] : $_ctrl->getAction();
        $_ctrl->setAction( $action );
        $_ctrl->loadInfo = $loadInfo;
        return TRUE;
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