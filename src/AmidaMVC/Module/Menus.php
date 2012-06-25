<?php
namespace AmidaMVC\Module;

class Menus
{
    protected $_ctrl;
    protected $_pageObj;
    protected $nav = NULL;
    protected $menu = array();
    function injectNav( $nav ) {
        $this->nav = $nav;
    }
    function __construct( $config=array() ) {
        if( isset( $config[ 'menu' ] ) ) {
            $this->menu = $config[ 'menu' ];
        }
    }
    function actionDefault( $_ctrl, $_pageObj ) {
        $this->_ctrl    = $_ctrl;
        $this->_pageObj = $_pageObj;
        $this->_prepMenu( $this->menu );
        $_pageObj->topNav = $this->getMenu();
    }
    function getMenu() {
        return $this->nav->getMenu( $this->menu );
    }
    function _prepMenu( &$menu ) {
        foreach( $menu as &$item ) {
            if( isset( $item[ 'url' ] ) ) {
                $item['url'] = $this->_ctrl->getBaseUrl( $item['url'] );
            }
            if( isset( $item[ 'pages' ] ) && is_array( $item[ 'pages' ] ) ) {
                $this->_prepMenu( $item[ 'pages' ] );
            }
        }
    }
}

