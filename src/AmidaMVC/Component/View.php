<?php
namespace AmidaMVC\Component;

class View
{
    /** @var string   application url. */
    static $app_url = '';
    static $pager_offset_is = 'start';
    static $pager_limit_is  = 'limit';
    // +-------------------------------------------------------------+
    static function _init(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj  )
    {
        // set to base url as default. 
        static::$app_url = $ctrl->getBaseUrl();
    }
    // +-------------------------------------------------------------+
    /**
     * displays page link list from \CenaDta\Dba\Pager. 
     * @static
     * @param array $pn      pagers information
     * @param null $action   action/etc to add to the base url.
     * @return string        resulting page link list
     */
    static function Pager( $pn, $action=NULL ) {
        $url = static::$app_url . $action;
        $html  = '';
        $html .= static::Pager_DisplayLink( $pn['top'],  $url, 'first' );
        $html .= '&nbsp;|&nbsp;';
        $html .= static::Pager_DisplayLink( $pn['prev'], $url, '<' );
        $html .= '&nbsp;';
        $html .= static::Pager_DisplayPages( $pn['page'], $url );
        $html .= static::Pager_DisplayLink( $pn['next'], $url, '>' );
        $html .= '&nbsp;|&nbsp;';
        $html .= static::Pager_DisplayLink( $pn['last'], $url, 'last' );
        return $html;
    }
    // +-------------------------------------------------------------+
    /**
     * displays page list (i.e. 1, 2, 3... ).
     * @param $pages      page info from \CenaDta\Dba\Pager.
     * @param $url        app url.
     * @return string
     */
    function Pager_DisplayPages( &$pages, $url )
    {
        $link = '';
        if( !empty( $pages ) ) {
            foreach( $pages as $key=>$arg ) {
                $tag = static::Pager_DisplayLink( $arg, $url, $key, TRUE );
                if( empty( $arg ) ) {
                    $link .= "<strong>{$tag}</strong>&nbsp;";
                }
                else {
                    $link .= "{$tag}&nbsp;";
                }
            }
        }
        return $link;
    }
    // +-------------------------------------------------------------+
    /**
     * displays a pager's link from \CenaDta\Dba\Pager.
     * @static
     * @param $args         link info (start=n&limit=&).
     * @param $url          app url. 
     * @param $word         word to use
     * @param bool $disp    nothing to display if no link info. 
     * @return null|string  resulting link info. 
     */
    static function Pager_DisplayLink( $args, $url, $word, $disp=TRUE )
    {
        if( WORDY > 4 ) echo " --disp_pv_link( &$args, $url, $word )-- \n";
        if( empty( $args ) ) {
            if( $disp ) {
                $link = "{$word}";
            }
            else {
                $link = NULL;
            }
            return $link;
        }
        
        $offset = 0;
        $limit = 10;
        $preg_pattern = '/' . static::$pager_offset_is . '=([0-9]+)/i';
        if( preg_match( $preg_pattern, $args, $matches ) ) {
            $offset = $matches[1];
            $args = preg_replace( $preg_pattern, '', $args );
            if( substr( $args, 0, 1 ) === '&' ) {
                $args = substr( $args, 1 );
            }
        }
        $preg_pattern = '/' . static::$pager_limit_is . '=([0-9]+)/i';
        if( preg_match( $preg_pattern, $args, $matches ) ) {
            $limit = $matches[1];
            $args = preg_replace( $preg_pattern, '', $args );
            if( substr( $args, 0, 1 ) === '&' ) {
                $args = substr( $args, 1 );
            }
        }
        $url .= "/{$offset}/{$limit}/";
        if( empty( $args ) ) {
            $link = "<a href='{$url}'>{$word}</a>";
        }
        else {
            $link = "<a href='{$url}?{$args}'>{$word}</a>";
        }
        return $link;
    }
    // +-------------------------------------------------------------+
}

