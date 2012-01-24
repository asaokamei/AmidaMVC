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
}