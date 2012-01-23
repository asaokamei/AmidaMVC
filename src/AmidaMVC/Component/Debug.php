<?php
namespace AmidaMVC\Component;

class Debug
{
    const HTML_TYPE = 'DebugHtml';
    const MARK_TYPE = 'DebugMD';

    static $self;

    var $outputTo   = FALSE;
    var $formatType = self::HTML_TYPE;
    var $debugLevel = 4; // debug level.
    var $result = '';
    // +-------------------------------------------------------------+
    //  to use Debug as static class like a singleton.
    // +-------------------------------------------------------------+
    /**
     * initializes static self behave a bit like a singleton.
     * ...not so good...
     * @static
     * @return mixed
     */
    static function _init() {
        if( !isset( static::$self) ) {
            static::$self = new Debug;
        }
        return static::$self;
    }
    // +-------------------------------------------------------------+
    /**
     * a generic function to call static::$self.
     * @static
     * @return bool
     */
    static function bug() {
        if( !static::$self ) return FALSE;
        $args = func_get_args();
        $method = $args[0];
        $args = array_slice( $args, 1 );
        $traces = debug_backtrace(false);
        static::$self->getTrace( $traces[0] );
        if( empty( $args ) ) {
            call_user_func( array( static::$self, $method ) );
        }
        else {
            call_user_func_array( array( static::$self, $method ), $args );
        }
        return TRUE;
    }
    // +-------------------------------------------------------------+
    static function result() {
        if( !static::$self ) return FALSE;
        return static::$self->getResult();
    }
    // +-------------------------------------------------------------+
    //  tools.
    // +-------------------------------------------------------------+
    function output( $text ) {
        if( $this->outputTo ) {
            echo $text;
        }
        else {
            $this->result .= $text;
        }
    }
    // +-------------------------------------------------------------+
    function getResult() {
        return $this->result;
    }
    // +-------------------------------------------------------------+
    function getTrace( $trace ) {
        //$this->arr( $trace );
        $info = "<div class='debugTraceInfo'>[<a title='Trace:{$trace['file']}:{$trace['line']}'>+</a>]</div>";
        $this->output( $info );
    }
    // +-------------------------------------------------------------+
    //  basic echo functions.
    // +-------------------------------------------------------------+
    function format() {
        if( $this->formatType == self::HTML_TYPE ) {
            $args = func_get_args();
            $method = $args[0];
            $args = array_slice( $args, 1 );
            return call_user_func_array( array( '\AmidaMVC\Component\DebugHtml', $method ), $args );
        }
        return '';
    }
    // +-------------------------------------------------------------+
    function echoHead( $level, $string ) {
        if( $this->debugLevel < $level ) return;
        $string = $this->format( 'head', $level, $string );
        $this->output( $string );
    }
    // +-------------------------------------------------------------+
    function echoPara( $level, $string ) {
        if( $this->debugLevel < $level ) return;
        $string = $this->format( 'para', $level, $string );
        $this->output( $string );
    }
    // +-------------------------------------------------------------+
    function echoText( $level, $string ) {
        if( $this->debugLevel < $level ) return;
        $string = $this->format( 'text', $level, $string );
        $this->output( $string );
    }
    // +-------------------------------------------------------------+
    function echoArray( $level, $mix, $title=NULL ) {
        if( $this->debugLevel < $level ) return;
        if( $title!== NULL ) $this->echoText( $level, $title );
        $string = $this->format( 'table', $level, $mix );
        $this->output( $string );
    }
    // +-------------------------------------------------------------+
    //  w* series outputs wordy; for debug framework.
    // +-------------------------------------------------------------+
    function head( $string ) {
        $this->echoHead( 3, $string );
    }
    // +-------------------------------------------------------------+
    function wordy( $string ) {
        $this->echoText( 5, $string );
    }
    // +-------------------------------------------------------------+
    //  t* series output array; for debug framework.
    // +-------------------------------------------------------------+
    function table( $mix, $title=NULL ) {
        $this->echoArray( 3, $mix, $title );
    }
    // +-------------------------------------------------------------+
}

class DebugHtml
{
    // +-------------------------------------------------------------+
    //  basic echo functions.
    // +-------------------------------------------------------------+
    static function head( $level, $string ) {
        $string = "<h{$level}>{$string}</h{$level}>";
        return $string;
    }
    // +-------------------------------------------------------------+
    static function para( $level, $string ) {
        $string = "<p>{$string}</p>";
        return $string;
    }
    // +-------------------------------------------------------------+
    static function text( $level, $string ) {
        $string = "{$string}<br/>";
        return $string;
    }
    // +-------------------------------------------------------------+
    //  compact var_dump.
    // +-------------------------------------------------------------+
    static function table( $level, $var, $showType=true ) {
        $type = gettype( $var );
        $result = '';
        if( in_array( $type, array( 'object', 'array' ) ) ) {
            if( $type == 'object' ) {
                $var = get_object_vars( $var );
            }
            $res_h = "";
            $res_t = "";
            $res_d = "";
            foreach( $var as $k => $v ) {
                $res = self::table( $level, $v, false );
                $res_h .= "<th>{$k}</th>";
                $res_t .= "<td>" . gettype($v) . "</td>";
                $res_d .= "<td>{$res}</td>";
            }
            $result .= "<table><thead>
               <tr><th>key:</th>{$res_h}</tr>
               </thead><tbody>
               <tr><th>type:</th>{$res_t}</tr>
               <tr><th>value:</th>{$res_d}</tr>
               </tbody></table>";
            return $result;
        }
        else {
            if( $var === NULL ) {
                $var = 'NULL';
            }
            else if( $var === FALSE ) {
                $var = 'FALSE';
            }
            else if( $var === TRUE ) {
                $var = 'TRUE';
            }
            if( $showType ) {
                $result .= '<table><tr><th>type:</th><td>' . gettype( $var ) . '</td><th>value:</th><td>'.$var.'</td></tr></table>';
            }
            else {
                $result .= $var;
            }
        }
        return $result;
    }
    // +-------------------------------------------------------------+
}
