<?php
namespace AmidaMVC\Editor;

class TextArea implements IfEditor
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
    <p style="font-size:small;">Editor: TextArea</p>
END_OF_HTML;
        return $contents;
    }
}