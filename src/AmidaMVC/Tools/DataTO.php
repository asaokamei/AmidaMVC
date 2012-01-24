<?php
namespace AmidaMVC\Tools;

/**
 * Data Transfer Object.
 */
class DataTO
{
    /** @var array   contains _data to transfer. */
    var $_data = array();
    // +-------------------------------------------------------------+
    function __construct() {
        $this->_data = array();
    }
    // +-------------------------------------------------------------+
    //  setter and getter
    // +-------------------------------------------------------------+
    /**
     * setter for $name. stores at $this->_data[$name].
     * @param type $name   name of value to set.
     * @param type $value  the value.
     * @return $this.
     */
    function set( $name, $value=NULL ) {
        if( is_array( $name ) ) {
            $this->_data = array_merge( $this->_data, $name );
        }
        else {
            $this->_data[ $name ] = $value;
        }
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * getter for $name. if $name is not given, return all the _data.
     * @param string $name name of value to get.
     * @return mix        returns the named value,
     *                     or all _data if name is not given.
     */
    function get( $name=NULL ) {
        if( $name === NULL ) {
            return $this->_data;
        }
        if( isset( $this->_data[ $name ] ) ) {
            return $this->_data[ $name ];
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
}