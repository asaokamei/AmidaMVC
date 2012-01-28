<?php
/**
 * TO-Do application for AmidaMVC Demonstration. 
 */

$_ctrl->prependComponent( array(
    array( 'appTodo',  'app' ),
    array( 'viewTodo', 'view' ),
));


class appTodo extends AmidaMVC\Component\Model
{
    static $todo_file = '';
    static $id_max = 0;
    // +-------------------------------------------------------------+
    static function _init() {
        static::$todo_file = __DIR__ . '/_todo/_todo.csv';
        if( !is_dir( dirname( static::$todo_file ) ) ) {
            mkdir( dirname( static::$todo_file ), 0777 );
        }
        if( !file_exists( static::$todo_file ) ) {
            $todo = "1,9,\"run todo demo.\"\n2,1,\"finish the first todo.\"";
            file_put_contents( static::$todo_file, $todo );
        }
    }
    // +-------------------------------------------------------------+
    static function putTodo( $todo ) {
        $data = '';
        foreach( $todo as $do ) {
            $line = '"' . implode( '","', $do ) . '"';
            $data .= $line . "\n";
        }
        file_put_contents( static::$todo_file, $data );
    }
    // +-------------------------------------------------------------+
    static function getTodo( $raw=FALSE ) {
        $list = trim( file_get_contents( static::$todo_file ) );
        if( $raw ) {
            return $list;
        }
        if( !$list ) return array();
        $list = explode( "\n", $list );
        $todo = array();
        $id_max = 0;
        foreach( $list as $line ) {
            $do = explode( ",", trim( $line ) );
            foreach( $do as &$col ) {
                if( substr( $col, 0, 1 ) == '"' && substr( $col, -1 ) == '"' ) {
                    $col = substr( $col, 1, -1 );
                }
            }
            if( $do[0] > $id_max ) {
                $id_max = $do[0];
            }
            $todo[] = $do;
        }
        static::$id_max = $id_max;
        return $todo;
    }
    // +-------------------------------------------------------------+
    // ACTIONS
    // +-------------------------------------------------------------+
    static function actionDefault(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$siteObj  )
    {

        // default is page not found error. 
        $ctrl->setAction( '_pageNotFound' );
    }
    // +-------------------------------------------------------------+
    static function actionList(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$siteObj  ) 
    {
        self::_init();
        $todo = self::getTodo();
        return $todo;
    }
    // +-------------------------------------------------------------+
    static function actionAdd(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$siteObj  ) 
    {
        self::_init();
        // add new to do item.
        $what = addslashes( $_POST[ 'what'] );
        if( $what ) {
            $todo = self::getTodo();
            $new  = array( static::$id_max+1, 1, $what );
            $todo[] = $new;
            self::putTodo( $todo );
        }
        $ctrl->redirect( '/todo/list' );
    }
    // +-------------------------------------------------------------+
    static function actionReset(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$siteObj  )
    {
        self::_init();
        if( file_exists( static::$todo_file ) ) {
            unlink( static::$todo_file );
        }
        self::_init();
        $ctrl->redirect( '/todo/list' );
    }
    // +-------------------------------------------------------------+
    static function actionToggle(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$siteObj  )
    {
        self::_init();
        $loadInfo = $siteObj->get( 'loadInfo' );
        $id = $loadInfo[ 'id' ];
        if( $id ) {
            $todo = self::getTodo();
            foreach( $todo as &$do ) {
                if( $do[0] == $id ) {
                    if( $do[1] == '1' ) { // active -> done
                        $do[1] = '9';
                    }
                    else {                // done -> active
                        $do[1] = '1';
                    }
                }
            }
            self::putTodo( $todo );
        }
        $ctrl->redirect( '/todo/list' );
    }
    // +-------------------------------------------------------------+
}

class ViewTodo extends \AmidaMVC\Component\View
{
    // +-------------------------------------------------------------+
    // VIEWS
    // +-------------------------------------------------------------+
    static function actionList(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$siteObj,
        $todo )
    {
        // view for list
        $todoTable = self::viewTodoTable( $todo );
        $addField  = self::viewNewField();
        $html  = "
          <title>ToDo List</title>
          <h2>add new todo</h2>
          {$addField}
          <h2>To Do List</h2>
          {$todoTable}
          <p>&nbsp;</p>
          <p style='text-align: right;'><a href='reset'>reset</a></p>
        ";
        $siteObj->setContents( $html );
    }
    // +-------------------------------------------------------------+
    static function viewNewField() {
        $addField = '<input type="text" name="what" size="40" />';
        $html = "
          <form name='addTodo' method='post' action='add'>
          {$addField}
          <input type='submit' name='submit' value='add todo' />
          </form>";
        return $html;
    }
    // +-------------------------------------------------------------+
    static function viewTodoTable( $todo ) {
        if( empty( $todo ) ) {
            $html = "<p>Congratulations<br />there's nothing to do!</p>";
        }
        else {
            $html = '';
            foreach( $todo as $do ) {
                $id   = $do[0];
                $done = ( $do[1] === "1" ) ? 'not yet' : 'done';
                $what = $do[2];
                $html .= "
                <tr>
                  <td>$id</td>
                  <td>{$done}</td>
                  <td>{$what}</td>
                  <td><a href='toggle/{$id}'>done</a></td>
                </tr>
                ";
            }
            $html = "<table><thead><tr>
            <th>#</th><th>status</th>
            <th>what to do...</th>
            <th>finished</th>
            </tr></thead>{$html}
            </table>";
        }
        return $html;
    }
    // +-------------------------------------------------------------+
}

