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
    function actionDefault( \AmidaMVC\Framework\Controller $ctrl, &$data ) {
        $siteDefault = array(
            'base_url'  => $ctrl->base_url,
            'path_info' => $ctrl->path,
            'routes'    => $ctrl->routes,
            'command'   => $ctrl->command,
            'prefix_cmd' => '_',
        );
        $data->set( 'siteObj', $siteDefault );
    }
    // +-------------------------------------------------------------+
    function getRoute() {
        $paths = explode( '/', $this->path );
        $this->command = array();
        $this->routes = array();
        foreach( $paths as $cmd ) {
            if( empty( $cmd ) ) continue;
            if( $cmd === '..' ) continue;
            if( substr( $cmd, 0, 1 ) === $this->prefixCmd ) {
                $this->command[] = $cmd;
            }
            else {
                $this->routes[] = $cmd;
            }
        }
        $this->path_info = implode( '/', $this->routes );
        if( empty( $this->path_info ) ) {
            $this->path_info = '/';
        }
    }
    // +-------------------------------------------------------------+
}