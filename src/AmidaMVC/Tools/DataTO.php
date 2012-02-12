<?php
namespace AmidaMVC\Tools;

/**
 * Data Transfer Object.
 * @property string file_mode
 * @property string src_type
 * @property string file_cmd
 * @property mixed curr_folder
 * @property array backup_list
 */
class DataTO
{
    // +-------------------------------------------------------------+
    function __construct( $data=NULL ) {
        if( isset( $data ) ) {
            $this->set( $data );
        }
    }
    // +-------------------------------------------------------------+
    function getInstance() {
        $class = get_called_class();
        return new $class;
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
        if( isset( $value ) ) {
            if( is_array( $value ) ) {
                $value = new DataTO( $value );
            }
            $this->$name = $value;
        }
        else if( is_array( $name ) ) {
            foreach( $name as $k=>$v ) {
                $this->$k = $v;
            }
        }
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * finds property $name from $this, or from $src if given. 
     * @param $name       name of property to find. 
     * @param null $src   source to look at. 
     * @return null       found data.
     */
    function _find( $name, $src=NULL ) {
        $value = NULL;
        if( !isset( $src ) ) $src = $this;
        if( isset( $src->$name ) ) {
            $value = $src->$name;
        }
        return $value;
    }
    // +-------------------------------------------------------------+
    /**
     * getter for $name. if $name is not given, return all the _data.
     * @param string $name    name of value to get.
     * @param null $name2     name of value in sub-data.
     * @return mix            returns the named value,
     *                        or all _data if name is not given.
     */
    function get( $name, $name2=NULL ) {
        if( $name === NULL ) {
            return $this;
        }
        $value = $this->_find( $name );
        if( isset( $name2 ) ) {
            $value = $this->_find( $name2, $value );
        }
        return $value;
    }
    // +-------------------------------------------------------------+
}