<?php

class topApp1
{
    static function actionDefault( $ctrl, &$data ) {
        $data = "topApp1::Default, action=".$ctrl->currAct();
    }
    static function actionList( $ctrl, &$data ) {
        $data = "topApp1::List, action=".$ctrl->currAct();
    }
}

$ctrl->prependModel( 'topApp1', 'model' );

