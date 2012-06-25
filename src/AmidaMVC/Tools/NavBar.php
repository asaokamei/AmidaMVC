<?php
namespace AmidaMVC\Tools;

class NavBar
{
    function __construct() {
    }
    function getMenu( $menu )
    {
        $class ='nav nav-pills';
        $html  = $this->_ul( $menu, $class );
        return $html;
    }
    function _ul( $menu, $class ) {
        $html = "<ul class=\"{$class}\">";
        $html .= $this->_li( $menu );
        $html .= '</ul>';
        return $html;
    }
    function _li( $menu ) {
        $html = '';
        foreach( $menu as $item ) {
            if( isset( $item[ 'pages' ] ) ) {
                $sub = $this->_ul( $item['pages'], 'dropdown-menu' );
                $html .= "
            <li class=\"dropdown\">
            <a class=\"dropdown-toggle\" data-toggle=\"dropdown\">{$item{'title'}}
                    <b class=\"caret\"></b>
            </a>
            {$sub}
            </li>
            ";
            }
            else {
                $url = $item[ 'url' ];
                $name = $item['title'];
                $html .= "<li><a href=\"{$url}\">{$name}</a></li>";
            }
        }
        return $html;
    }
}

