<?php
namespace AmidaMVC\Module;

class Lang extends AModule implements IfModule
{
    /*
     * simple to do
     * - set available language
     * - determine the language to use
     *   - check if _lang in url
     *   - check if _lang specified
     *   - check for _lang in session
     *   - check for browser's accept language
     *   - then, check against available language
     * - set i18n's language
     * - fill the language selection
     * - setup ctrl_root
     * - replace pathInfo and baseUrl in Request
     */
    /** @var array   list of available languages in the application */
    protected $langList = array( 'en', 'ja' );

    /** @var array   list of supported commands.  */
    var $commands = array( '_lang' );

    /** @var string  check url against this regexp */
    protected $matchUrl = '';

    /** @var bool */
    protected $matchRewrite = FALSE;

    /** @var string  set ctrl_root to the name below */
    protected $ctrlRootName = array();

    /** @var bool|string  selected language */
    protected $language = 'en';

    /** @var string */
    protected $sessionSaveId = '_AmidaLanguage:Lang';

    /** @var \AmidaMVC\Tools\i18n */
    protected $i18n;

    /** @var \AmidaMVC\Tools\Session */
    protected $session;

    /** @var \AmidaMVC\Framework\Controller */
    protected $_ctrl;

    /** @var \AmidaMVC\Framework\PageObj */
    protected $_pageObj;
    // +-------------------------------------------------------------+
    /**
     * @param array $config
     * @return \AmidaMVC\Module\Lang
     */
    public function __construct( $config=array() ) {
        if( !empty( $config ) ) {
            foreach( $config as $key => $val ) {
                if( $key == 'lang_list' ) {
                    $this->langList = $val;
                }
                elseif( $key == 'ctrl_root' ) {
                    $this->ctrlRootName = $val;
                }
                elseif( $key == 'match_url' ) {
                    $this->matchUrl = $val;
                }
            }
        }
        // lang_list
        // ctrl_root folder name
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
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $extra
     * @return mixed
     */
    public function actionDefault( $_ctrl, &$_pageObj, $extra = array() )
    {
        $this->_ctrl = $_ctrl;
        $this->_pageObj = $_pageObj;
        $this->getLanguage();
        // setup i18n with language.
        $this->i18n->language( $this->language );
        $this->i18n->loadFiles();
        // setup ctrl root
        $this->ctrlRoot();
        $this->drawSection();
        return $extra;
    }
    function drawSection() {
        $section = array(
            'title' => $this->i18n->text( 'select_lang' ),
            'type'  => 'list',
            'lists' => array(),
        );
        foreach( $this->langList as $lang ) {
            $name = ( $this->i18n->langCode( $lang ) ) ?: $lang;
            if( $this->matchRewrite ) {
                $link = $this->_ctrl->getBaseUrl('../')."{$lang}/";
            }
            else {
                $link = $this->_ctrl->getBaseUrl( $this->_ctrl->getPathInfo() ).'/_lang?language='.$lang;
            }
            $section[ 'lists' ][] = array( $name, $link );
        }
        $this->_pageObj->section->set( 'lang', $section );
    }
    // +-------------------------------------------------------------+
    function ctrlRoot() {
        if( $this->ctrlRootName ) {
            $ctrlRoot = FALSE;
            if( array_key_exists( $this->language, $this->ctrlRootName ) ) {
                $ctrlRoot = $this->ctrlRootName[ $this->language ];
            }
            elseif( isset( $this->ctrlRootName[0] ) ) {
                $ctrlRoot = $this->ctrlRootName[0];
            }
            if( $ctrlRoot ) {
                $ctrlRoot = $this->_fill( $ctrlRoot, $this->language );
                $this->_ctrl->setCtrlRoot( $ctrlRoot );
            }
        }
    }
    /**
     * @return bool
     */
    function getLanguage()
    {
        $pathInfo = $this->_getPathInfo();
        $accepted = $this->_getAcceptedLanguages();
        $language =
            ( $this->checkUrl( $pathInfo ) ) ?:
            ( $this->checkPost() ) ?:
            ( $this->checkSession() ) ?:
            ( $this->checkHttp( $accepted ) );
        if( $language ) {
            $this->language = $language;
            $this->session->set( $this->sessionSaveId, $language );
        }
    }
    /**
     * @param array $accepted
     * @return bool
     */
    function checkHttp( $accepted ) {
        $language = FALSE;
        foreach( $accepted as $lang ) {
            if( in_array( $lang, $this->langList ) ) {
                $language = $lang;
                break;
            }
        }
        return $language;
    }
    /**
     * @return bool|string
     */
    function checkSession() {
        $this->session->start();
        $language = ( $this->session->get( $this->sessionSaveId ) ) ?: FALSE;
        return $language;
    }
    /**
     * @return bool|string
     */
    function checkPost() {
        $language = FALSE;
        $command  = $this->findCommand( $this->_ctrl->getCommands() );
        if( $command == '_lang' ) {
            $language = ( $this->_ctrl->request->getPost( 'language', 'code' ) ) ?: FALSE;
        }
        return $language;
    }
    /**
     * @param string $pathInfo
     * @return bool|string
     */
    function checkUrl( $pathInfo ) {
        $language    = FALSE;
        $token       = NULL;
        foreach( $this->langList as $lang ) {
            $exp = $this->_fill( $this->matchUrl, $lang );
            if( preg_match( "/({$exp})/i", $pathInfo, $matches ) ) {
                $language = $lang;
                $token    = $matches[1];
                break;
            }
        }
        if( $language && $token ) {
            $this->_ctrl->request->rewriteBaseAndPath( $token );
            $this->matchRewrite = TRUE;
        }
        return $language;
    }
    /**
     * @param $text
     * @param null|string $lang
     * @return mixed
     */
    function _fill( $text, $lang=NULL ) {
        $lang = ( isset( $lang ) ) ? $lang: $this->language;
        $text = str_replace( '{lang}', $lang, $text );
        return $text;
    }
    /**
     * @return null|string
     */
    function _getPathInfo() {
        return $this->_ctrl->getPathInfo();
    }
    /**
     * @return array
     */
    function _getAcceptedLanguages() {
        return $this->_ctrl->request->getLanguageList();
    }
    // +-------------------------------------------------------------+
}