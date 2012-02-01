<?php

$_ctrl->prependComponent( 'appHello',  'app' );


class appHello extends \AmidaMVC\Component\Model
{
    // +-------------------------------------------------------------+
    static function actionDefault(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$siteObj  )
    {
        $html = '<h1>Hello Application</h1>' . 
            '<p>Hello World from Application</p>';
        $siteObj->setContents( $html );
    }
}