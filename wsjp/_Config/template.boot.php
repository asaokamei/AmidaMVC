<?php
/** @var $_pageObj \AmidaMVC\Framework\PageObj */
/** @var $_ctrl    \AmidaMVC\Framework\Controller */
$_pageObj->setCss( '/common/css/bootstrap.css' );
$_pageObj->setCss( '/common/css/bootstrap-responsive.css' );
$_pageObj->setJs( '/common/js/jquery-1.7.1.js' );
$_pageObj->setJs( '/common/js/bootstrap.js' );
$_pageObj->setJs( '/common/js/bootstrap-dropdown.js' );
$_pageObj->setCss( '/boot.css' );

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
            <a href="<?php echo $_ctrl->getBaseUrl(); ?>" ><img class="" id="" src="<?php echo $_ctrl->getBaseUrl( '/common/img/logo.gif'); ?>" title="<?php echo $_ctrl->getOption( 'site_title' ); ?>"></a>
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
        <article id="content" class="span8">
            <!-- content starts -->
            <?php echo $_pageObj->getContent(); ?>
            <!-- content ends -->
        </article>
        <side class="span4">
            <div class="sectionBox">
                <h3>所在地・連絡先</h3>
                <dl>
                    <dt>名前：</dt>
                    <dd>亀井亜佐夫</dd>
                    <dt>屋号：</dt>
                    <dd>ワークスポット･ジェーピー</dd>
                    <dt>住所：</dt>
                    <dd>〒152-0022 東京都目黒区</dd>
                    <dt>連絡方法：</dt>
                    <dd>090-4660-7870　（携帯）<br />
                    info @ workspot.jp　（email）</dd>
                </dl>
            </div>
            <div style="clear:both;"></div>
            <?php
            echo $_pageObj->section->draw( 'lang' );
            ?>
        </side>
        <footer class="span12">
            <div class="sectionBox">
                <p><strong>WorkSpot.JP</strong> brings open source to business...</p>
                </dl>
                <ul class="bottomNav">
                    <li><a href="<?php echo $_ctrl->getBaseUrl( '/serv.md'); ?>" ><img src="<?php echo $_ctrl->getBaseUrl( '/common/img/bar_ser.gif'); ?>"></a></li>
                    <li><a href="<?php echo $_ctrl->getBaseUrl( '/prof.md'); ?>" ><img src="<?php echo $_ctrl->getBaseUrl( '/common/img/bar_pro.gif'); ?>"></a></li>
                    <li><a href="<?php echo $_ctrl->getBaseUrl( '/expr.md'); ?>" ><img src="<?php echo $_ctrl->getBaseUrl( '/common/img/bar_exp.gif'); ?>"></a></li>
                    <li><a href="<?php echo $_ctrl->getBaseUrl( '/tech/'); ?>" ><img src="<?php echo $_ctrl->getBaseUrl( '/common/img/bar_tec.gif'); ?>"></a></li>
                </ul>
            </div>
            <div class="span4" style="float: right;">
                <?php
                echo $_pageObj->section->draw( 'auth' );
                ?>
            </div>
            <p style="clear: both;"></p>
        </footer>
    </div>
    <script type="text/javascript">
        $(document).ready( function() {
            $( "table" ).addClass( "table" );
            $('.dropdown-toggle').dropdown()
        });
    </script>
</div>
</html>
