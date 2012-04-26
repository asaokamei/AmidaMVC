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
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_siteObj
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
        if( is_callable( $file_name ) ) {
            /** @var string $file_name  */
            $_siteObj->setContent( $file_name( $_ctrl, $_siteObj, $loadInfo ) );
        }
        else if( $load::isView( $file_name ) ) {
            $_siteObj->setContent( $load::getContentsByBuffer( $file_name ) );
            $loadInfo[ 'loadMode' ] = '_view';
        }
        else if( $load::isAsIs( $file_name ) ) {
            $_siteObj->getContent( $load::getContentsByGet( $file_name ) );
            $loadInfo[ 'loadMode' ] = '_asIs';
        }
        if( isset( $loadInfo['action'] ) ) {
            $_ctrl->setAction( $loadInfo['action'] );
        }
        $_ctrl->loadInfo = $loadInfo;
        return TRUE;
    }
    // +-------------------------------------------------------------+
    /**
     * do nothing for pageNotFound. sorry page loaded by Emitter.
     * @static
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_siteObj
     * @return array
     */
    static function action_PageNotFound( $_ctrl, $_siteObj )
    {
    }
    // +-------------------------------------------------------------+
}