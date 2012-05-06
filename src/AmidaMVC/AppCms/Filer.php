<?php
namespace AmidaMVC\AppCms;

class Filer implements \AmidaMVC\Framework\IModule
{
    function _init( $option=array() ) {
    }
    function actionDefault( $_ctrl, &$_pageObj, $option=array() )
    {
        return $option;
    }
}

