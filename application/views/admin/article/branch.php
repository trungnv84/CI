<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php if(!defined('BASEPATH')) exit('No direct script access allowed')?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php if(isset($browser_title))echo $browser_title?></title>
    <base href="<?php echo $base_url = $this->config->base_url()?>">
	<link href="<?php echo APPFOLDER?>/views/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo APPFOLDER?>/views/admin/css/cupertino/jquery-ui.custom.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo APPFOLDER?>/views/admin/css/admin.css" rel="stylesheet" type="text/css" />
	<script src="<?php echo APPFOLDER?>/views/admin/js/jquery.min.js" type="text/javascript" language="javascript"></script>
	<script src="<?php echo APPFOLDER?>/views/bootstrap/js/bootstrap.min.js" type="text/javascript" language="javascript"></script>
	<script src="<?php echo APPFOLDER?>/views/admin/js/jquery-ui.custom.min.js" type="text/javascript" language="javascript"></script>
	<script src="<?php echo APPFOLDER?>/views/admin/js/admin.js" type="text/javascript" language="javascript"></script>
    <script type="text/javascript" language="javascript">
        $(document).ready(function() {
            function setProgress(value, max) {
                $("#progressbar").progressbar({
                    value: value,
                    max: max,
                    change: function( event, ui ) {
                        $(this).find(".ui-progressbar-value").first().html("<span style='display: inline-block'>"+value+"/"+max+"</span>");
                    }
                });
            }
            setProgress(<?php echo $start=$cursor+$limit?>, <?php echo $total?>);
            function doBranch(start) {
                $.post("admin/branchArticle", {cursor: start}, function(data){
                    if(data && data.cursor) {
                        start = Math.min(data.cursor+data.limit, data.total);
                        setProgress(start, data.total);
                    }
                    if(data.status==1)
                        doBranch(start);
                    else if(data.message)
                        admin.message("", data.message, "info", 0, $("#admin_message"), [{label: ""}]);
                    else
                        location.href = "<?php echo $base_url?>admin/article";
                }, 'json');
            }
            doBranch(<?php echo $start?>);
        });
    </script>
</head>

<body>
<?php require_once APPPATH. 'views/admin/modules/header.php'?>
<div class="admin_wrapper">
    <div class="admin_inner">
        <h2><?php if(isset($page_heading))echo $page_heading?></h2>
        <div id="admin_message">
            <?php if(isset($message) && $message):?>
            <div class="sys_messages_wrapper">
                <?php echo $message?>
            </div>
            <?php endif;?>
        </div>
        <div class="admin_alone_content">
            <div style="margin-bottom: 10px">Tiến trình:</div>
            <div id="progressbar"></div>
        </div><br/><br/>
        <?php require_once APPPATH. 'views/admin/modules/footer.php'?>
    </div>
</div>

</body>
</html>