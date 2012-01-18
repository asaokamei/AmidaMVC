<?php
namespace AmidaMVC\Component;

/**
 * dumb/simple application loader.
 * currently, it searches app.php or action.php is subsequent folders.
 * this maybe slow.
 * TODO: use route map for finding applications.
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
    static function actionDefault( $ctrl, &$data, $loadInfo=NULL ) {
        // load by searching routes.
        self::setLocation( $ctrl->getLocation() );
        $routes = $ctrl->routes;
        static::$prefix = $ctrl->prefixCmd;

        if( isset( $loadInfo ) ) {
            $ctrl->debug( 'table', $loadInfo, 'Loader::default got loadinfo!!!' );
        }

        $file_name = self::searchRoutes( $ctrl, $routes, $action );
        if( $file_name ) {
            self::loadApp( $ctrl, $data, $file_name );
            $ctrl->nextModel( $action );
        }
        else {
            $ctrl->nextAct( 'Err404' );
        }
    }
    // +-------------------------------------------------------------+
    function loadApp( $ctrl, &$data, $file_name ) {
        $extension = pathinfo( $file_name, PATHINFO_EXTENSION );
        $read_files = array( 'html', 'text' );
        $read_asis  = array( 'pdf', 'png', 'jpg', 'gif' );
        $ctrl->debug( 'head', "loading file: {$file_name} as $extension" );

        if( $extension == 'php') {
            include $file_name;
        }
        else if( in_array( $extension, $read_files ) ) {
            $data = file_get_contents( $file_name );
        }
        else if( in_array( $extension, $read_asis ) ) {
            readfile( $file_name );
        }
    }
    // +-------------------------------------------------------------+
    function actionErr404( $ctrl, &$data ) {
        // do something about error 404, a file not found.
        $ctrl->debug( 'wordy', 'Loader::Err404 happened!' );
        $data = 'We are sorry about page not found. ';
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
    static function searchRoutes( $ctrl, &$routes, &$action ) {
        // loads from existing app file.
        $action = self::getAction( $routes[0] );
        if( self::$postfix === NULL ) {
            $extension = '.php';
        }
        else {
            $extension = '_' . self::$postfix . '.php';
        }
        $prefix = self::$prefix;

        // load application.

        // try load in subsequent action folder.
        $folder = static::$location . "/{$action}";
        if( is_dir( $folder ) && !empty( $routes ) ) {
            $routes = array_slice( $routes, 1 );
            self::$location = $folder;
            return self::searchRoutes( $ctrl, $routes, $action );
        }
        // try loading action.php script.
        if( $file_name = self::getActionFiles( static::$location, $action ) ) {
            $routes = array_slice( $routes, 1 );
            return $file_name;
        }
        // try loading ./App.php
        $file_name = static::$location . "/{$prefix}App{$extension}";
        if( file_exists( $file_name ) ) {
            $action = self::getAction( $routes[0] );
            return $file_name;
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
    static function getActionFiles( $location, $action ) {
        $list = glob( "{$location}/{$action}*", GLOB_NOSORT );
        foreach( $list as $file_name ) {
            return $file_name;
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    static function getAction( $string ) {
        if( is_array( $string ) ) $string = $string[0];
        $action = preg_replace( '/[^_a-zA-Z0-9]/m', '', $string );
        return $action;
    }
    // +-------------------------------------------------------------+
    /**
     * search maps to find file to load.
     * this should be much faster than searchRoutes because there
     * are no file system access.
     * not implemented!!!
     * @static
     * @param $routes
     */
    static function searchMaps( $routes ) {
        // not implemented.
    }
    // +-------------------------------------------------------------+
}


