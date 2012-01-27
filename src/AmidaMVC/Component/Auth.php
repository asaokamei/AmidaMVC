<?php
namespace AmidaMVC\Component;

class Auth
{
    static $auth_callback = array( '\\AmidaMVC\\Tools\\AuthNot', 'getAuth' );
    // +-------------------------------------------------------------+
    function actionDefault(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$data )
    {
        // default: do nothing...
        Debug::bug( 'wordy', 'Auth default... do nothing. ');
    }
    // +-------------------------------------------------------------+
    function action_dev(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$data )
    {
        // default: do nothing...
        Debug::bug( 'wordy', 'Auth for _dev... require authentification. ');
        $auth_info = call_user_func( static::$auth_callback );
        if( $auth_info ) {
            $data->set( 'authInfo', $auth_info );
        }
        else {
            $ctrl->setAction( '_LoginForm' );
        }
    }
    // +-------------------------------------------------------------+
}