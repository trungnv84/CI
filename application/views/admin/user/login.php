<?php if(!defined('BASEPATH')) exit('No direct script access allowed')?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php if(isset($browser_title))echo $browser_title?></title>
    <base href="<?php echo $this->config->base_url()?>">
    <link href="<?php echo APPFOLDER?>/views/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo APPFOLDER?>/views/admin/css/admin.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo APPFOLDER?>/views/admin/css/alert.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo APPFOLDER?>/views/admin/js/jquery.min.js" type="text/javascript" language="javascript"></script>
    <script src="<?php echo APPFOLDER?>/views/bootstrap/js/bootstrap.min.js" type="text/javascript" language="javascript"></script>
    <script src="<?php echo APPFOLDER?>/views/admin/js/admin.js" type="text/javascript" language="javascript"></script>
    <script src="<?php echo APPFOLDER?>/views/admin/js/alert.js" type="text/javascript" language="javascript"></script>
    <script type="text/javascript" language="javascript">
        $(document).ready(function() {
            admin.validate = function(btn) {
                var result = true;
                var el = $("#username");
                var text = $.trim(el.val());
                if(text=="") {
                    admin.invalid(el, "Bạn phải nhập \"tên đăng nhập\".");
                    result = false;
                }
                var el = $("#password");
                var text = $.trim(el.val());
                if(text=="") {
                    admin.invalid(el, "Bạn phải nhập \"mật khẩu\".");
                    result = false;
                }
                return result;
            };
        });
    </script>
</head>

<body id="admin_login_page">

<?php require_once APPPATH. 'views/admin/modules/header.php'?>
<div class="admin_wrapper">
    <div class="admin_inner">
        <form class="admin_form_validate" method="post">
			<?php if(USE_SESSION_TOKEN) {
				$CI =& get_instance();
				$CI->load->library('form');
				echo $CI->form->form_token('admin_login');
			}?>
            <h1>Đăng nhập quản trị</h1>
            <hr />
            <div id="admin_message">
                <?php if(isset($message) && $message):?>
                <?php echo $message?>
                <?php endif;?>
            </div>
            <label for="username">Tên đăng nhập</label>
            <input type="text" id="username" name="username" value="<?php if(isset($username))echo $username?>">
            <br /><br />
            <label for="password">Mật khẩu</label>
            <input type="password" id="password" name="password">
            <br /><br />
            <button type="submit" class="btn">Đăng nhập</button>
        </form>
        <?php require_once APPPATH. 'views/admin/modules/footer.php'?>
    </div>
</div>

</body>
</html>