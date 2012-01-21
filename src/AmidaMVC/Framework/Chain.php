<?php
namespace AmidaMVC\Framework;
/**
 * Dispatcher for application controller.
 * uses Chain of Responsibility pattern...
 */

class Chain
{
    // ---------------------------------
    /**
     * @var null    object model.
     */
    var $model   = NULL;
    /**
     * @var null    current model name.
     */
    var $modelName = NULL;
    /**
     * @var array   list of models. nextModel sets model to the next.
     */
    var $models  = array();

    // ---------------------------------
    /**
     * @var null    name of next action.
     */
    var $nextAct = NULL;
    /**
     * @var null    name of current action.
     */
    var $currAct = NULL;
    /**
     * @var null    name of original dispatched action.
     */
    var $dispatchAct= NULL;
    /**
     * @var string   default exec name if not matched.
     */
    var $defaultAct = 'default';
    /**
     * @var string   prefixAct for action to func/method name.
     */
    var $prefixAct = 'action';
    // +-------------------------------------------------------------+
    function __construct() {
        // nothing.
    }
    // +-------------------------------------------------------------+
    /**
     * get current model.
     * @return string    current model.
     */
    function getModel() {
        return $this->model;
    }
    // +-------------------------------------------------------------+
    /**
     * get current model name.
     * @return string     returns current model name.
     */
    function getModelName() {
        return $this->modelName;
    }
    // +-------------------------------------------------------------+
    /**
     * adds models to Dispatcher.
     * the first model is set to $this->model, the subsequent ones
     * are stored in $this->models[].
     * @param $model      model class or object.
     * @param $name       name of model.
     * @return Dispatch   returns this.
     */
    function addModel( $model, $name=NULL ) {
        if( is_null( $this->model ) ) {
            $this->model = $model;
            $this->modelName = $name;
        }
        else {
            $this->models[] = array( $model, $name );
        }
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * prepend a model to models.
     * @param $model       model.
     * @param null $name   name of the model.
     * @return Dispatch    returns this.
     */
    function prependModel( $model, $name=NULL ) {
        $this->models = array_merge( array( array( $model, $name ) ), $this->models );
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * use next model. for instance, the models can be: auth,
     * cache, data model, and view.
     * @param null $nextAct
     *     sets action name to start the next model. if not set,
     *     uses current action.
     * @return bool/string    next action if next model exists. FALSE if not.
     */
    function nextModel( $nextAct=NULL ) {
        if( isset( $this->models[0] ) ) {
            // replace model with the next model.
            $this->model  = $this->models[0][0];
            $this->modelName = $this->models[0][1];
            $this->models = array_slice( $this->models, 1 );
            // sets next action for the next model.
            if( $nextAct === NULL ) {
                $nextAct = $this->currAct( $this->currAct() );
            }
            else {
                $this->nextAct( $nextAct );
            }
            return $nextAct;
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    /**
     * set current model to given name.
     * @param $name           name of model to set.
     * @return bool|string    returns model name set, or false if not found.
     */
    function skipToModel( $name ) {
        while( $this->models ) {
            if( $name === $this->models[0][1] ) {
                return $name;
            }
            else {
                $this->models = array_slice( $this->models, 1 );
            }
        }
        // should throw an exception, maybe.
        return FALSE;
    }
    // +-------------------------------------------------------------+
    /**
     * @return bool   TRUE if more models exists.
     */
    function moreModels() {
        return !empty( $this->models );
    }
    // +-------------------------------------------------------------+
    /**
     * use next model. for instance, the models can be: auth,
     * cache, data model, and view.
        }
        throw new RuntimeException( 'no next model in Chain. ' );
    }
    // +-------------------------------------------------------------+
    /**
     * set/get next action.
     * @param null $action   sets next action if set.
     * @return string        returns next action.
     */
    function nextAct( $action=NULL ) {
        if( $action !== NULL ) {
            $this->nextAct = $action;
        }
        return $this->nextAct;
    }
    // +-------------------------------------------------------------+
    /**
     * set/get current action.
     * @param null $action    sets current action if set.
     * @return string         returns current action.
     */
    function currAct( $action=NULL ) {
        if( $action !== NULL ) {
            $this->currAct = $action;
        }
        return $this->currAct;
    }
    // +-------------------------------------------------------------+
    /**
     * @param null $action
     * @return string
     */
    function defaultAct( $action=NULL ) {
        if( $action !== NULL ) {
            $this->defaultAct = $action;
        }
        return $this->defaultAct;
    }
    // +-------------------------------------------------------------+
    /**
     * starts loop. I think this is chain of responsibility pattern.
     * TODO: current loop is a bit too complicated.
     * TODO: remove nextAction?
     * @param $action           name of action to start.
     * @param null $data        data to pass to each exec method.
     * @return bool|mixed|null  returns the last returned value.
     */
    function dispatch( $action, &$data=NULL )
    {
        // set current action.
        $return = NULL;
        $this->dispatchAct = $action;
        // -----------------------------
        // chain of responsibility loop.
        while( $action )
        {
            $this->currAct( $action );
            $this->nextAct( FALSE ); // reset next action.
            $this->fireDispatch( $action );
            $return = $this->execAction( $action, $data, $return );
            $action = $this->nextAct();
            // automatically advance to next model.
            if( !$action &&  // next action not set
                $this->moreModels() ) { // still model exists
                $action = $this->nextModel(); // advance model using current action
            }
        }
        // -----------------------------
        return $return;
    }
    // +-------------------------------------------------------------+
    /**
     * a method to hook before dispatching a model/action.
     * overwrite this method to hook.
     * @param $action
     */
    function fireDispatch( $action ) {
        // do nothing.
    }
    // +-------------------------------------------------------------+
    /**
     * get exec object from action name.
     * either it is a model/method or function.
     * @param string $action   name of action.
     * @return array|bool      found exec object.
     */
    function getExecFromAction( $action ) {
        $exec = FALSE;
        if( !$action ) return $exec;
        if( !isset( $this->model ) ) return $exec;

        $action = $this->prefixAct . ucwords( $action );
        $this->loadModel( $this->model );
        if( is_callable( array( $this->model, $action ) ) ) {
            $exec = array( $this->model, $action );
        }
        return $exec;
    }
    // +-------------------------------------------------------------+
    /**
     * loads model if not exist, but *not* implemented!!
     * overwrite thid method if autoload is not enough.
     * @param $model
     * @return \AmidaMVC\Framework\Chain
     */
    function loadModel( $model ) {
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * execute action based on action name and default.
     * @param $action      name of action to execute.
     * @param null $data   data to pass if any.
     * @return bool|mixed  returned value from exec object.
     */
    function execAction( $action, &$data=NULL, $return=NULL ) {
        $exec = $this->getExecFromAction( $action );
        if( !$exec ) {
            $exec = $this->getExecFromAction( $this->defaultAct );
        }
        if( $exec ) {
            $return = call_user_func_array( $exec, array( $this, &$data, $return ) );
            return $return;
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
}


