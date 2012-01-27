<?php
namespace AmidaMVC\Component;
/**
 * Authentication component class.  
 */

class Auth
{
    static $auth_callback = array( '\\AmidaMVC\\Tools\\AuthNot', 'getAuth' );
    static $logout_callback = array( '\\AmidaMVC\\Tools\\AuthNot', 'logout' );
    // +-------------------------------------------------------------+
    function actionDefault(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$data )
    {
        // default: do nothing...
        Debug::bug( 'wordy', 'Auth default... do nothing. ');
    }
    // +-------------------------------------------------------------+
    function action_logout(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$data )
    {
        // ok logout from 
        call_user_func( static::$logout_callback );
        $ctrl->mode = '';
        $ctrl->redirect();
    }
    // +-------------------------------------------------------------+
    function action_dev(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$data )
    {
        Debug::bug( 'wordy', 'Auth for _dev... require authentification. ');
        $auth_info = call_user_func( static::$auth_callback );
        if( $auth_info ) {
            // authentication OK. 
            $data->set( 'authInfo', $auth_info );
            if( $auth_info[ 'auth_method'] == 'post' ) {
                // just logged in. redirect to the top.
                $ctrl->redirect( $ctrl->path_info );  
            }
        }
        else {
            $ctrl->setAction( '_LoginForm' );
        }
    }
    // +-------------------------------------------------------------+
}