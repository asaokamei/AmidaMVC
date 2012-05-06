<?php
/** @var $_pageObj \AmidaMVC\Framework\PageObj */
/** @var $_ctrl \AmidaMVC\AppSimple\Application */
$_pageObj->setCss( 'demo.css' );
$_pageObj->setJs( '../bootstrap/js/jquery-1.7.1.js' );
?>
<!DOCTYPE HTML>
<html lang="en">
<title><?php echo $_ctrl->getOption( 'site_title' ); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php echo $_pageObj->getCssLinks( $_ctrl ); ?>
<div class="mainbody">
    <header><a href="<?php echo $_ctrl->getBaseUrl(); ?>"><?php echo $_ctrl->getOption( 'site_title' ); ?></a></header>
    <div id="content" >
        <!-- developer's nav starts here -->
        <style>
            .mainbody {
                margin-top: 50px;
            }
            .devNavMemo {
                float: right; color: gray; font-size: 12px;
            }
            .devNavMemoStrong {
                color:pink;
            }
        </style>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="brand" href="#">_dev</a>
                    <div class="devNavMemo">loadAs: <span class="devNavMemoStrong"><?php echo $_pageObj->loadInfo['loadMode'];?></span></div>
                </div>
            </div>
        </div>
        <!-- developer's nav ends here -->
        <!-- content starts -->
        <?php echo $_pageObj->getContent(); ?>
        <!-- content ends -->
    </div>
    <footer>
        <p>AppSimple Suites by AmidaMVC.<br />
        gitHub:https://github.com/asaokamei/AmidaMVC</p>
    </footer>
<?php echo $_pageObj->getJsLinks( $_ctrl ); ?>
</div>
</html>
