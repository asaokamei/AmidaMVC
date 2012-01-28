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
    static function actionPost(
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
    static function actionDetail(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$siteObj  )
    {
        self::_init();
        $loadInfo = $siteObj->get( 'loadInfo' );
        $id = $loadInfo[ 'id' ];
        if( $id ) {
            $todo = self::getTodo();
            foreach( $todo as $do ) {
                if( $do[0] == $id ) {
                    break;
                }
            }
        }
        return $do;
    }
    // +-------------------------------------------------------------+
    static function actionPut(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$siteObj  )
    {
        self::_init();
        $loadInfo = $siteObj->get( 'loadInfo' );
        $id = $loadInfo[ 'id' ];
        $what = addslashes( $_POST[ 'what'] );
        if( $id && $what ) {
            $todo = self::getTodo();
            foreach( $todo as &$do ) {
                if( $do[0] == $id ) {
                    $do[2] = $what;
                }
            }
            self::putTodo( $todo );
        }
        $ctrl->redirect( '/todo/list' );
    }
    // +-------------------------------------------------------------+
}

class viewTodo extends \AmidaMVC\Component\View
{
    static $title = "<title>ToDo List Application</title>\n";
    // +-------------------------------------------------------------+
    // VIEWS
    // +-------------------------------------------------------------+
    static function actionList(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$siteObj,
        $todo )
    {
        // view for list
        $todoTable = self::makeTodoTable( $todo );
        $addField  = self::makeNewField();
        $html  = "
          <h2>add new todo</h2>
          {$addField}
          <h2>To Do List</h2>
          {$todoTable}
          <p>&nbsp;</p>
          <p style='text-align: right;'>[ <a href='reset'>reset todo list</a> ]</p>
        ";
        $html = self::makeTodoContents( $html );
        $siteObj->setContents( $html );
    }
    // +-------------------------------------------------------------+
    static function makeNewField() {
        $addField = '<input type="text" name="what" size="40" />';
        $html = "
          <form name='addTodo' method='post' action='post'>
          {$addField}
          <input type='submit' name='submit' value='add todo' />
          </form>";
        return $html;
    }
    // +-------------------------------------------------------------+
    static function makeTodoTable( $todo ) {
        if( empty( $todo ) ) {
            $html = "<p>Congratulations<br />there's nothing to do!</p>";
        }
        else {
            $html = '';
            foreach( $todo as $do ) {
                $id   = $do[0];
                $done = ( $do[1] === "1" ) ? 'close' : 'reopen';
                $what = $do[2];
                $html .= "
                <tr>
                  <td align=\"right\">$id</td>
                  <td><span class=\"{$done}\"><a href='detail/{$id}'>{$what}</a></span></td>
                  <td align=\"center\">[<a href='toggle/{$id}'>{$done}</a>]</td>
                </tr>
                ";
            }
            $html = "<table width=\"80%\"><thead><tr>
            <th width=\"5%\">#</th>
            <th width=\"80%\">what to do...</th>
            <th width=\"15%\">toggle</th>
            </tr></thead>{$html}
            </table>";
        }
        return $html;
    }
    // +-------------------------------------------------------------+
    static function actionDetail(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$siteObj,
        $todo )
    {
        //
        $id   = $todo[0];
        $done = ( $todo[1] === "1" ) ? 'not yet' : 'done';
        $what = $todo[2];
        $target = $ctrl->getBaseUrl() .  "todo/put/$id";
        $html = "
          <h2>Modify To Do</h2>
          <form name=\"todoDetail\" method=\"post\" action=\"{$target}\">
          <input type=\"text\" name=\"what\" size=\"40\" value=\"{$what}\" />
          <input type='submit' name='submit' value='modify todo' />
          </form>
        ";
        $html = self::makeTodoContents( $html );
        $siteObj->setContents( $html );
    }
    // +-------------------------------------------------------------+
    static function makeTodoContents( $html ) {
        $content = "
        <style>
        div#todoApp .reopen {
          color:#888888;
          text-decoration: line-through;
        }
        div#todoApp a:link {
          color:#000060;
          text-decoration: none;
        }
        div#todoApp a:visited {
          color:#000060;
        }
        div#todoApp a:hover {
          color:#3333CC;
        }
        </style>
        <div id=\"todoApp\">
        <title>ToDo List Application</title>\n
        {$html}
        </div>
        ";
        return $content;
    }
    // +-------------------------------------------------------------+
}

