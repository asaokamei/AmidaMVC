<?php
namespace AmidaMVC\Component;
/**
 * TODO: make Config
 *  for controlling multiple-lang, admin, debug info,
 *  dev/staging/real server.
*
 */
class Config
{
    /**
     * @var array  list of available mode. 
     */
    static $mode_list = array( '_logout', '_dev' );
    static $file_list = array( '_edit', '_put', '_pub', '_del' );
    // +-------------------------------------------------------------+
    function actionDefault(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$siteObj )
    {
        self::setRoute( $ctrl, $siteObj );
        if( $siteObj->siteObj->mode ) {
            $ctrl->setAction( $siteObj->siteObj->mode );
            $ctrl->addComponentAfter( 'auth', 'Config', 'config' );
        }
    }
    // +-------------------------------------------------------------+
    function action_dev(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj )
    {
        $command   = $_siteObj->siteObj->command;
        $filer_cmd = FALSE;
        foreach( static::$file_list as $cmd ) {
            if( in_array( $cmd, $command ) ) {
                $filer_cmd = $cmd;
                break;
            }
        }
        if( $filer_cmd ) {
            // set to filer's command. 
            $ctrl->setAction( $filer_cmd );
            $_siteObj->siteObj->file_mode = $filer_cmd;
            $ctrl->addComponentAfter( 'router', 'Filer', 'filer' );
        }
        return;
    }
    // +-------------------------------------------------------------+
    function checkInit(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj )
    {
        if( $_siteObj->get( 'siteObj' ) ) {
            return TRUE;
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    function findMode( $command )
    {
        foreach( static::$mode_list as $mode ) {
            if( in_array( $mode, $command ) ) {
                // found special mode. 
                return $mode;
            }
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    function setRoute( 
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj ) 
    {
        $siteDefault = array(
            'base_url'  => $ctrl->base_url,
            'path_info_ctrl' => $ctrl->path_info,
            'path_info' => '',
            'routes'    => array(),
            'command'   => array(),
            'prefix_cmd' => $ctrl->prefixCmd,
            'mode' => NULL,
            'file_mode' => NULL,
        );
        $paths = explode( '/', $siteDefault[ 'path_info_ctrl' ] );
        foreach( $paths as $cmd ) {
            if( empty( $cmd ) ) continue;
            if( $cmd === '..' ) continue;
            if( substr( $cmd, 0, 1 ) === '.' ) continue;
            if( substr( $cmd, 0, 1 ) === $siteDefault[ 'prefix_cmd' ] ) {
                $siteDefault[ 'command' ][] = $cmd;
            }
            else {
                $siteDefault[ 'routes' ][] = $cmd;
            }
        }
        // setup path_info.
        $siteDefault[ 'path_info' ] = implode( '/', $siteDefault[ 'routes' ] );
        if( empty( $siteDefault[ 'path_info' ] ) ) {
            $siteDefault[ 'path_info' ] = '/';
        }
        $ctrl->path_info = $siteDefault[ 'path_info' ];
        // setup command. 
        $siteDefault[ 'command' ] = array_unique( $siteDefault[ 'command' ] );
        if( !empty( $siteDefault[ 'command'] ) ) {
            if( $mode = static::findMode( $siteDefault[ 'command'] ) ) {
                $ctrl->mode = $mode;
                $ctrl->setAction( $mode );
                $siteDefault[ 'mode' ] = $mode;
            }
        }
        $_siteObj->set( 'siteObj', $siteDefault );
    }
    // +-------------------------------------------------------------+
}