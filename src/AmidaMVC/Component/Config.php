<?php
namespace AmidaMVC\Component;
/**
 * TODO: make Config
 *  for controlling multiple-lang, admin, debug info,
 *  dev/staging/real server.
*
 */
class Config
{
    static $loaded = FALSE;
    static $prefix = '';
    static $postfix = '';
    static $folder  = 'Config';
    static $appRoot = NULL;
    static $currFolder = NULL;
    static $modelToLoad = array();
    // +-------------------------------------------------------------+
    static function _init() {
        static::$modelToLoad = array(
            'admin'  => 'Admin',
            'review' => 'Admin',
            'dev'    => 'Dev',
            'en'     => 'Lang',
            'ja'     => 'Lang',
        );
    }
    // +-------------------------------------------------------------+
    function actionDefault( $ctrl, $request ) {
        // load extra models as in command list.
        $command = $ctrl->command;
        foreach( $command as $loadModel ) {
            if( in_array( $loadModel, self::$modelToLoad ) ) {
                $model = self::$modelToLoad[ $loadModel ];
                $ctrl->loadModel( $model );
            }
        }
    }
    // +-------------------------------------------------------------+
}