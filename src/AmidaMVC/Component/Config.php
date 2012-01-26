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
    // +-------------------------------------------------------------+
    function actionDefault(
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
        self::setRoute( $siteDefault );
        $ctrl->path_info = $siteDefault[ 'path_info' ];
        $data->set( 'siteObj', $siteDefault );
    }
    // +-------------------------------------------------------------+
    function setRoute( &$siteDefault ) {
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
        $siteDefault[ 'path_info' ] = implode( '/', $siteDefault[ 'routes' ] );
        if( empty( $siteDefault[ 'path_info' ] ) ) {
            $siteDefault[ 'path_info' ] = '/';
        }
    }
    // +-------------------------------------------------------------+
}