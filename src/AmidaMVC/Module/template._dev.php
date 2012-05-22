<?php

// filer's mode; _edit, _put, _pub, _del,...
/** @var $_filerObj object  */
/** @var $_ctrl \AmidaMVC\Framework\Controller  */
$self = $_ctrl->getBaseUrl( $_ctrl->getPathInfo() );
$base = $_ctrl->getBaseUrl();

$simpleMenuList = array(
    '_fEdit' => 'edit',
    '_fPub' => 'publish',
    '_fDiff' => 'diff',
);

// ------------------------------------------------------
// Developer's Header Menu
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
            <div style="float: right; color: gray;">source: <span style="color:pink;"><?php echo $_filerObj->file_src;?></span></div>
            <div class="nav-collapse">
                <ul class="nav">
                    <?php
                    // do the simple menu staff
                    foreach( $simpleMenuList as $simCmd => $simMenu ) {
                        if( in_array( $simCmd, $_filerObj->file_cmd ) ) {
                            echo "<li><a href=\"{$self}/{$simCmd}\">{$simMenu}</a></li>\n";
                        }
                    }
                    if( in_array( '_fDel', $_filerObj->file_cmd ) ) {
                        echo '<li><a href="javascript:$(\'#filerDelModal\').toggle(\'fast\');">Delete▲</a></li>';
                    }
                    if( in_array( '_fPurge', $_filerObj->file_cmd ) ) {
                        echo '<li><a href="javascript:$(\'#filerPurgeModal\').toggle(\'fast\');">Purge-File▲</a></li>';
                    }
                    ?>
                    <li><a href="javascript:$('#filerNewForm').toggle('fast');">add file▼</a></li>
                    <li><a href="javascript:$('#filerAddFolder').toggle('fast');">new folder▼</a></li>
                    <?php if( !empty( $_filerObj->file_list) ) { ?>
                    <li><a href="javascript:$('#filerDirList').toggle('fast');">file list▼</a></li>
                    <?php } ?>
                    <?php if( !empty( $_filerObj->backup_list) ) {
                    echo '<li><a href="javascript:$(\'#filerBackUpList\').toggle(\'fast\');">backups▼</a></li>';  } ?>
                    <?php if( !empty( $debug ) ) {
                    echo '<li><a href="javascript:toggle(\'debugInfo\');">debug info▼</a></li>';  } ?>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </div>
<div class="container filerBoxes">
    <!-- modal for delete -->
    <div class="modal" id="filerDelModal" style="display: none;">
        <div class="modal-header">
            <a class="close"  href="javascript:$('#filerDelModal').toggle('fast');">×</a>
            <h3>Delete Edited File</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure to deleted the edited file?</p>
            <p>File: at folder:</p>
        </div>
        <div class="modal-footer">
            <a href="<?php echo "{$self}/_fDel" ?>" class="btn btn-primary">Delete File</a>
            <a href="javascript:$('#filerDelModal').toggle('fast');" class="btn">Cancel</a>
        </div>
    </div>
    <!-- modal for purge -->
    <div class="modal" id="filerPurgeModal" style="display: none;">
        <div class="modal-header">
            <a class="close" href="javascript:$('#filerPurgeModal').toggle('fast');">×</a>
            <h3>Purge This File</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure to purge this file?</p>
            <p>File: at folder:</p>
        </div>
        <div class="modal-footer">
            <a href="<?php echo "{$self}/_fPurge" ?>" class="btn btn-primary">Purge File</a>
            <a href="javascript:$('#filerPurgeModal').toggle('fast')" class="btn">Cancel</a>
        </div>
    </div>
    <!-- adding new file; show empty edit page -->
    <div id="filerNewForm">
        <a href="javascript:$('#filerNewForm').toggle('fast');" class="close">×</a>
        creates new file at: /<?php echo $_filerObj->curr_folder; ?>
        <form method="post" name="_showNewForm" action="<?php echo $self?>/_fFile" class="well form-inline">
            <label>
                <input type="text" name="_newFileName" width="50"
                       placeholder="enter new file name">
            </label>
            <input class="btn-small btn-primary" type="submit" name="submit" value="add new file"/>
        </form>
    </div>
    <!-- adding new folder -->
    <div id="filerAddFolder">
        <a href="javascript:$('#filerAddFolder').toggle('fast');" class="close">×</a>
        add new folder:
        <form method="post" name="_addFolder" action="<?php echo $self?>/_fDir" class="well form-inline">
            <label>
                <input type="text" name="_folderName" width="50"
                                  placeholder="new folder at '/<?php echo $_filerObj->curr_folder; ?>'">
            </label>
            <input class="btn-small btn-primary" type="submit" name="submit" value="add new folder"/>
        </form>
    </div>
    <!-- show error message from _dev components -->
    <?php if( $_filerObj->message ) { ?>
    <div id="filerError" class="alert alert-error">
        <a href="javascript:$('#filerError').toggle('fast');" class="close">×</a>
        <dl>
            <dt>Error: <?php echo $_filerObj->error; ?></dt>
            <dd><?php echo $_filerObj->message; ?></dd>
        </dl>
    </div>
    <?php } ?>
    <!-- show file in the folder -->
    <?php if( !empty( $_filerObj->file_list ) ) { ?>
    <div id="filerDirList">
        <a href="javascript:$('#filerDirList').toggle('fast');" class="close">×</a>
        File/Folder Lists at <?php echo $base; ?>:
        <?php foreach( $_filerObj->file_list as $file ) {  ?>
        <p>
            <?php echo $file;?>:
            <a class="btn-mini btn-info" href="<?php echo "{$base}{$file}";?>">view</a>&nbsp;
            <?php if( substr( $file, -1 ) !== '/' ) { ?>
            <a class="btn-mini btn-info" href="<?php echo "{$base}{$file}/_src";?>">source</a>&nbsp;
            <a class="btn-mini btn-info" href="<?php echo "{$base}{$file}/_raw";?>">raw</a>&nbsp;
            <a class="btn-mini btn-danger" href="<?php echo "{$base}{$file}/_fEdit";?>">edit</a>
            <?php } ?>
        </p>
        <?php } ?>
    </div>
    <?php } ?>
    <!-- show backup file list -->
    <?php if (!empty($_filerObj->backup_list)) { ?>
    <div id="filerBackUpList">
        <a href="javascript:$('#filerBackUpList').toggle('fast');" class="close">×</a>
        Back Up File List:
        <ul>
            <?php foreach ($_filerObj->backup_list as $backup) { ?>
            <li>
                <?php echo $backup;?>:
                <a class="btn-mini btn-info" href="<?php echo "{$self}/_bkView?bf={$backup}";?>">view</a>&nbsp;
                <a class="btn-mini btn-info" href="<?php echo "{$self}/_bkDiff?bf={$backup}";?>">diff</a>
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
    div.filerBoxes {
        margin: 10px 30px 10px 30px;
        background-color:white;
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
        margin: 0;
        padding: 2px;
    }

    div#debugInfo .debugTraceInfo {
        float: left;
        margin: 0 0 0 -20px;
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

<script type="text/javascript">
    $(document).ready( function() {
        function toggle( id ) {
            $( "#" + id ).toggle("fast");
        }
        $( '#filerError' ).click( function(){
            $( '#filerError' ).toggle( 'fast' );
        });
    });
</script>
