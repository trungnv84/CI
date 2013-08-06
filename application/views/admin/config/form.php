<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php if(isset($browser_title))echo $browser_title?></title>
    <base href="<?php echo $this->config->base_url()?>">
	<link href="<?php echo APPFOLDER?>/views/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo APPFOLDER?>/views/admin/css/cupertino/jquery-ui.custom.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo APPFOLDER?>/views/admin/css/alert.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo APPFOLDER?>/views/admin/css/admin.css" rel="stylesheet" type="text/css"/>
    <script src="<?php echo APPFOLDER?>/views/admin/js/jquery.min.js" type="text/javascript" language="javascript"></script>
	<script src="<?php echo APPFOLDER?>/views/bootstrap/js/bootstrap.min.js" type="text/javascript" language="javascript"></script>
    <script src="<?php echo APPFOLDER?>/views/admin/js/jquery-ui.custom.min.js" type="text/javascript" language="javascript"></script>
    <script src="<?php echo APPFOLDER?>/third_party/tinymce/jscripts/tiny_mce/jquery.tinymce.js" type="text/javascript" language="javascript"></script>
	<script src="<?php echo APPFOLDER?>/views/admin/js/alert.js" type="text/javascript" language="javascript"></script>
    <script src="<?php echo APPFOLDER?>/views/admin/js/admin.js" type="text/javascript" language="javascript"></script>
</head>

<body>

<?php require_once APPPATH. 'views/admin/modules/header.php'?>
<div class="admin_wrapper">
    <div class="admin_inner">
        <h2><?php if(isset($page_heading))echo $page_heading?></h2>
        <div id="admin_message">
            <?php if(isset($message) && $message):?>
                <?php echo $message?>
            <?php endif;?>
        </div>
        <div id="admin_top_panels" class="admin_panels" align="center">
            <a class="admin_button btn-success admin_submit_button admin_save_button" href="admin/saveConfig">
                <i class="admin_button_icon">&nbsp;</i> Lưu</a>
            <a class="admin_button admin_submit_button admin_save_close_button" href="admin/saveConfigAndClose">
                <i class="admin_button_icon">&nbsp;</i> Lưu và Đóng</a>
            <a class="admin_button admin_cancel_button" href="admin/config">
                <i class="admin_button_icon">&nbsp;</i> Đóng</a>
        </div>
        <form id="admin_form" class="admin_form" accept-charset="utf-8" method="post" action="">
        <table border="1" cellpadding="4" cellspacing="0" class="admin_table" width="100%">
            <tbody>
                <tr>
                    <th class="admin_label" width="30%">
						<?php echo $config['name']?><br/>
						<span class="admin_slug"><?php echo $id?></span>
						<input type="hidden" id="id" name="id" value="<?php echo $id?>">
					</th>
                    <td>
						<?php
						$config['input']['name'] = $id;
						$config['input']['value'] = constant($id);
						echo $this->form->form_input($config['input']);
						?>
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="2">
						<a class="admin_button btn-success admin_submit_button admin_save_button" href="admin/saveConfig">
							<i class="admin_button_icon">&nbsp;</i> Lưu</a>
						<a class="admin_button admin_submit_button admin_save_close_button" href="admin/saveConfigAndClose">
							<i class="admin_button_icon">&nbsp;</i> Lưu và Đóng</a>
						<a class="admin_button admin_cancel_button" href="admin/config">
							<i class="admin_button_icon">&nbsp;</i> Đóng</a>
                    </td>
                </tr>
            </tbody>
        </table>
        </form>
        <?php require_once APPPATH. 'views/admin/modules/footer.php'?>
    </div>
</div>

</body>
</html>