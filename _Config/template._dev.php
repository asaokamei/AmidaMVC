<?php

// filer's mode; _edit, _put, _pub, _del,...
$file_mode = (isset($_siteObj->filerObj->file_mode)) ? $_siteObj->filerObj->file_mode : FALSE;
;
// myself.
$self = $_ctrl->getPath( $_ctrl->getPathInfo() );
$base = $_ctrl->getBaseUrl();

// ------------------------------------------------------
// Developer's Header Menu
ob_start();
ob_implicit_flush(0);
?>
<!-- developer's header section -->
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="#">_dev</a>
            <div style="float: right; color: gray;">source: <span style="color:pink;"><?php echo $_siteObj->filerObj->file_src;?></span></div>
            <div class="nav-collapse">
                <ul class="nav">
                    <?php if( in_array( '_edit', $_siteObj->filerObj->file_cmd ) ) { ?>
                    <li><a href="<?php echo "$self/_edit";?>">edit</a></li>
                    <?php } ?>
                    <?php if( in_array( '_purge', $_siteObj->filerObj->file_cmd ) ) { ?>
                    <li><a data-toggle="modal" href="#filerPurgeModal">purge file</a></li>
                    <?php } ?>
                    <?php foreach( $_siteObj->filerObj->file_cmd as $cmd ) { 
                    if( in_array( $cmd, array('_edit','_purge') ) ) { continue; } ?>
                    <li><a href="<?php echo "$self/$cmd";?>"><?php echo $cmd;?></a></li>
                    <?php } ?>
                    <li><a href="javascript:toggle('filerNewForm')">add file▼</a></li>
                    <li><a href="javascript:toggle('filerAddFolder')">new folder▼</a></li>
                    <?php if( !empty( $_siteObj->filerObj->file_list) ) { ?>
                    <li><a href="javascript:toggle('filerDirList');">file list▼</a></li>
                    <?php } ?>
                    <?php if( !empty( $_siteObj->filerObj->backup_list) ) {
                    echo '<li><a href="javascript:toggle(\'filerBackUpList\');">backups▼</a></li>';  } ?>
                    <?php if( !empty( $debug ) ) {
                    echo '<li><a href="javascript:toggle(\'debugInfo\');">debug info▼</a></li>';  } ?>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </div>
