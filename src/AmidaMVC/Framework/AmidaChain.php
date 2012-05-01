<?php
namespace AmidaMVC\Framework;
/**
 * Dispatcher for application controller.
 * uses Chain of Responsibility pattern...
 */

class AmidaChain
{
    /**
     * @var array   list of modules. nextModel sets model to the next.
     */
    var $_modules  = array();
    /**
     * @var null    name of current action.
     */
    var $_action = NULL;
    /**
     * @var null    name of original dispatched action. set *only* after dispatched. 
     */
    var $_dispatchAct= NULL;
    /**
     * @var bool   flag to advance the module in dispatch chain.
     */
    var $_useNextModule = TRUE;
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
    function getModule() {
        if( empty( $this->_modules ) ) return FALSE;
        if( !isset( $this->_modules[0][0] ) ) return FALSE;
        return $this->_modules[0][0];
    }
    // +-------------------------------------------------------------+
    /**
     * get current model name.
     * @return string     returns current model name.
     */
    function getModuleName() {
        if( empty( $this->_modules ) ) return FALSE;
        if( !isset( $this->_modules[0][1] ) ) return FALSE;
        return $this->_modules[0][1];
    }
    // +-------------------------------------------------------------+
    /**
     * adds modules to AmidaChain.
     * @param mixed $module  class or object to plug-in as module.
     * @param null $name       name of module.
     * @return AmidaChain      returns this.
     */
    function addModule( $module, $name=NULL ) {
        $this->appendModule( $module, $name );
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * wraps module input as array( array( comp, name ) ).
     * @param mixed $module
     * @param null $name
     * @return array
     * @throws \RuntimeException
     */
    function _prepareModule( $module, $name=NULL ) {
        if( is_array( $module ) ) {
            if( isset( $module[0] ) && is_array( $module[0] ) ) {
                $compInfo = $module;
            }
            else {
                $compInfo = array( $module );
            }
        }
        else if( $name !== NULL ) {
            $compInfo = array( array( $module, $name ) );
        }
        else {
            throw new \RuntimeException( 'wrong arg in _prepareModule: '. $module );
        }
        return $compInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * append a module to AmidaChain.
     * @param array|string $compInfo   
     * @param null $name 
     * @return AmidaChain
     */
    function appendModule( $compInfo, $name=NULL ) {
        $compInfo = $this->_prepareModule( $compInfo, $name );
        $this->_modules = array_merge( $this->_modules, $compInfo );
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * prepend a module to AmidaChain.
     * @param array|string $compInfo    module and its name in array.
     * @param null $name   name of module
     * @return AmidaChain    returns this.
     */
    function prependModule( $compInfo, $name=NULL ) {
        $compInfo = $this->_prepareModule( $compInfo, $name );
        if( isset( $this->_dispatchAct ) ) {
            // chain already started. 
            // the first module is the current one.
            // so add the new module after the current module.
            $first_module  = $this->_modules[0];
            $this->_modules = array_slice( $this->_modules, 1 );
            $this->_modules = array_merge(
                array( $first_module ),
                $compInfo, 
                $this->_modules
            );
        }
        else {
            // add the module at the beginning of module chain.
            $this->_modules = array_merge( $compInfo, $this->_modules );
        }
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * add a module after given module name (addAfterThisName).
     * @param string $addAfterThisName    add new module after this name.
     * @param string|object $compInfo     module class or object.
     * @param string $name                name of the new module.
     */
    function addModuleAfter( $addAfterThisName, $compInfo, $name ) {
        $new_modules = array();
        foreach( $this->_modules as $comp ) {
            $new_modules[] = $comp;
            if( $addAfterThisName === $comp[1] ) {
                $new_modules[] = array( $compInfo, $name );
            }
        }
        $this->_modules = $new_modules;
    }
    // +-------------------------------------------------------------+
    /**
     * getter/setter for useNextModule flag.
     * @param null $next
     * @return bool
     */
    function useNextModule( $next=NULL ) {
        if( $next === TRUE ) {
            $this->_useNextModule = $next;
        }
        elseif( $next === FALSE ) {
            $this->_useNextModule = $next;
        }
        return $this->_useNextModule;
    }
    // +-------------------------------------------------------------+
    /**
     * use next module.
     * @return \AmidaMVC\Framework\AmidaChain
     */
    function nextModule() {
        if( isset( $this->_modules[0] ) ) {
            // replace model with the next model.
            $this->_modules = array_slice( $this->_modules, 1 );
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
        // keep the current module at idx=0.
        $current_module = $this->_modules[0];
        $this->_modules = array_slice( $this->_modules, 1 );
        
        while( $this->_modules ) {
            if( $name === $this->_modules[0][1] ) {
                break;
            }
            else {
                $this->_modules = array_slice( $this->_modules, 1 );
            }
        }
        $this->_modules = array_merge( $current_module, $this->_modules );
        // should throw an exception, maybe.
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * @return bool   TRUE if more modules exists.
     */
    function moreModels() {
        return !empty( $this->_modules );
    }
    // +-------------------------------------------------------------+
    /**
     * use next model. for instance, the modules can be: auth,
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
     * setter for action, but uses the same module.
     * @param string $action      action name. 
     * @return string      returns the new action. 
     */
    function setMyAction( $action ) {
        $this->setAction( $action );
        $this->useNextModule( FALSE );
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
            if( $this->useNextModule() ) {
                // go to next module.
                $this->nextModule();
            }
            else {
                $this->useNextModule( TRUE ); // reset to TRUE.
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
        $module = $this->getModule();
        $name      = $this->getModuleName();
        $method    = $this->makeActionMethod( $action );
        if( !$action ) return $exec;
        if( !isset( $module ) ) return $exec;

        $this->loadModule( $module, $name );
        if( is_callable( array( $module, $method ) ) ) {
            $exec = array( $module, $method );
        }
        return $exec;
    }
    // +-------------------------------------------------------------+
    /**
     * returns method name from action string.
     * @param string $action
     * @return string
     */
    function makeActionMethod( $action ) {
        return $this->_prefixAct . ucwords( $action );
    }
    // +-------------------------------------------------------------+
    /**
     * loads model if not exist, but *not* implemented!!
     * overwrite this method for tailored auto-loading classes.
     * @param mixed $module   class or object. maybe a function?
     * @param string $name
     * @return AmidaChain
     */
    function loadModule( &$module, $name ) {
        return $this;
    }
    // +-------------------------------------------------------------+
}


