<?php
namespace AmidaMVC\Framework;

abstract class AModule
{
    var $commands = array();

    function findCommand( $commandList ) {
        if( empty( $this->commands ) ) return FALSE;
        foreach( $this->commands as $command ) {
            if( in_array( $command, $commandList ) ) {
                return $command;
            }
        }
        return FALSE;
    }
}