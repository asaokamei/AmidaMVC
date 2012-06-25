<?php
namespace AmidaMVC\Module;

class Menus
{
    /** @var \AmidaMVC\Framework\Controller */
    protected $_ctrl;
    /** @var \AmidaMVC\Framework\PageObj */
    protected $_pageObj;
    /** @var \AmidaMVC\Tools\NavBar */
    protected $nav = NULL;
    /** @var array */
    protected $menu = array();
    /**
     * @param array $config
     */
    function __construct( $config=array() ) {
        if( isset( $config[ 'menu' ] ) ) {
            $this->menu = $config[ 'menu' ];
        }
    }
    /**
     * @param $nav
     */
    function injectNav( $nav ) {
        $this->nav = $nav;
    }
    /**
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     */
    function actionDefault( $_ctrl, $_pageObj ) {
        $this->_ctrl    = $_ctrl;
        $this->_pageObj = $_pageObj;
        $this->_prepMenu( $this->menu );
        /** @var topNav string */
        $_pageObj->topNav = $this->getMenu();
    }
    /**
     * @return string
     */
    function getMenu() {
        return $this->nav->getMenu( $this->menu );
    }
    /**
     * @param array $menu
     */
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