</div>
<div class="container filerBoxes">
    <!-- modal for purge -->
    <div class="modal hide fade" id="filerPurgeModal">
        <div class="modal-header">
            <a class="close" data-dismiss="modal">×</a>
            <h3>Purge This File</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure to purge this file?</p>
            <p>File: at folder:</p>
        </div>
        <div class="modal-footer">
            <a href="<?php echo "{$self}/_purge" ?>" class="btn btn-primary">Purge File</a>
            <a href="javascript:$('#filerPurgeModal').modal('hide')" class="btn">Cancel</a>
        </div>
    </div>
    <!-- adding new file; show empty edit page -->
    <div id="filerNewForm">
        <form method="post" name="_showNewForm" action="<?php echo $self?>/_fileNew" class="well form-inline">
            <label>
                add file:<input type="text" name="_newFileName" width="50"
                       placeholder="creates new file at '/<?php echo $_siteObj->filerObj->curr_folder; ?>'">
            </label>
            <input class="btn-small btn-primary" type="submit" name="submit" value="add new file"/>
        </form>
    </div>
    <!-- adding new folder -->
    <div id="filerAddFolder">
        <form method="post" name="_addFolder" action="<?php echo $self?>/_fileFolder" class="well form-inline">
            <label>
                new folder:<input type="text" name="_folderName" width="50"
                                  placeholder="new folder at '/<?php echo $_siteObj->filerObj->curr_folder; ?>'">
            </label>
            <input class="btn-small btn-primary" type="submit" name="submit" value="add new folder"/>
        </form>
    </div>
    <!-- show error message from _dev components -->
    <?php if (isset($_siteObj->filerObj->error)) { ?>
    <div id="filerError" class="alert alert-error">
        <dl>
            <dt>Error: <?php echo $_siteObj->filerObj->error; ?></dt>
            <dd><?php echo $_siteObj->filerObj->err_msg; ?></dd>
        </dl>
    </div>
    <?php } ?>
    <!-- show file in the folder -->
    <?php if( !empty( $_siteObj->filerObj->file_list ) ) { ?>
    <div id="filerDirList">
        File/Folder Lists at <?php echo $curr_folder = $_siteObj->filerObj->curr_folder; if( $curr_folder) $curr_folder.='/'; ?>:
        <?php foreach( $_siteObj->filerObj->file_list as $file ) {  ?>
        <li>
            <?php echo $file;?>:
            <a class="btn-mini btn-info" href="<?php echo "{$base}{$curr_folder}{$file}";?>">view</a>&nbsp;
            <?php if( substr( $file, -1 ) !== '/' ) { ?>
            <a class="btn-mini btn-info" href="<?php echo "{$base}{$curr_folder}{$file}/_src";?>">source</a>&nbsp;
            <a class="btn-mini btn-info" href="<?php echo "{$base}{$curr_folder}{$file}/_raw";?>">raw</a>&nbsp;
            <a class="btn-mini btn-danger" href="<?php echo "{$base}{$curr_folder}{$file}/_edit";?>">edit</a>
            <?php } ?>
        </li>
        <?php } ?>
    </div>
    <?php } ?>
    <!-- show backup file list -->
    <?php if (!empty($_siteObj->filerObj->backup_list)) { ?>
    <div id="filerBackUpList">
        Back Up File List:
        <ul>
            <?php foreach ($_siteObj->filerObj->backup_list as $backup) { ?>
            <li>
                <?php echo $backup;?>:
                <a class="btn-mini btn-info" href="<?php echo "{$self}/_bkView:{$backup}";?>">view</a>&nbsp;
                <a class="btn-mini" href="<?php echo "{$self}/_bkDiff:{$backup}";?>">diff</a>
            </li>
            <?php } ?>
        </ul>
    </div>
    <?php } ?>
    <!-- show debug info -->
    <?php if (!empty($debug)) { ?>
    <div id='debugInfo'><?php echo $debug;?></div>
    <?php } ?>
</div>
</div>
<?php
$_devHeader = ob_get_clean();

// ------------------------------------------------------
// Developer's Main Body; for editing maybe

if( isset( $_siteObj->filerObj ) && 
    $_siteObj->filerObj->file_mode == '_edit' ) {
    // show form to edit contents.
    ob_start();
    ob_implicit_flush(0);
    ?>
    <form method="post" name="_editFile" action="<?php echo $self?>/_put">
        <textarea name="_putContent" style="width:95%; height:350px; font-family: courier;"
            ><?php echo htmlspecialchars($contents); ?></textarea>
        <input type="submit" class="btn-primary" name="submit" value="put contents"/>
        <input type="button" class="btn" name="cancel" value="cancel" onclick="location.href='<?php echo $self?>'"/>
    </form>
    <?php
    $contents = ob_get_clean();
}

// ------------------------------------------------------
// Developer's Footer; include some more

ob_start();
ob_implicit_flush(0);

