<?php
namespace AmidaMVC\Tools;

class Section
{
    /** @var array */
    var $sections = array();
    /** @var array */
    var $footers  = array();
    /** @var array */
    var $sideBars = array();
    /** @var \AmidaMVC\Tools\i18n */
    var $i18n = NULL;
    // +-------------------------------------------------------------+
    /**
     * @param array $config
     */
    public function __construct( $config=array() ) {
        if( isset( $config[ 'footers'] ) ) {
            $this->footers = $config[ 'footers' ];
        }
        if( isset( $config[ 'sideBars'] ) ) {
            $this->sideBars = $config[ 'sideBars' ];
        }
    }

    /**
     * @param $i18n  \AmidaMVC\Tools\i18n
     */
    public function injectI18n( $i18n ) {
        $this->i18n = $i18n;
    }
    /**
     * @param array $option
     * @return mixed
     */
    public function _init( $option = array() ) {
    }
    // +-------------------------------------------------------------+
    /**
     * @param string $name
     * @param array $data
     */
    public function set( $name, $data ) {
        $this->sections[ $name ] = $data;
    }
    /**
     * @param string $name
     * @return bool
     */
    public function draw( $name ) {
        if( !isset( $this->sections[ $name ] ) ) {
            return FALSE;
        }
        $data = $this->sections[ $name ];
        $type = $data[ 'type' ];
        $method = 'draw' . ucwords( $type );
        if( !method_exists( $this, $method ) ) {
            return FALSE;
        }
        $html = $this->$method( $data );
        return $html;
    }
    /**
     * @param array $data
     * @return string
     */
    public function drawList( $data ) {
        $html  = '<div class="sectionBox">';
        $html .= "<h3>{$data{'title'}}</h3>";
        foreach( $data[ 'lists' ] as $link ) {
            if( isset( $link[0] ) && isset( $link[1] ) ) {
                $html .= "<p>&nbsp;[ <a href=\"" . $link{1} . "\">{$link{0}}</a> ]</p>";
            }
            elseif( isset( $link[0] ) ) {
                $html .= "<p>{$link{0}}</p>";
            }
        }
        $html .= "</div>";
        return $html;
    }
}