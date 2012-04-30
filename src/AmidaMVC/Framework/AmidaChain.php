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
    var $_components  = array();
    /**
     * @var null    name of current action.
     */
    var $_action = NULL;
    /**
     * @var null    name of original dispatched action. set *only* after dispatched. 
     */
    var $_dispatchAct= NULL;
    /**
     * @var bool   flag to advance the component in dispatch chain. 
     */
    var $_useNextComponent = TRUE;
    /**
     * @var string   default exec name if not matched.
     */
    var $_defaultAct = 'default';
    /**
     * @var string   prefixAct for action to func/method name.
     */
    var $_prefixAct = 'action';
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
        if( empty( $this->_components ) ) return FALSE;
        if( !isset( $this->_components[0][0] ) ) return FALSE;
        return $this->_components[0][0];
    }
    // +-------------------------------------------------------------+
    /**
     * get current model name.
     * @return string     returns current model name.
     */
    function getComponentName() {
        if( empty( $this->_components ) ) return FALSE;
        if( !isset( $this->_components[0][1] ) ) return FALSE;
        return $this->_components[0][1];
    }
    // +-------------------------------------------------------------+
    /**
     * adds components to AmidaChain.
     * @param mixed $component  class or object to plug-in as component.
     * @param null $name       name of component.
     * @return AmidaChain      returns this.
     */
    function addComponent( $component, $name=NULL ) {
        $this->appendComponent( $component, $name );
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * wraps component input as array( array( comp, name ) ). 
     * @param mixed $component
     * @param null $name
     * @return array
     * @throws \RuntimeException
     */
    function _prepareComponent( $component, $name=NULL ) {
        if( is_array( $component ) ) {
            if( isset( $component[0] ) && is_array( $component[0] ) ) {
                $compInfo = $component;
            }
            else {
                $compInfo = array( $component );
            }
        }
        else if( $name !== NULL ) {
            $compInfo = array( array( $component, $name ) );
        }
        else {
            throw new \RuntimeException( 'wrong arg in _prepareComponent: '. $component );
        }
        return $compInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * append a component to AmidaChain.
     * @param array|string $compInfo   
     * @param null $name 
     * @return AmidaChain
     */
    function appendComponent( $compInfo, $name=NULL ) {
        $compInfo = $this->_prepareComponent( $compInfo, $name );
        $this->_components = array_merge( $this->_components, $compInfo );
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * prepend a component to AmidaChain.
     * @param array|string $compInfo    component and its name in array.
     * @param null $name   name of component
     * @return AmidaChain    returns this.
     */
    function prependComponent( $compInfo, $name=NULL ) {
        $compInfo = $this->_prepareComponent( $compInfo, $name );
        if( isset( $this->_dispatchAct ) ) {
            // chain already started. 
            // the first component is the current one. 
            // so add the new component after the current component. 
            $first_component  = $this->_components[0];
            $this->_components = array_slice( $this->_components, 1 );
            $this->_components = array_merge( 
                array( $first_component ),
                $compInfo, 
                $this->_components 
            );
        }
        else {
            // add the component at the beginning of component chain. 
            $this->_components = array_merge( $compInfo, $this->_components );
        }
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * add a component after given component name (addAfterThisName). 
     * @param string $addAfterThisName    add new component after this name. 
     * @param string|object $compInfo     component class or object. 
     * @param string $name                name of the new component. 
     */
    function addComponentAfter( $addAfterThisName, $compInfo, $name ) {
        $new_components = array();
        foreach( $this->_components as $comp ) {
            $new_components[] = $comp;
            if( $addAfterThisName === $comp[1] ) {
                $new_components[] = array( $compInfo, $name );
            }
        }
        $this->_components = $new_components;
    }
    // +-------------------------------------------------------------+
    /**
     * getter/setter for useNextComponent flag. 
     * @param null $next
     * @return bool
     */
    function useNextComponent( $next=NULL ) {
        if( $next === TRUE ) {
            $this->_useNextComponent = $next;
        }
        elseif( $next === FALSE ) {
            $this->_useNextComponent = $next;
        }
        return $this->_useNextComponent;
    }
    // +-------------------------------------------------------------+
    /**
     * use next component.
     * @return \AmidaMVC\Framework\AmidaChain
     */
    function nextComponent() {
        if( isset( $this->_components[0] ) ) {
            // replace model with the next model.
            $this->_components = array_slice( $this->_components, 1 );
        }
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * set current model to given name.
     * @param string $name           name of model to set.
     * @return bool|string    returns model name set, or false if not found.
     */
    function skipToModel( $name ) {
        // keep the current component at idx=0.
        $current_component = $this->_components[0];
        $this->_components = array_slice( $this->_components, 1 );
        
        while( $this->_components ) {
            if( $name === $this->_components[0][1] ) {
                break;
            }
            else {
                $this->_components = array_slice( $this->_components, 1 );
            }
        }
        $this->_components = array_merge( $current_component, $this->_components );
        // should throw an exception, maybe.
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * @return bool   TRUE if more components exists.
     */
    function moreModels() {
        return !empty( $this->_components );
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
        return $this->_action;
    }
    // +-------------------------------------------------------------+
    /**
     * setter for action.
     * @param string $action    sets action.
     * @return string         returns the new action.
     */
    function setAction( $action ) {
        if( isset( $action ) ) {
            $this->_action = $action;
        }
        return $this->_action;
    }
    // +-------------------------------------------------------------+
    /**
     * setter for action, but uses the same component. 
     * @param string $action      action name. 
     * @return string      returns the new action. 
     */
    function setMyAction( $action ) {
        $this->setAction( $action );
        $this->useNextComponent( FALSE );
        return $this->_action;
    }
    // +-------------------------------------------------------------+
    /**
     * setter/getter for default action. 
     * @param null $action
     * @return string
     */
    function defaultAct( $action=NULL ) {
        if( $action !== NULL ) {
            $this->_defaultAct = $action;
        }
        return $this->_defaultAct;
    }
    // +-------------------------------------------------------------+
    /**
     * starts loop. I think this is chain of responsibility pattern.
     * @param string $action           name of action to start.
     * @param mixed $data        data to pass to each exec method.
     * @return bool|mixed|null  returns the last returned value.
     */
    function dispatch( $action, &$data=NULL )
    {
        // set current action.
        $return = NULL;
        $this->_dispatchAct = $action;
        $this->setAction( $action );
        $this->fireStart();
        // -----------------------------
        // chain of responsibility loop.
        while( $this->moreModels() )
        {
            $this->fireDispatch();
            $action = $this->getAction();
            $return = $this->execAction( $action, $data, $return );
            if( $this->useNextComponent() ) {
                // go to next component. 
                $this->nextComponent();
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
     * @param string $action      name of action to execute.
     * @param null $data   data to pass if any.
     * @param null $return data from the previous action.
     * @return bool|mixed  returned value from exec object.
     */
    function execAction( $action, &$data=NULL, $return=NULL ) {
        $exec = $this->getExecFromAction( $action );
        if( !$exec ) {
            $exec = $this->getExecFromAction( $this->_defaultAct );
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
        $name      = $this->getComponentName();
        $method    = $this->_prefixAct . ucwords( $action );
        if( !$action ) return $exec;
        if( !isset( $component ) ) return $exec;

        $this->loadComponent( $component, $name );
        if( is_callable( array( $component, $method ) ) ) {
            $exec = array( $component, $method );
        }
        return $exec;
    }
    // +-------------------------------------------------------------+
    /**
     * loads model if not exist, but *not* implemented!!
     * overwrite this method for tailored auto-loading classes.
     * @param mixed $component   class or object. maybe a function?
     * @param string $name
     * @return AmidaChain
     */
    function loadComponent( &$component, $name ) {
        return $this;
    }
    // +-------------------------------------------------------------+
}


