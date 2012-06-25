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
        $max_score = $this->_prepMenu( $this->menu );
        $_pageObj->topNav = $this->getMenu( $max_score );
    }

    /**
     * @param int $max_score
     * @return string
     */
    function getMenu( $max_score ) {
        return $this->nav->getMenu( $this->menu, $max_score );
    }
    /**
     * @param array $menu
     * @return int
     */
    function _prepMenu( &$menu ) {
        $max_score = -1;
        foreach( $menu as &$item ) {
            if( isset( $item[ 'url' ] ) ) {
                $item[ 'score' ] = $this->_score( $item['url'] );
                if( $item[ 'score' ] >  $max_score ) $max_score = $item[ 'score' ];
                $item['url'] = $this->_ctrl->getBaseUrl( $item['url'] );
            }
            if( isset( $item[ 'pages' ] ) && is_array( $item[ 'pages' ] ) ) {
                $score = $this->_prepMenu( $item[ 'pages' ] );
                if( $score > $max_score ) $max_score = $score;
                if( $score > $item[ 'score' ] ) $item[ 'score' ] = $score;
            }
        }
        return $max_score;
    }
    /**
     * @param string $url
     * @return int
     */
    function _score( $url ) {
        $pathInfo = $this->_ctrl->getPathInfo();
        $pathLength = strlen( $pathInfo );
        for( $i = 0; $i < $pathLength; $i++ ) {
            if( !isset( $url[$i] ) || $pathInfo[$i] !== $url[$i] ) break;
        }
        $diff = min( 100, strlen( $url ) - $pathLength );
        $score = $i * 100 + 100 - $diff;
        return $score;
    }
}

