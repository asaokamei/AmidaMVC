<?php
namespace AmidaMVC\Component;

class Model
{
    // +-------------------------------------------------------------+
    static function actionDefault( $ctrl, $view ) {

    }
    // +-------------------------------------------------------------+
    /**
     * a dumb setup routine for Rest. 
     * creates actionAction_Method like 'actionResource_put'.   
     * @static
     * @param \AmidaMVC\Framework\Controller $ctrl
     * @param SiteObj $siteObj
     */
    static function setRestMethod(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$siteObj )
    {
        $methods = array( '_post', '_put', '_delete', '_edit', '_new', '_get' );
        $siteInfo = $siteObj->get( 'siteObj' );
        $restMethod = FALSE;
        foreach( $methods as $method ) {
            if( in_array( $method, $siteInfo[ 'command' ] ) ) {
                $restMethod = $method;
            }
        }
        if( $restMethod ) {
            $action = $ctrl->getAction();
            $action .= $restMethod;
            $ctrl->setAction( $action );
        }
    }
    // +-------------------------------------------------------------+
}

