<?php
namespace AmidaMVC\Module;

class Template extends AModule implements IfModule
{
    protected $template_file = '_Config/template.%s.php';
    protected $template_list = array(
        'corp' => 'corporation',
        'base' => 'basic orange',
        'boot' => 'bootstrap'
    );
    protected $var_name = 'template';
    protected $temp_type;
    var $commands = array( '_template' );
    protected $default_template = 'boot';

    /** @var \AmidaMVC\Framework\Controller */
    protected $_ctrl;
    /** @var \AmidaMVC\Framework\PageObj */
    protected $_pageObj;
    /** @var \AmidaMVC\Tools\i18n */
    protected $i18n;
    /** @var \AmidaMVC\Tools\Session */
    protected $session;

    /**
     * @param array $config
     */
    public function __construct( $config=array() ) {
        if( isset( $config[ 'var_name' ] ) ) {
            $this->var_name = $config[ 'var_name' ];
        }
        if( isset( $config[ 'commands' ] ) ) {
            $this->commands = ( is_array( $config[ 'commands' ] ) ) ?
                $config[ 'commands' ] : array( $config[ 'commands' ] );
        }
    }
    /**
     * @param array $option
     * @return mixed
     */
    public function _init( $option = array() ) {
    }
    public function injectI18n( $i18n ) {
        $this->i18n = $i18n;
    }
    public function injectSession( $session ) {
        $this->session = $session;
    }

    /**
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param \AmidaMVC\Framework\PageObj    $_pageObj
     * @param array                          $extra
     * @return mixed
     */
    public function actionDefault( $_ctrl, &$_pageObj, $extra = array() )
    {
        $this->_ctrl = $_ctrl;
        $this->_pageObj = $_pageObj;
        $template = $this->getVariable();
        $this->setCtrlOption( 'template_file', $template );
        $this->drawSection();
        return $extra;
    }

    function getVariable() {
        $this->session->start();
        $sessionId = $this->getSessionId();
        if( $this->findCommand( $this->_ctrl->getCommands() ) && isset( $this->var_name ) ) {
            $this->temp_type = $this->_ctrl->request->getPost( $this->var_name );
        }
        elseif( $this->temp_type = ( $this->session->get( $sessionId ) ) ) {
        }
        if( !$this->temp_type || !isset( $this->template_list[ $this->temp_type ] ) ) {
            $this->temp_type = $this->default_template;
        }
        $template = sprintf( $this->template_file, $this->temp_type );
        $this->session->set( $sessionId, $this->temp_type );
        return $template;
    }
    public function setCtrlOption( $name, $value ) {
        $this->_ctrl->setOption( $name, $value );
    }
    public function getSessionId() {
        $var_name  = ucwords( $this->var_name );
        $sessionId = "_Amida{$var_name}:variable";
        return $sessionId;
    }
    function drawSection() {
        $this->i18n->textSection( '_template' );
        $section = array(
            'title' => $this->i18n->text( 'select_template' ),
            'type'  => 'list',
            'lists' => array(),
        );
        foreach( $this->template_list as $temp_type => $name ) {
            $name = ( $this->i18n->langCode( $name ) ) ?: $name;
            $link = $this->_ctrl->getBaseUrl( $this->_ctrl->getPathInfo() ).'/_template?template='.$temp_type;
            $section[ 'lists' ][] = array( $name, $link );
        }
        $this->_pageObj->section->set( 'template', $section );
    }
}