<?php
namespace AmidaMVC\Component;

/**
 * Router class
 * from Perfect PHP.
 */

class Router
{
    // +-------------------------------------------------------------+
    static function actionDefault( $ctrl, &$data ) {
        $loadInfo = \AmidaMVC\Framework\Route::match( $ctrl->path );
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