<?php
/** @var $_pageObj \AmidaMVC\Framework\PageObj */
/** @var $_ctrl \AmidaMVC\AppSimple\Application */
$_pageObj->setCss( '../bootstrap/css/bootstrap.css' );
$_pageObj->setCss( '../bootstrap/css/bootstrap-responsive.css' );
$_pageObj->setCss( 'demo.css' );
$_pageObj->setJs( '../bootstrap/js/jquery-1.7.1.js' );
$_pageObj->setJs( '../bootstrap/js/bootstrap.js' );
$_pageObj->setJs( '../bootstrap/js/bootstrap-dropdown.js' );
?>
<!DOCTYPE HTML>
<html lang="en">
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

<div class="mainbody">
    <header><a href="<?php echo $_ctrl->getBaseUrl(); ?>"><?php echo $_ctrl->getOption( 'site_title' ); ?></a></header>

    <div id="content" >
        <!-- content starts -->
        <?php echo $_pageObj->getContent(); ?>
        <!-- content ends -->
    </div>
    <footer>
        <p>AppSimple Suites by AmidaMVC.<br />
        gitHub:https://github.com/asaokamei/AmidaMVC</p>
    </footer>
    <script type="text/javascript">
        $(document).ready( function() {
            $( "table" ).addClass( "table" );
        });
    </script>
</div>
</html>
