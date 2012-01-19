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
            $ctrl->nextModel( 'Err404' );
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
        $ctrl->debug( 'head', "loading file: {$file_name} as $extension" );

        /** @var $file_name  relative to ctrl_root. */
        if( pathinfo( $loadInfo[ 'file' ], PATHINFO_EXTENSION ) == 'php') {
            include $ctrl->ctrl_root . '/' . $loadInfo[ 'file' ];
        }
        else if( in_array( $extension, array( 'html', 'text' ) ) ) {
            $data = file_get_contents( $loadInfo[ 'file' ] );
        }
        else if( in_array( $extension, array( 'pdf', 'png', 'jpg', 'gif' ) ) ) {
            readfile( $loadInfo[ 'file' ] );
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


