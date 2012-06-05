<?php
namespace AmidaMVC\Editor;

/**
 * from
 * http://pietschsoft.com/post/2009/07/21/jHtmlArea-The-all-NEW-HTML-WYSIWYG-Editor-for-jQuery.aspx
 * http://jhtmlarea.codeplex.com/
 *
 * current version was jHtmlArea 0.7.0 at the time of downloading.
 */
class jHtmlArea implements IfEditor
{
    var $cmd;
    function __construct( $cmd='_fPut' ) {
        $this->cmd = $cmd;
    }
    function edit( $title, $self, $contents ) {
        $contents = htmlspecialchars( $contents );
        $contents =<<<END_OF_HTML
<h1>{$title}</h1>

    <form method="post" name="_editFile" action="{$self}/{$this->cmd}">
        <textarea name="_putContent" style="width:95%; height:350px; font-family: courier;">{$contents}</textarea>
        <input type="submit" class="btn-primary" name="submit" value="Save File"/>
        <input type="button" class="btn" name="cancel" value="cancel" onclick="location.href='{$self}'"/>
    </form>
    <p style="font-size:small;">Editor: jHtmlArea.js</p>
    <script>
    $(function(){
        $("textarea").htmlarea();
    });
</script>
END_OF_HTML;
        return $contents;
    }
    /**
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     */
    function page( $_pageObj ) {
        $_pageObj->setJs(  '/common/jHtmlArea/scripts/jHtmlArea-0.7.0.js' );
        $_pageObj->setCss( '/common/jHtmlArea/style/jHtmlArea.css' );
    }
}