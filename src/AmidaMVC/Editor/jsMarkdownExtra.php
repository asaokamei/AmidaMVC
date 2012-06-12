<?php
namespace AmidaMVC\Editor;

/**
 * from
 * https://github.com/tanakahisateru/js-markdown-extra
 *
 * current version was ver 1.0 at the time of the download.
 */
class jsMarkdownExtra implements IfEditor
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
        <textarea id="mdtext" name="_putContent" style="width:100%; height:350px; font-family: courier;">{$contents}</textarea>
        <input type="submit" class="btn-primary" name="submit" value="Save File"/>
        <input type="button" class="btn" name="cancel" value="cancel" onclick="location.href='{$self}'"/>
    </form>
    <p style="font-size:small;">Editor: js-markdown-extra.js</p>
    <div id="result"></div>
    <script>
    $(function(){
        function parse_md() {
            var result = Markdown($('#mdtext').val());
            $('#result').html(result);
            //$('#result-src').text(result);
        }
        var initial = $('#mdtext').val();
        var prev = initial;
        $('#mdtext').keyup(function() {
            var mdtext = $('#mdtext').val();
            if(prev != mdtext) {
                parse_md();
                prev = mdtext;
            }
        });
        $('#reset').click(function() {
            $('#mdtext').val(initial);
            prev = initial;
            parse_md();
        });
        parse_md();
    });
    </script>
END_OF_HTML;
        return $contents;
    }
    /**
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     */
    function page( $_pageObj ) {
        $_pageObj->setJs(  '/common/jsMarkdownExtra/js-markdown-extra.js' );
    }
}