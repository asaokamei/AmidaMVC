<?php
namespace AmidaMVC\AppCms;

class Filer implements \AmidaMVC\Framework\IModule
{
    // +-------------------------------------------------------------+
    /**
     * @param array $option
     */
    function _init( $option=array() ) {
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $option
     * @return array
     */
    function actionDefault( $_ctrl, &$_pageObj, $option=array() )
    {
        $_pageObj->setJs(  '../bootstrap/js/jquery-1.7.1.js' );
        $_pageObj->setJs(  '../bootstrap/js/bootstrap.js' );
        $_pageObj->setJs(  '../bootstrap/js/bootstrap-modal.js' );
        $_pageObj->setCss( '../bootstrap/css/bootstrap.css' );
        return $option;
    }
    // +-------------------------------------------------------------+
}

