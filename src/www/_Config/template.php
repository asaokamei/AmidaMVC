<?php
/** @var $_pageObj \AmidaMVC\Framework\PageObj */
/** @var $_ctrl    \AmidaMVC\Framework\Controller */
$_pageObj->setCss( '/common/css/bootstrap.css' );
$_pageObj->setCss( '/common/css/bootstrap-responsive.css' );
$_pageObj->setCss( 'demo.css' );
$_pageObj->setJs( '/common/js/jquery-1.7.1.js' );
$_pageObj->setJs( '/common/js/bootstrap.js' );
$_pageObj->setJs( '/common/js/bootstrap-dropdown.js' );

$_ctrl->i18n->textSection( '_template' );
/** @var $_ctrl string */
$baseUrl = $_ctrl->getBaseUrl();
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
    <header>
        <div id="headTitle">
            <a href="<?php echo $_ctrl->getBaseUrl(); ?>"><?php echo $_ctrl->getOption( 'site_title' ); ?></a>
        </div>
        <ul class="nav nav-pills">
            <li class="active"><a href="<?php echo $_ctrl->getBaseUrl(); ?>">Home</a></li>
            <li><a href="<?php echo $baseUrl;?>docs/README.md">documents</a></li>
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown">source
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo $baseUrl;?>src/README.md">src folder</a></li>
                    <li><a href="<?php echo $baseUrl;?>src/AmidaMVC/README.md">AmidaMVC Source Codes</a></li>
                    <li><a href="<?php echo $baseUrl;?>src/www/">shadow www folder</a></li>
                </ul>
            </li>
            <li><a href="<?php echo $baseUrl;?>vendor/README.md">vendor</a></li>
            <li><a href="<?php echo $baseUrl;?>tests/">tests</a></li>
            <li><a href="<?php echo $baseUrl;?>demo/">demo</a></li>
        </ul>
    </header>
    <div id="content" >
        <!-- content starts -->
        <?php echo $_pageObj->getContent(); ?>
        <!-- content ends -->
    </div>
    <footer>
        <div style="float: right; text-align: right;">
            <p>
                <?php echo $_ctrl->i18n->text('admin_mode'); ?>:<br />
                <?php $authDev = $_ctrl->getServices()->get( 'authDev' );
                if( is_object( $authDev ) && $authDev->isLoggedIn() ) { ?>
                [<a href="<?php echo $baseUrl; ?>dev_logout"><?php echo $_ctrl->i18n->text('logout'); ?></a>]
                <?php } else { ?>
                [<a href="<?php echo $baseUrl; ?>dev_login"><?php echo $_ctrl->i18n->text('login'); ?></a>]
                <?php } ?>
            </p>
        </div>
        <div>
            <p>AppSimple Suites by AmidaMVC.<br />
                https://github.com/asaokamei/AmidaMVC</p>
        </div>
        <p style="clear: both;"></p>
    </footer>
    <script type="text/javascript">
        $(document).ready( function() {
            $( "table" ).addClass( "table" );
            $('.dropdown-toggle').dropdown()
        });
    </script>
</div>
</html>
