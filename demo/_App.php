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

$ctrl->topApp1( 'modelApp1', 'model' );

