<?php
namespace AmidaMVC\Component;

/**
 * Router class
 * from Perfect PHP.
 */

class Router
{
    static $router = array( '\AmidaMVC\Framework\Route', 'match' );
    // +-------------------------------------------------------------+
    static function actionDefault( $ctrl ) {
        $loadInfo = call_user_func( self::$router, $ctrl->path );
        if( !$loadInfo ) {
            \AmidaMVC\Component\Debug::bug( 'wordy', 'Router::not matched:'.$ctrl->path );
        }
        else {
            \AmidaMVC\Component\Debug::bug( 'table', $loadInfo, 'Router::path matched:'.$ctrl->path );
            $ctrl->nextModel( 'Load' );
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
}