<?php
namespace AmidaMVC\Module;

class Auth extends Config
{
    /** @var \AmidaMVC\Tools\i18n */
    protected $i18n;

    /**
     * @param \AmidaMVC\Tools\i18n $i18n
     */
    public function injectI18n( $i18n ) {
        $this->i18n = $i18n;
    }
    // +-------------------------------------------------------------+
    /**
     * @param $message
     */
    function logout( $message ) {
        $this->_auth->logout();
    }
    function drawLogin() {
        $this->i18n->textSection( '_template' );
        $section = array(
            'title' => $this->i18n->text( 'admin_mode' ),
            'type'  => 'list',
            'lists' => array(),
        );
        $section[ 'lists' ][] = array(
            $this->i18n->text('login'),
            $this->_ctrl->getBaseUrl( '/dev_login' ),
        );
        $this->_pageObj->section->set( 'auth', $section );
    }
    function drawLogout() {
        $this->i18n->textSection( '_template' );
        $section = array(
            'title' => $this->i18n->text( 'admin_mode' ),
            'type'  => 'list',
            'lists' => array(),
        );
        if( isset( $this->auth_success[ 'auth_name' ] ) ) {
            $section[ 'lists' ][] = array(
                $this->i18n->text( 'auth_name' ) . ':' . $this->auth_success[ 'auth_name' ]
            );
        }
        if( isset( $this->auth_success[ 'name' ] ) ) {
            $section[ 'lists' ][] = array(
                $this->i18n->text( 'user_name' ) . ':' . $this->auth_success[ 'name' ]
            );
        }
        $section[ 'lists' ][] = array(
            $this->i18n->text('logout'),
            $this->_ctrl->getBaseUrl( '/dev_logout' ),
        );
        $this->_pageObj->section->set( 'auth', $section );
    }
    // +-------------------------------------------------------------+
}