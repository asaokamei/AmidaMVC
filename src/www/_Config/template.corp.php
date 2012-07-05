<?php
/** @var $_pageObj \AmidaMVC\Framework\PageObj */
/** @var $_ctrl    \AmidaMVC\Framework\Controller */
$_pageObj->setCss( '/common/css/bootstrap.css' );
$_pageObj->setCss( '/common/css/bootstrap-responsive.css' );
$_pageObj->setCss( '/corp.css' );
$_pageObj->setJs( '/common/js/jquery-1.7.1.js' );
$_pageObj->setJs( '/common/js/bootstrap.js' );
$_pageObj->setJs( '/common/js/bootstrap-dropdown.js' );

$_ctrl->i18n->textSection( '_template' );
/** @var $_ctrl string */
$baseUrl = $_ctrl->getBaseUrl();
?>
<!DOCTYPE HTML>
<html lang="en">
<meta charset="utf-8">
<title><?php echo $_ctrl->getOption( 'site_title' ); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php echo $_pageObj->getCssLinks( $_ctrl ); ?>
<?php echo $_pageObj->getJsLinks( $_ctrl ); ?>
<?php
if( isset( $_pageObj->devInfo ) ) {
    echo '<!-- developer\'s nav starts here -->', "\n";
    echo $_pageObj->devInfo;
    echo '<!-- developer\'s nav ends here -->', "\n";
}
?>

<div class="container mainbody">
    <div class="row">
        <header class="span12">
            <h1>
                <a href="<?php echo $_ctrl->getBaseUrl(); ?>"><?php echo $_ctrl->getOption( 'site_title' ); ?></a>
            </h1>
            <?php
                if( $_ctrl->getOption( 'site_sub_title' ) ) {
                    echo "<p class=\"lead\">" . $_ctrl->getOption( 'site_sub_title' ) . "</p>\n";
                }
                ?>
            <?php
            if( $topNav = $_pageObj->getComponent( 'topNav' ) ) {
                echo "<div class='headMenu'>" . $topNav->draw( 'tabs' ) ."</div>";
            }
            ?>
        </header>
        <div id="content" class="span8">
            <!-- content starts -->
            <?php echo $_pageObj->getContent(); ?>
            <!-- content ends -->
        </div>
        <div id="side" class="span3">
            <h1>Side Bars</h1>
            <?php
            echo $_pageObj->section->draw( 'auth' );
            echo $_pageObj->section->draw( 'lang' );
            echo $_pageObj->section->draw( 'template' );
            ?>
        </div>
        <div class="span1 visible-desktop">&nbsp;</div>
        <div style="clear: both;"></div>
        <footer class="span12">
            <div class="sectionBox" style="width:250px;float: left;">
                <p>AppSimple Suites by AmidaMVC.<br />
                    https://github.com/asaokamei/AmidaMVC</p>
            </div>
            <p style="clear: both;"></p>
        </footer>
    </div>
    <script type="text/javascript">
        $(document).ready( function() {
            //$( "table" ).addClass( "table" );
            $('.dropdown-toggle').dropdown()
        });
    </script>
</div>
</html>
