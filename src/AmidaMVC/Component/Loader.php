<?php
namespace AmidaMVC\Component;

/**
 * dumb/simple application loader.
 * TODO: Loader needs various loading method.
 *  app.php: load php and prepend model. not sure if
 *           loader/app.php to prepend...
 *  action.php: include php and intercept output by ob.
 *  action.html: read html as a contents.
 *  action is a file: output as is and set new mime type.
 */
class Loader
{
    static $location = NULL;
    static $postfix  = NULL;
    static $prefix   = '';
    // +-------------------------------------------------------------+
    static function _init() {
    }
    // +-------------------------------------------------------------+
    static function setLocation( $location ) {
        static::$location = $location;
        return static::$location;
    }
    // +-------------------------------------------------------------+
    /**
     * searches for action files under controller folder.
     * must be slow.
     * @static
     * @param $ctrl
     * @param $data
     * @param null $loadInfo
     */
    static function actionDefault( $ctrl, &$data, $loadInfo=NULL ) {
        // check if loadInfo contains file to load.
        if( is_array( $loadInfo ) && isset( $loadInfo[ 'file' ] ) ) {
            self::actionLoad( $ctrl, $data, $loadInfo );
        }
        // load by searching routes.
        self::setLocation( $ctrl->getLocation() );
        static::$prefix = $ctrl->prefixCmd;
        $folder = '';
        $routes = $ctrl->routes;
        $loadInfo = self::searchRoutes( $ctrl, $routes, $folder );
        if( $loadInfo ) {
            self::actionLoad( $ctrl, $data, $loadInfo );
        }
        else {
            $ctrl->nextModel( 'pageNotFound' );
        }
    }
    // +-------------------------------------------------------------+
    /**
     * loads app file.
     * specify file as $loadInfo[ 'file' ] relative to ctrl_root.
     * @static
     * @param $ctrl
     * @param $data
     * @param $loadInfo
     */
    static function actionLoad( $ctrl, &$data, $loadInfo ) {
        $_file_name = $loadInfo['file'];
        $_file_ext  = pathinfo( $_file_name, PATHINFO_EXTENSION );
        $_action    = ( $loadInfo['action'] ) ? $loadInfo['action'] : $ctrl->defaultAct();
        \AmidaMVC\Component\Debug::bug( 'head', "loading file: ".$_file_name );
        \AmidaMVC\Framework\Event::fire( 'Loader::load', $loadInfo );
        /** @var $file_name  relative to ctrl_root. */
        $ctrl->currAct( $_action );
        if( $_file_ext == 'php') {
            include $ctrl->ctrl_root . '/' . $loadInfo[ 'file' ];
        }
        else if( in_array( $_file_ext, array( 'html', 'html' ) ) ) {
            self::loadHtml( $data, $loadInfo );
        }
        else if( in_array( $_file_ext, array( 'text', 'md', 'markdown', 'mark' ) ) ) {
            self::loadMarkdown( $data, $loadInfo );
        }
        else if( in_array( $_file_ext, array( 'css', 'js', 'pdf', 'png', 'jpg', 'gif' ) ) ) {
            self::loadAsIs( $data, $loadInfo, $_file_ext );
        }
    }
    // +-------------------------------------------------------------+
    function actionErr404( $ctrl, &$data ) {
        // do something about error 404, a file not found.
        \AmidaMVC\Component\Debug::bug( 'wordy', 'Loader::Err404 happened!' );
        $data = 'We are sorry about page not found. ';
    }
    // +-------------------------------------------------------------+
    function loadAsIs( &$data, $loadInfo, $_file_ext ) {
        $data->setHttpContent( file_get_contents( $loadInfo[ 'file' ] ) );
        $mime  = mime_content_type( $loadInfo[ 'file' ] );
        if( !$mime ) {
            switch( strtolower( $_file_ext ) ) {
                case 'css':
                    $mime = 'text/css';
                    break;
                case 'js':
                case 'javascript':
                    $mime = 'text/javascript';
                    break;
                case 'jpg':
                case 'jpeg':
                    $mime = 'image/jpeg';
                    break;
                case 'gif':
                    $mime = 'image/gif';
                    break;
                case 'png':
                    $mime = 'image/png';
                    break;
            }
        }
        if( $mime ) {
            $data->setMimeType( $mime );
        }
    }
    // +-------------------------------------------------------------+
    /** load html file into view object.
     * @param $data
     * @param $loadInfo
     */
    function loadHtml( &$data, $loadInfo ) {
        $content = file_get_contents( $loadInfo[ 'file' ] );
        $title   = self::extractTitle( $content );
        if( $title  ) {
            $data->setTitle( $title );
        }
        $data->setContents( $content );
    }
    // +-------------------------------------------------------------+
    /** load markdown file into view object.
     * @param $data
     * @param $loadInfo
     */
    function loadMarkdown( &$data, $loadInfo ) {
        include_once( __DIR__ .  '/../../../vendor/PHPMarkdown/markdown.php' );
        $content = file_get_contents( $loadInfo[ 'file' ] );
        $title   = self::extractTitle( $content );
        if( $title  ) {
            $data->setTitle( $title );
        }
        $content = Markdown( $content );
        $data->setContents( $content );
    }
    // +-------------------------------------------------------------+
    /** extracts title tag from text/html, and remove it.
     * @param $content
     * @return bool
     */
    function extractTitle( &$content ) {
        $pattern = '/\<title\>([^<]*)\<\/title\>/i';
        if( preg_match( $pattern, $content, $matched ) ) {
            $title = $matched[1];
            $content = preg_replace( $pattern, '', $content );
            return $title;
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    /**
     * loads application based on folder structure.
     * say, uri is 'action/action2/...', this loader looks for
     * app.php, action.php, first. if not found, searches for
     * action/app.php, then action/action2.php.
     * @param $ctrl
     * @param $routes                   route to search for.
     * @param $action
     * @return bool|string $file_name   search file name, or FALSE if not found..
     */
    static function searchRoutes( $ctrl, &$routes, &$folder ) {
        // loads from existing app file.
        if( is_array( $routes ) && isset( $routes[0] ) ) {
            // search folder, action.php, or _App.php
            $action  = self::getAction( $routes[0] );
        }
        else {
            // search _App.php only.
            $action = FALSE;
        }
        $loadInfo = array(
            'file' => FALSE,
            'action' => $action
        );
        // try load in subsequent action folder.
        if( $action && is_dir( static::$location . "/{$action}" ) ) {
            // search in the directory.
            if( !empty( $routes ) ) {
                $routes = array_slice( $routes, 1 ); // shorten routes.
            }
            $folder .= "{$action}";
            return self::searchRoutes( $ctrl, $routes, $folder );
        }
        // try loading action.php script.
        if( $action && $file_name = self::getActionFiles( $folder, $action ) ) {
            $routes = array_slice( $routes, 1 );
            $loadInfo[ 'file' ] = $file_name;
            return $loadInfo;
        }
        // try loading ./App.php
        if( $file_name = self::getActionFiles( $folder, '_App' ) ) {
            $action = self::getAction( $routes[0] );
            $loadInfo[ 'file' ] = $file_name;
            $loadInfo[ 'action' ] = $action;
            return $loadInfo;
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    /**
     * TODO: multiple action.* causes headache...
     * @static
     * @param $location     where to search for.
     * @param $action       name of action, ie action.extension
     * @return bool/string  returns filename, false if not found.
     */
    static function getActionFiles( $folder, $action ) {
        $list = glob( static::$location . "/{$folder}/{$action}*", GLOB_NOSORT );
        foreach( $list as $file_name ) {
            $file_name = substr( $file_name, strlen( static::$location ) + 1 );
            return $file_name;
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    static function getAction( $string ) {
        if( is_array( $string ) ) $string = $string[0];
        $action = preg_replace( '/[^._a-zA-Z0-9]/m', '', $string );
        return $action;
    }
    // +-------------------------------------------------------------+
}