?>    
<!-- other stuff -->
<style>
    .mainbody {
        margin-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
    }
    ins {
        color: blue;
        text-decoration: none;
    }
    del {
        color: red;
    }
    div.filerBoxes form {
        margin: 0px;
        padding: 3px;
    }
    div#filerBackUpList {
        display: none;
        margin: 10px;
        padding: 5px 5px 5px 25px;
        border: 1px solid #cccccc;
        box-shadow: 2px 2px 2px #cccccc;
    }
    div#filerError {
        margin: 10px;
        padding: 2px 2px 2px 25px;
        border: 2px solid #d59392;
        box-shadow: 3px 3px 3px #d59392;
    }
    div#filerError dt {
        font-weight: bold;
        color: red;
    }
    div#filerAddFolder {
        display: none;
        margin: 10px;
        padding: 5px 5px 5px 25px;
        border: 1px solid #cccccc;
        box-shadow: 2px 2px 2px #cccccc;
    }
    div#filerNewForm {
        display: none;
        margin: 10px;
        padding: 5px 5px 5px 25px;
        border: 1px solid #cccccc;
        box-shadow: 2px 2px 2px #cccccc;
    }
    div#filerDirList {
        display: none;
        margin: 10px;
        padding: 5px 5px 5px 25px;
        border: 1px solid #cccccc;
        box-shadow: 2px 2px 2px #cccccc;
    }
    div#debugInfo {
        display: none;
        font-size: 12px;
        color: #666666;
        margin: 10px;
        padding: 5px 5px 5px 25px;
        border: 1px solid #cccccc;
        box-shadow: 2px 2px 2px #cccccc;
    }

    div#debugInfo h3 {
        margin: 0px;
        padding: 2px;
    }

    div#debugInfo .debugTraceInfo {
        float: left;
        margin: 0px 0px 0px -20px;
    }

    div#debugInfo table {
        border: 1px solid gray;
        font-size: 11px;
        border-collapse: collapse;
    }

    div#debugInfo td, th {
        border: 1px dotted gray;
        vertical-align: top;
    }

    div#debugInfo th {
        background-color: #E0E0E0;
    }
</style>
<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->

<script src="<?php echo $_siteObj->siteObj->base_url; ?>/bootstrap/js/jquery-1.7.1.js"></script>
<script src="<?php echo $_siteObj->siteObj->base_url; ?>/bootstrap/js/bootstrap-modal.js"></script>
<script type="text/javascript">
    $( "table" ).addClass( "table" );
    function toggle( id ) {
        $( "#" + id ).toggle("fast");
    }
</script>
<?php
$_devFooter = ob_get_clean();

// ------------------------------------------------------
// include template

include( $_siteObj->siteObj->template_orig_file );

/*

<script src="<?php echo $_siteObj->siteObj->base_url; ?>/bootstrap/js/bootstrap-alert.js"></script>
<script src="<?php echo $_siteObj->siteObj->base_url; ?>/bootstrap/js/bootstrap-transition.js"></script>
<script src="<?php echo $_siteObj->siteObj->base_url; ?>/bootstrap/js/bootstrap-alert.js"></script>
<script src="<?php echo $_siteObj->siteObj->base_url; ?>/bootstrap/js/bootstrap-modal.js"></script>
<script src="<?php echo $_siteObj->siteObj->base_url; ?>/bootstrap/js/bootstrap-dropdown.js"></script>
<script src="<?php echo $_siteObj->siteObj->base_url; ?>/bootstrap/js/bootstrap-scrollspy.js"></script>
<script src="<?php echo $_siteObj->siteObj->base_url; ?>/bootstrap/js/bootstrap-tab.js"></script>
<script src="<?php echo $_siteObj->siteObj->base_url; ?>/bootstrap/js/bootstrap-tooltip.js"></script>
<script src="<?php echo $_siteObj->siteObj->base_url; ?>/bootstrap/js/bootstrap-popover.js"></script>
<script src="<?php echo $_siteObj->siteObj->base_url; ?>/bootstrap/js/bootstrap-button.js"></script>
<script src="<?php echo $_siteObj->siteObj->base_url; ?>/bootstrap/js/bootstrap-collapse.js"></script>
<script src="<?php echo $_siteObj->siteObj->base_url; ?>/bootstrap/js/bootstrap-carousel.js"></script>
<script src="<?php echo $_siteObj->siteObj->base_url; ?>/bootstrap/js/bootstrap-typeahead.js"></script></body>

 */

?>