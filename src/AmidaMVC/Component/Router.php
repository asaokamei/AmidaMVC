<?php
namespace AmidaMVC\Component;

/**
 * Router class to determine files to load
 * by route map, or scanning file systems.
 */

class Router
{
    static $router = array( '\AmidaMVC\Tools\Route', 'match' );
    // +-------------------------------------------------------------+
    /**
     * default is to use route map first, then scan the file system
     * to determine which file to load.
     * @static
     * @param $ctrl
     * @param $_siteObj
     * @return array|bool|mixed    $loadInfo for Loader.
     */
    static function actionDefault( 
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj $_siteObj ) 
    {
        $loadInfo  = call_user_func( self::$router, $ctrl->getPathInfo() );
        if( $loadInfo ) {
            // found by route map.
            $loadInfo[ 'foundBy' ] = 'route';
        }
        else {
            $loadInfo = self::actionScan( $ctrl, $_siteObj );
            if( $loadInfo ) {
                $loadInfo[ 'foundBy' ] = 'scan';
            }
        }
        if( !$loadInfo ) {
            $ctrl->setAction( '_pageNotFound' );
            $loadInfo = array();
        }
        else {
            // action is as is; probably default.
            $_siteObj->set( 'loadInfo', $loadInfo );
        }
        static::fireRouterResult( $loadInfo );
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /** 
     * search file system for path info.
     * @static
     * @param $ctrl
     * @param $_siteObj
     * @return array|bool   return $loadInfo or FALSE if not found
     */
    static function actionScan( 
        \AmidaMVC\Framework\Controller $ctrl, 
        \AmidaMVC\Component\SiteObj &$_siteObj ) 
    {
        $loadInfo = array(
            'file' => FALSE,
            'action' => $ctrl->defaultAct(),
        );
        // find a file to load. 
        // ex: path_info = path/to/file_name.
        $file_name = $ctrl->getLocation() . '/' . $ctrl->getPathInfo();
        if( file_exists( $file_name ) && !is_dir( $file_name ) ) {
            $loadInfo[ 'file' ] = $ctrl->getPathInfo();
            return $loadInfo;
        }
        // find an app to load.
        // ex: path_info = path/to/_App.php/action.
        $routes = explode( '/', $ctrl->getPathInfo() );
        if( empty( $routes ) ) {
            $routes = array( '' );
        }
        $folder = '';
        $found  = FALSE;
        foreach( $routes as $loc ) {
            if( $found ) {
                // found an _App to load. next loc is the action.
                $loadInfo[ 'action' ] = $loc;
                return $loadInfo;
            }
            $folder .= $loc . '/';
            $file_name = $ctrl->getLocation() . '/' . $folder . '_App.php';
            if( file_exists( $file_name ) ) {
                $loadInfo[ 'file' ] = $folder . '_App.php';
                $found = TRUE;
            }
        }
        if( $found ) {
            return $loadInfo;
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    static function action_LoginForm( 
        \AmidaMVC\Framework\Controller $ctrl, 
        \AmidaMVC\Component\SiteObj &$_siteObj ) {
        // show login form.
        $siteInfo = $_siteObj->get( 'siteObj' );
        if( isset( $siteInfo->loginForm ) ) {
            $loadInfo = array(
                'file' => $siteInfo->loginForm,
                'action' => 'default',
            );
            return $loadInfo;
        }
        return array();
    }
    // +-------------------------------------------------------------+
    static function fireRouterResult( $loadInfo ) {
        // do nothing
    }
    // +-------------------------------------------------------------+
}