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
    static $modes = array( '_dev' );
    // +-------------------------------------------------------------+
    function actionDefault(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$data )
    {
        self::setRoute( $ctrl, $data );
    }
    // +-------------------------------------------------------------+
    function findMode( $command )
    {
        foreach( static::$modes as $mode ) {
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
        \AmidaMVC\Component\SiteObj &$data ) 
    {
        $siteDefault = array(
            'base_url'  => $ctrl->base_url,
            'path_info_ctrl' => $ctrl->path_info,
            'path_info' => '',
            'routes'    => array(),
            'command'   => array(),
            'prefix_cmd' => $ctrl->prefixCmd,
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
            }
        }
        $data->set( 'siteObj', $siteDefault );
    }
    // +-------------------------------------------------------------+
}