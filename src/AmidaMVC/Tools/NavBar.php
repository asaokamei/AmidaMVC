<?php
namespace AmidaMVC\Tools;

class NavBar
{
    protected $max_score = NULL;
    function __construct() {
    }
    /**
     * @param array $menu
     * @param $max_score
     * @return string
     */
    function getMenu( $menu, $max_score=NULL )
    {
        $this->max_score = $max_score;
        $class ='nav nav-pills';
        $html  = $this->_ul( $menu, $class );
        return $html;
    }
    /**
     * @param array $menu
     * @param string $class
     * @return string
     */
    function _ul( $menu, $class ) {
        $html = "<ul class=\"{$class}\">";
        $html .= $this->_li( $menu );
        $html .= '</ul>';
        return $html;
    }
    /**
     * @param array $menu
     * @return string
     */
    function _li( $menu ) {
        $html = '';
        foreach( $menu as $item ) {
            $active = ( $item[ 'score' ] >= $this->max_score ) ? ' active' : '';
            if( isset( $item[ 'pages' ] ) ) {
                $sub = $this->_ul( $item['pages'], 'dropdown-menu' );
                $html .= "
            <li class=\"dropdown{$active}\">
            <a class=\"dropdown-toggle\" data-toggle=\"dropdown\">{$item{'title'}}
                    <b class=\"caret\"></b>
            </a>
            {$sub}
            </li>
            ";
            }
            elseif( isset( $item[ 'divider' ] ) ) {
                $html .= "<li class=\"divider\"></li>";
            }
            else {
                $url = $item[ 'url' ];
                $name = $item['title'];
                $html .= "<li class=\"{$active}\"><a href=\"{$url}\">{$name}</a></li>";
            }
        }
        return $html;
    }
}

