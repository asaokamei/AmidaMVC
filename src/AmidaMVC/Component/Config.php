<?php
namespace AmidaMVC\Component;
/**
 *  for controlling multiple-lang, admin, debug info,
 *  dev/staging/real server.
 */
class Config
{
    /**
     * @var array  list of available mode. 
     */
    static $mode_list = array( '_logout', '_dev' );
    // +-------------------------------------------------------------+
    /**
     * set up configuration for all mode. 
     * @static
     * @param \AmidaMVC\Framework\Controller $ctrl
     * @param SiteObj $siteObj
     */
    static function actionDefault(
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
    /**
     * set up configuration for _dev mode. 
     * @static
     * @param \AmidaMVC\Framework\Controller $ctrl
     * @param SiteObj $_siteObj
     * @return mixed
     */
    static function action_dev(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj )
    {
        $ctrl->addComponentAfter( 'router', 'Filer', 'filer' );
        return;
    }
    // +-------------------------------------------------------------+
    /**
     * do not initialize _siteObj for second time Config. 
     * @static
     * @param \AmidaMVC\Framework\Controller $ctrl
     * @param SiteObj $_siteObj
     * @return bool
     */
    static function checkInit(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj )
    {
        if( $_siteObj->get( 'siteObj' ) ) {
            return TRUE;
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    /**
     * find mode (_dev or _login, for instance).
     * @static
     * @param $command
     * @return bool
     */
    static function findMode( $command )
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
    /**
     * set up siteObj. 
     * @static
     * @param \AmidaMVC\Framework\Controller $ctrl
     * @param SiteObj $_siteObj
     */
    static function setRoute( 
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