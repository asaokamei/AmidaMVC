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

<div class="mainbody">
    <header>
        <div id="headTitle">
            <a href="<?php echo $_ctrl->getBaseUrl(); ?>"><?php echo $_ctrl->getOption( 'site_title' ); ?></a>
            <?php
            if( $_ctrl->getOption( 'site_sub_title' ) ) {
                echo "<p class=\"headSubTitle\">" . $_ctrl->getOption( 'site_sub_title' ) . "</p>\n";
            }
            ?>
        </div>
        <?php
        if( $topNav = $_pageObj->getComponent( 'topNav' ) ) {
            echo "<div class='headMenu'>{$topNav}</div>";
        }
        ?>
    </header>
    <div id="content" >
        <!-- content starts -->
        <?php echo $_pageObj->getContent(); ?>
        <!-- content ends -->
    </div>
    <footer>
        <div class="sectionBox">
            <h3><?php echo $_ctrl->i18n->text('admin_mode'); ?>:</h3>
            <p>
                <?php $authDev = $_ctrl->getServices()->get( 'authDev' );
                if( is_object( $authDev ) && $authDev->isLoggedIn() ) { ?>
                    &nbsp;[<a href="<?php echo $baseUrl; ?>dev_logout"><?php echo $_ctrl->i18n->text('logout'); ?></a>]
                <?php } else { ?>
                    &nbsp;[<a href="<?php echo $baseUrl; ?>dev_login"><?php echo $_ctrl->i18n->text('login'); ?></a>]
                <?php } ?>
            </p>
        </div>
        <?php
        if( isset( $_pageObj->sections[ 'footer' ][ 'lang' ] ) ) {
            $section = $_pageObj->sections[ 'footer' ][ 'lang' ];
            $html = "<div class=\"sectionBox\"><h3>" . $_ctrl->i18n->text($section['title']) . "</h3>\n";
            foreach( $section[ 'lists' ] as $link ) {
                $html .= "<p>&nbsp;[<a href=\"{$link{1}}\">{$link{0}}</a>]</p>";
            }
            $html .= "</div>";
            echo $html;
        }
        ?>
        <div class="sectionBox" style="width:250px;float: left;">
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
