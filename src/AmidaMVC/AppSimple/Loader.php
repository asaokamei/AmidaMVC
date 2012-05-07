<?php
namespace AmidaMVC\AppSimple;

class Loader extends \AmidaMVC\Framework\AModule implements \AmidaMVC\Framework\IModule
{
    /**
     * @var \AmidaMVC\Tools\Load   static class for loading methods.
     */
    var $_loadClass = '\AmidaMVC\Tools\Load';
    /**
     * @var array   list of supported commands.
     */
    var $commands = array( '_src' );
    // +-------------------------------------------------------------+
    /**
     * initialize class.
     * @param array $option    option to initialize.
     */
    function _init( $option=array() ) {
        if( isset( $option[ 'loadClass' ] ) ) {
            $this->_loadClass = $option[ 'loadClass' ];
        }
    }
    // +-------------------------------------------------------------+
    /**
     * loads file based on $loadInfo, determined by Router.
     * specify absolute path of a file as $loadInfo[ 'file' ].
     * setMyAction for _src and _raw mode
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $loadInfo    info about file to load from Router.
     * @return bool
     */
    function actionDefault( $_ctrl, &$_pageObj, $loadInfo=array() )
    {
        if( !isset( $loadInfo[ 'file' ] ) ) {
            return FALSE;
        }
        $command = $this->findCommand( $_ctrl->getCommands() );
        $load = $this->_loadClass;
        $file_name = $loadInfo[ 'file' ];

        /** @var string $file_name  */
        // load the file
        if( is_callable( $file_name ) ) {
            // loading output from a closure function.
            // set type to as specified in loadInfo['type'].
            // and set loadMode as 'closure'.
            $_pageObj->setContent( $file_name( $loadInfo ) );
            if( isset( $loadInfo[ 'type' ] ) ) {
                $_pageObj->contentType( $loadInfo[ 'type' ] );
            }
            $loadInfo[ 'loadMode' ] = '_closure';
        }
        else {
            // it's a file. load contents.
            $loadInfo[ 'base_name' ] = basename( $file_name );
            $loadInfo[ 'file_ext'  ]  = pathinfo( $file_name, PATHINFO_EXTENSION );
            $loadInfo[ 'file_type' ] = $load::getFileType( $file_name );
            if( $load::isView( $file_name ) && $command == '_src' ) {
                $_pageObj->setContent( $load::getContentsByGet( $file_name ) );
                $loadInfo[ 'loadMode' ] = '_src';
            }
            else if( $load::isView( $file_name ) ) {
                $_pageObj->setContent( $load::getContentsByBuffer(
                    $file_name, array( '_ctrl'=>$_ctrl, '_pageObj'=>$_pageObj, '_loadInfo'=>$loadInfo ) )
                );
                $loadInfo[ 'loadMode' ] = '_view';
            }
            else if( $load::isAsIs( $file_name ) ) {
                $_pageObj->setContent( $load::getContentsByGet( $file_name ) );
                $loadInfo[ 'loadMode' ] = '_asIs';
            }
            $type = $load::getFileType( $file_name );
            $_pageObj->contentType( $type );
        }
        if( isset( $loadInfo['action'] ) ) {
            $_ctrl->setAction( $loadInfo['action'] );
        }
        $_pageObj->loadInfo = $loadInfo;
        return TRUE;
    }
    // +-------------------------------------------------------------+
    /**
     * do nothing for pageNotFound. sorry page loaded by Emitter.
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @return array
     */
    function action_PageNotFound( $_ctrl, $_pageObj )
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