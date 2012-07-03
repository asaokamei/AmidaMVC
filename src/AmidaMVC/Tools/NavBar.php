<?php
namespace AmidaMVC\Tools;

class NavBar
{
    protected $menu = array();
    protected $max_score = NULL;
    protected $tabs = array(
        'topUl' => '<ul class="nav nav-tabs">',
        'subUl' => '<ul class="dropdown-menu">',
        'divider' => '<li class="divider"></li>',
        'endUl' => '</ul>',
        'liItem' => '<li class="%3$s"><a href="%2$s">%1$s</a></li>',
        'liSub' => '<li class="dropdown%3$s">
            <a class="dropdown-toggle" data-toggle="dropdown">%1$s
                    <b class="caret"></b>
            </a>
            %2$s
            </li>',
        'icon' => '<i class="icon-%s"></i> ',
    );
    protected $pill = array();
    protected $tags = array();

    function __construct() {
        $this->pill = $this->tabs;
        $this->pill[ 'topUl' ] = '<ul class="nav nav-pills">';
        $this->tags = $this->tabs;
    }
    /**
     * @param array $menu
     * @param $max_score
     * @return string
     */
    function setMenu( $menu, $max_score=NULL ) {
        $this->menu      = $menu;
        $this->max_score = $max_score;
    }

    /**
     * @param string $name
     */
    function setTags( $name ) {
        if( isset( $name ) && isset( $this->$name ) && is_array( $this->$name ) ) {
            $this->tags = & $this->$name;
        }
    }

    /**
     * @param null|string $name
     * @return string
     */
    function draw( $name=NULL ) {
        $this->setTags( $name );
        $html  = $this->_ul( $this->menu );
        return $html;
    }

    /**
     * @param array $menu
     * @param null $max_score
     * @return string
     */
    function getMenu( $menu, $max_score=NULL )
    {
        $this->max_score = $max_score;
        $html  = $this->_ul( $menu );
        return $html;
    }

    /**
     * @param string $name
     * @return mixed|string
     */
    function _text( $name ) {
        $text = '';
        if( isset( $this->tags[ $name ] ) ) {
            $text = $this->tags[ $name ];
            if( func_num_args() > 1 ) {
                $args = func_get_args();
                $args[0] = $text;
                $text = call_user_func_array( 'sprintf', $args );
            }
        }
        return $text;
    }

    /**
     * @param array $menu
     * @param string $ulType
     * @return string
     */
    function _ul( $menu, $ulType='topUl' ) {
        $html = $this->_text( $ulType );
        $html .= $this->_li( $menu );
        $html .= $this->_text( 'endUl' );
        return $html;
    }
    /**
     * @param array $menu
     * @return string
     */
    function _li( $menu ) {
        $html = '';
        foreach( $menu as $item ) {
            $url   = ( isset( $item[ 'url' ] ) ) ? $item[ 'url' ] : '';
            $title = ( isset( $item[ 'title' ] ) ) ? $item[ 'title' ] : '';
            $score = ( isset( $item[ 'score' ] ) ) ? $item[ 'score' ] : 0;
            $active = ( $score >= $this->max_score ) ? ' active' : '';
            if( isset( $item[ 'icon' ] ) ) $title = $this->_text( 'icon', $item['icon'] ) . $title;
            if( isset( $item[ 'pages' ] ) ) {
                $sub = $this->_ul( $item['pages'], 'subUl' );
                $html .= $this->_text( 'liSub', $title, $sub, $active );
            }
            elseif( isset( $item[ 'divider' ] ) ) {
                $html .= $this->_text( 'divider' );
            }
            else {
                $html .= $this->_text( 'liItem', $title, $url, $active );
            }
        }
        return $html;
    }
}

