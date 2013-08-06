<?php if(!defined('BASEPATH')) exit('No direct script access allowed')?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php if(isset($browser_title))echo $browser_title?></title>
    <base href="<?php echo $this->config->base_url()?>">
    <link href="<?php echo APPFOLDER?>/views/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo APPFOLDER?>/views/admin/css/admin.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo APPFOLDER?>/views/admin/js/jquery.min.js" type="text/javascript" language="javascript"></script>
    <script src="<?php echo APPFOLDER?>/views/bootstrap/js/bootstrap.min.js" type="text/javascript" language="javascript"></script>
    <script src="<?php echo APPFOLDER?>/views/admin/js/admin.js" type="text/javascript" language="javascript"></script>
</head>

<body>

<?php require_once APPPATH. 'views/admin/modules/header.php'?>
<div class="admin_wrapper">
    <div class="admin_inner">
        <div id="admin_message">
            <?php if(isset($message) && $message):?>
            <div class="sys_messages_wrapper">
                <?php echo $message?>
            </div>
            <?php endif;?>
        </div>
        <div class="admin_dashboard">
            <ul class="thumbnails">
				<li class="span2">
					<div class="thumbnail">
						<a class="shortcut geer-icon" href="admin/config">Cấu hình</a>
					</div>
				</li>
                <li class="span2">
                    <div class="thumbnail">
                        <a class="shortcut user-icon" href="admin/user">Người dùng</a>
                    </div>
                </li>
				<li class="span2">
					<div class="thumbnail">
						<a class="shortcut menu-icon" href="admin/category/6">Menu</a>
					</div>
				</li>
				<li class="span2">
					<div class="thumbnail">
						<a class="shortcut category-icon" href="admin/category/1">Loại tin</a>
					</div>
				</li>
				<li class="span2">
					<div class="thumbnail">
						<a class="shortcut article-icon" href="admin/article">Bài viết</a>
					</div>
				</li>
				<li class="span2">
					<div class="thumbnail">
						<a class="shortcut tree-icon" href="admin/category/2">Nhóm sản phẩm</a>
					</div>
				</li>
				<!--<li class="span2">
					<div class="thumbnail">
						<a class="shortcut type-icon" href="admin/category/3">Loại sản phẩm</a>
					</div>
				</li>-->
				<li class="span2">
					<div class="thumbnail">
						<a class="shortcut product-icon" href="admin/product">Sản phẩm</a>
					</div>
				</li>
				<li class="span2">
					<div class="thumbnail">
						<a class="shortcut image-group-icon" href="admin/category/7">Nhóm banner</a>
					</div>
				</li>
				<li class="span2">
					<div class="thumbnail">
						<a class="shortcut advertising-icon" href="admin/banner">Banner quảng cáo</a>
					</div>
				</li>
            </ul>
        </div>
        <?php require_once APPPATH. 'views/admin/modules/footer.php'?>
    </div>
</div>

</body>
</html>