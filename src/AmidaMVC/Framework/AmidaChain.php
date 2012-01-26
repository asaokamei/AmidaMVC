<?php
namespace AmidaMVC\Framework;
/**
 * Dispatcher for application controller.
 * uses Chain of Responsibility pattern...
 */

class AmidaChain
{
    /**
     * @var array   list of components. nextModel sets model to the next.
     */
    var $components  = array();
    /**
     * @var null    name of current action.
     */
    var $action = NULL;
    /**
     * @var null    name of original dispatched action. set *only* after dispatched. 
     */
    var $dispatchAct= NULL;
    /**
     * @var bool   flag to advance the component in dispatch chain. 
     */
    var $useNextComponent = TRUE;
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
    function getComponent() {
        return $this->components[0][0];
    }
    // +-------------------------------------------------------------+
    /**
     * get current model name.
     * @return string     returns current model name.
     */
    function getComponentName() {
        return $this->components[0][1];
    }
    // +-------------------------------------------------------------+
    /**
     * adds components to AmidaChain.
     * @param $component  class or object to plug-in as component.
     * @param $name       name of component.
     * @return Dispatch   returns this.
     */
    function addComponent( $component, $name=NULL ) {
        $this->appendComponent( $component, $name );
        return $this;
    }
    // +-------------------------------------------------------------+
    function _prepareComponent( $component, $name=NULL ) {
        if( is_array( $component ) ) {
            if( is_array( $component[0] ) ) {
                $compInfo = $component;
            }
            else {
                $compInfo = array( $component );
            }
        }
        else if( $name !== NULL ) {
            $compInfo = array( array( $component, $name ) );
        }
        return $compInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * append a component to AmidaChain.
     * @param $compInfo    component and its name in array.
     * @param null $name   name of component
     * @return Dispatch    returns this.
     */
    function appendComponent( $compInfo, $name=NULL ) {
        $compInfo = $this->_prepareComponent( $compInfo, $name );
        $this->components = array_merge( $this->components, $compInfo );
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * prepend a component to AmidaChain.
     * @param $compInfo    component and its name in array.
     * @param null $name   name of component
     * @return Dispatch    returns this.
     */
    function prependComponent( $compInfo, $name=NULL ) {
        $compInfo = $this->_prepareComponent( $compInfo, $name );
        if( isset( $this->dispatchAct ) ) {
            // chain already started. 
            // the first component is the current one. 
            // so add the new component after the current component. 
            $first_component  = $this->components[0];
            $this->components = array_slice( $this->components, 1 );
            $this->components = array_merge( 
                array( $first_component ),
                $compInfo, 
                $this->components 
            );
        }
        else {
            // add the component at the beginning of component chain. 
            $this->components = array_merge( $compInfo, $this->components );
        }
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * getter/setter for useNextComponent flag. 
     * @param null $next
     * @return bool
     */
    function useNextComponent( $next=NULL ) {
        if( $next === TRUE ) {
            $this->useNextComponent = $next;
        }
        elseif( $next === FALSE ) {
            $this->useNextComponent = $next;
        }
        return $this->useNextComponent;
    }
    // +-------------------------------------------------------------+
    /**
     * use next model. for instance, the components can be: auth,
     * cache, data model, and view.
     * @param null $nextAct
     *     sets action name to start the next model. if not set,
     *     uses current action.
     * @return bool/string    next action if next model exists. FALSE if not.
     */
    function _nextModel() {
        if( isset( $this->components[0] ) ) {
            // replace model with the next model.
            $this->components = array_slice( $this->components, 1 );
        }
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * set current model to given name.
     * @param $name           name of model to set.
     * @return bool|string    returns model name set, or false if not found.
     */
    function skipToModel( $name ) {
        while( $this->components ) {
            if( $name === $this->components[0][1] ) {
                return $name;
            }
            else {
                $this->components = array_slice( $this->components, 1 );
            }
        }
        // should throw an exception, maybe.
        return FALSE;
    }
    // +-------------------------------------------------------------+
    /**
     * @return bool   TRUE if more components exists.
     */
    function moreModels() {
        return !empty( $this->components );
    }
    // +-------------------------------------------------------------+
    /**
     * use next model. for instance, the components can be: auth,
     * cache, data model, and view.
        }
        throw new RuntimeException( 'no next model in AmidaChain. ' );
    }
    // +-------------------------------------------------------------+
    /**
     * getter for action.
     * @return string        returns action.
     */
    function getAction() {
        return $this->action;
    }
    // +-------------------------------------------------------------+
    /**
     * setter for action.
     * @param null $action    sets action.
     * @return string         returns the new action.
     */
    function setAction( $action ) {
        $this->action = $action;
        return $this->action;
    }
    // +-------------------------------------------------------------+
    function execOwnAction( $action ) {
        $this->setAction( $action );
        $this->useNextComponent( FALSE );
        return $this->action;
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
        $this->setAction( $action );
        $this->fireStart();
        // -----------------------------
        // chain of responsibility loop.
        while( $this->moreModels() )
        {
            $this->fireDispatch();
            $return = $this->execAction( $action, $data, $return );
            if( $this->useNextComponent() ) {
                // go to next component. 
                $this->_nextModel();
            }
            else {
                $this->useNextComponent( TRUE ); // reset to TRUE. 
            }
        }
        // -----------------------------
        return $return;
    }
    // +-------------------------------------------------------------+
    /**
     * methods to hook before dispatching a model/action.
     * overwrite this method to observe dispatch chain.
     */
    function fireStart() {
        // do nothing.
    }
    // +-------------------------------------------------------------+
    function fireDispatch() {
        // do nothing.
    }
    // +-------------------------------------------------------------+
    /**
     * execute action based on action name and default.
     * @param $action      name of action to execute.
     * @param null $data   data to pass if any.
     * @param null $return data from the previous action.
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
    /**
     * get exec object from action name.
     * either it is a model/method or function.
     * @param string $action   name of action.
     * @return array|bool      found exec object.
     */
    function getExecFromAction( $action ) {
        $exec      = FALSE;
        $component = $this->getComponent();
        $method    = $this->prefixAct . ucwords( $action );
        if( !$action ) return $exec;
        if( !isset( $component ) ) return $exec;

        $this->loadModel( $component );
        if( is_callable( array( $component, $method ) ) ) {
            $exec = array( $component, $method );
        }
        return $exec;
    }
    // +-------------------------------------------------------------+
    /**
     * loads model if not exist, but *not* implemented!!
     * overwrite this method for tailored auto-loading classes.
     * @param $model
     * @return \AmidaChain\Framework\Chain
     */
    function loadModel( $model ) {
        return $this;
    }
    // +-------------------------------------------------------------+
}


