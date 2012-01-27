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
     * @param $data
     * @return array|bool|mixed    $loadInfo for Loader.
     */
    static function actionDefault( \AmidaMVC\Framework\Controller $ctrl, $data ) {
        $loadInfo = call_user_func( self::$router, $ctrl->path_info );
        if( !$loadInfo ) {
            $loadInfo = self::actionScan( $ctrl, $data );
        }
        if( !$loadInfo ) {
            $ctrl->setAction( '_pageNotFound' );
        }
        else {
            // action is as is; probably default.
        }
        static::fireRouterResult( $loadInfo );
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /** search file system for routes.
     * @static
     * @param $ctrl
     * @param $data
     * @return array|bool   return $loadInfo or FALSE if not found
     */
    static function actionScan( \AmidaMVC\Framework\Controller $ctrl, &$data ) {
        // search routes.
        $folder = $ctrl->getLocation();
        $routes = explode( '/', $ctrl->path_info );
        $loadInfo = self::searchRoutes( $ctrl, $routes, $folder );
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    static function action_LoginForm( 
        \AmidaMVC\Framework\Controller $ctrl, 
        \AmidaMVC\Component\SiteObj &$data ) {
        // show login form.
        $siteInfo = $data->get( 'siteObj' );
        if( isset( $siteInfo[ 'loginForm' ] ) ) {
            $loadInfo = array(
                'file' => $siteInfo[ 'loginForm' ],
                'action' => 'default',
            );
            return $loadInfo;
        }
    }
    // +-------------------------------------------------------------+
    /** search for a file in a $folder.
     * @static
     * @param $ctrl
     * @param $routes
     * @param $folder        folder to search.
     * @return array|bool    return $loadInfo or FALSE if not found.
     */
    static function searchRoutes( $ctrl, &$routes, &$folder ) {
        // loads from existing app file.
        if( is_array( $routes ) && isset( $routes[0] ) ) {
            // search folder, action.php, or _App.php
            $action  = $routes[0];
        }
        else {
            // search _App.php only.
            $action = FALSE;
        }
        $loadInfo = array(
            'file' => FALSE,
            'action' => $action
        );
        // search in subsequent action folder.
        if( $action && is_dir( $folder . "/{$action}" ) ) {
            // search in the directory.
            if( !empty( $routes ) ) {
                $routes = array_slice( $routes, 1 ); // shorten routes.
            }
            $folder .= "/{$action}";
            return self::searchRoutes( $ctrl, $routes, $folder );
        }
        // search for action file.
        if( $action && $file_name = self::getActionFiles( $folder, $action ) ) {
            $routes = array_slice( $routes, 1 );
            $loadInfo[ 'file' ] = "{$folder}/{$file_name}";
            return $loadInfo;
        }
        // search for _App.php
        if( $file_name = self::getActionFiles( $folder, '_App.php' ) ) {
            $action = $routes[0];
            $loadInfo[ 'file' ] = "{$folder}/{$file_name}";
            $loadInfo[ 'action' ] = $action;
            return $loadInfo;
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    static function getActionFiles( $folder, $action ) {
        $ext  = pathinfo( $action, PATHINFO_EXTENSION );
        $base = pathinfo( $action, PATHINFO_FILENAME );
        $find = "{$folder}/{$base}*.{$ext}";
        $list = glob( $find, GLOB_NOSORT );
        foreach( $list as $file_name ) {
            $file_name = basename( $file_name );
            return $file_name;
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    static function fireRouterResult( $loadInfo ) {
        // do nothing
    }
    // +-------------------------------------------------------------+
}