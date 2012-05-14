<?php
namespace AmidaMVC\Framework;

interface IfModule
{
    /**
     * @abstract
     * @param array $option
     * @return mixed
     */
    public function _init( $option=array() );
    /**
     * @abstract
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $extra
     * @return mixed
     */
    public function actionDefault( $_ctrl, &$_pageObj, $extra=array() );
}
