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
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $loadInfo    info about file to load from Router.
     * @return bool
     */
    static function actionDefault( $_ctrl, &$_pageObj, $loadInfo )
    {
        if( !isset( $loadInfo[ 'file' ] ) ) {
            return FALSE;
        }
        $load = static::$_load;
        $file_name = $loadInfo[ 'file' ];

        // load the file
        if( is_callable( $file_name ) ) {
            /** @var string $file_name  */
            $_pageObj->setContent( $file_name( $loadInfo ) );
            if( isset( $loadInfo[ 'type' ] ) ) {
                $_pageObj->contentType( $loadInfo[ 'type' ] );
            }
        }
        else {
            // it's a file. load contents.
            $file_loc  = $_ctrl->getLocation( $file_name );
            $loadInfo[ 'base_name' ] = basename( $file_name );
            $loadInfo[ 'file_ext'  ]  = pathinfo( $file_name, PATHINFO_EXTENSION );
            $loadInfo[ 'file_type' ] = $load::getFileType( $file_name );
            if( $load::isView( $file_loc ) ) {
                $_pageObj->setContent( $load::getContentsByBuffer( $file_loc ) );
                $loadInfo[ 'loadMode' ] = '_view';
            }
            else if( $load::isAsIs( $file_loc ) ) {
                $_pageObj->getContent( $load::getContentsByGet( $file_loc ) );
                $loadInfo[ 'loadMode' ] = '_asIs';
            }
            $type = $load::getFileType( $file_name );
            $_pageObj->contentType( $type );
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
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @return array
     */
    static function action_PageNotFound( $_ctrl, $_pageObj )
    {
        if( $_ctrl->getOption( 'pageNotFound_file' ) ) {
            $loadInfo = array(
                'file' => $_ctrl->getOption( 'pageNotFound_file' ),
                'action' => $_ctrl->defaultAct(),
            );
            $_ctrl->setMyAction( $_ctrl->defaultAct() );
            return $loadInfo;
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
}