<?php
namespace AmidaMVC\AppSimple;

class Router
{
    static $router  = array( '\AmidaMVC\Tools\Route', 'match' );
    static $scanner = array( '\AmidaMVC\Tools\Route', 'scan' );
    // +-------------------------------------------------------------+
    /**
     * default is to use route map first, then scan the file system
     * to determine which file to load.
     * @static
     * @param \AmidaMVC\AppSimple\Controller $_ctrl
     * @param \AmidaMVC\AppSimple\SiteObj $_siteObj
     * @return array|bool|mixed    $loadInfo for Loader.
     */
    static function actionDefault( $_ctrl, $_siteObj )
    {
        $loadInfo  = call_user_func( self::$router, $_ctrl->getPathInfo() );
        if( $loadInfo ) {
            // found by route map.
            $loadInfo[ 'foundBy' ] = 'route';
        }
        else {
            $loadInfo = call_user_func( self::$router, $_ctrl->getBaseUrl(), $_ctrl->getPathInfo() );
            if( $loadInfo ) {
                $loadInfo[ 'foundBy' ] = 'scan';
            }
        }
        if( !$loadInfo ) {
            $_ctrl->setAction( '_pageNotFound' );
            $loadInfo = array();
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
}