<?php

class modelRoute
{
    static function actionDefault( $ctrl, $data ) {
        $data->setContents( "modelRoute::default page not found, action=".$ctrl->currAct() );
        $ctrl->nextModel( 'pageNotFound' );
    }
    static function actionList( $ctrl, $data ) {
        $data->setTitle( "Route List" );
        $data->setContents( "modelRoute::list some thing." );
    }
}

$ctrl->prependComponent( 'modelRoute', 'model' );
