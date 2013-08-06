<?php if (!defined('BASEPATH')) exit('No direct script access allowed')?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php if (isset($browser_title)) echo $browser_title?></title>
    <base href="<?php echo $this->config->base_url()?>">
    <link href="<?php echo APPFOLDER?>/views/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
	<link href="<?php echo APPFOLDER?>/views/admin/css/cupertino/jquery-ui.custom.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo APPFOLDER?>/views/admin/css/alert.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo APPFOLDER?>/views/admin/css/admin.css" rel="stylesheet" type="text/css"/>
    <script src="<?php echo APPFOLDER?>/views/admin/js/jquery.min.js" type="text/javascript" language="javascript"></script>
    <script src="<?php echo APPFOLDER?>/views/bootstrap/js/bootstrap.min.js" type="text/javascript" language="javascript"></script>
	<script src="<?php echo APPFOLDER?>/views/admin/js/jquery-ui.custom.min.js" type="text/javascript" language="javascript"></script>
    <script src="<?php echo APPFOLDER?>/views/admin/js/alert.js" type="text/javascript" language="javascript"></script>
    <script src="<?php echo APPFOLDER?>/views/admin/js/admin.js" type="text/javascript" language="javascript"></script>
    <script type="text/javascript" language="javascript">
        admin.elemFocus = "#username";
        $(document).ready(function () {
			$("#start, #expire").datepicker({
				showOtherMonths: true,
				showButtonPanel: true,
				selectOtherMonths: true,
				changeMonth: true,
				changeYear: true,
				numberOfMonths: 2,
				dateFormat: "dd-mm-yy",
				/*showOn: "both",
				 buttonText: "Chọn",*/
				showWeek: true,
				firstDay: 1
			});

			$("#start").datepicker("option", "onClose",
				function( selectedDate, inst ) {
					$( "#expire" ).datepicker( "option", "minDate", new Date(inst.currentYear, inst.currentMonth, parseInt(inst.currentDay) + 1 ));
				}
			);

            admin.validate = function () {
                var result = true;
                var el = $("#username");
                var text = $.trim(el.val());
                if (text == "") {
                    admin.invalid(el, "Bạn phải nhập \"tên đăng nhập\".");
                    result = false;
                } else if (!/^([-a-z0-9_-])+$/i.test(text)) {
                    admin.invalid(el, "\"Tên đăng nhập\" không được chứa ký tự đặc biệt hoặc dấu cách.");
                    result = false;
                }
                var el = $("#email");
                var text = $.trim(el.val());
                if (text == "") {
                    admin.invalid(el, "Bạn phải nhập \"email\".");
                    result = false;
                } else if (!admin.invalidEmail(text)) {
                    admin.invalid(el, "Bạn nhập \"email\" không chính xác.");
                    result = false;
                }
                var id = $("#id");
                var el = $("#password");
                var text = $.trim(el.val());
                if (id.length == 0) {
                    if (text == "") {
                        admin.invalid(el, "Bạn phải nhập \"mật khẩu\".");
                        result = false;
                    }
                    var el = $("#password2");
                    var text2 = $.trim(el.val());
                    if (text2 == "" || text2 != text) {
                        admin.invalid(el, "Bạn phải nhập giống \"mật khẩu\".");
                        result = false;
                    }
                } else if (text != "") {
                    var el = $("#password2");
                    var text2 = $.trim(el.val());
                    if (text2 == "" || text2 != text) {
                        admin.invalid(el, "Bạn phải nhập giống \"mật khẩu\".");
                        result = false;
                    }
                }
                return result;
            };
        });
    </script>
</head>

<body>
<?php require_once APPPATH . 'views/admin/modules/header.php'?>
<div class="admin_wrapper">
    <div class="admin_inner user_form">
        <h2><?php if (isset($page_heading)) echo $page_heading?></h2>

        <div id="admin_message">
            <?php if (isset($message) && $message): ?>
            <?php echo $message ?>
            <?php endif;?>
        </div>
        <div class="admin_panels" align="center">
            <a class="admin_button btn-success admin_submit_button admin_save<?php if (!isset($user->id)) echo '_new'?>_button"
               href="admin/saveUser">
                <i class="admin_button_icon">&nbsp;</i> Lưu</a>
            <a class="admin_button admin_submit_button admin_save_close_button" href="admin/saveUserAndClose">
                <i class="admin_button_icon">&nbsp;</i> Lưu và Đóng</a>
            <a class="admin_button admin_submit_button admin_save_add_button" href="admin/saveUserAndAdd">
                <i class="admin_button_icon">&nbsp;</i> Lưu và Thêm</a>
            <a class="admin_button admin_cancel_button"
               href="admin/user<?php if ($cur_page > 1) echo '/', $cur_page?>">
                <i class="admin_button_icon">&nbsp;</i> <?php if (isset($user->id)): ?>Đóng<?php else: ?>
                Hủy<?php endif;?></a>
        </div>
        <form method="post" id="admin_form" class="admin_form" action="">
            <table border="1" cellpadding="4" cellspacing="0" class="admin_table" width="100%">
                <tbody>
                <?php if (isset($user->id)): ?>
                <tr>
                    <th class="admin_label">Id:</th>
                    <td>
                        <?php echo $user->id?>
                        <input type="hidden" id="id" name="id" value="<?php echo $user->id?>">
						<input type="hidden" id="begin" name="begin" value="<?php echo $user->begin?>">
                    </td>
                </tr>
				<?php endif;?>
                <tr>
                    <th class="admin_label">Trạng thái:</th>
                    <td>
                        <label class="checkbox inline">
                            <input type="checkbox" name="status[]" value="1" autocomplete="off"
                                <?php if (isset($user->status) && ($user->status & 1)): ?> checked="checked"<?php endif;?>>
                        Kích hoạt</label>
                        <label class="checkbox inline">
                            <input type="checkbox"name="status[]" value="2" autocomplete="off"
                                <?php if (isset($user->status) && ($user->status & 2)): ?> checked="checked"<?php endif;?>>
                        Bị khóa</label>
                    </td>
                </tr>
				<tr>
					<th class="admin_label">Thời hạn:</th>
					<td>
						<input type="text" id="start" name="start" class="input-small" size="10" value="<?php if(isset($user->start) && $user->start)echo date('d-m-Y', $user->start)?>" placeholder="Bắt đầu">
						<input type="text" id="expire" name="expire" class="input-small" size="10" value="<?php if(isset($user->expire) && $user->expire)echo date('d-m-Y', $user->expire)?>" placeholder="Kết thúc">
						<button type="button" class="btn" style="margin-bottom: 10px" onclick="$('#expire').val('')"> Xóa </button>
					</td>
				</tr>
                <!--<tr>
                    <th class="admin_label"><label for="lastname">Họ và tên:</label></th>
                    <td>
                        <input type="text" id="lastname" name="lastname" size="20" maxlength="30"
                               value="<?php /*if (isset($user->lastname)) echo $user->lastname*/?>" style="width: 120px"
                               placeholder="Họ đệm">
                        <input type="text" id="firstname" name="firstname" size="10" maxlength="30"
                               value="<?php /*if (isset($user->firstname)) echo $user->firstname*/?>" style="width: 70px"
                               placeholder="Tên">
                    </td>
                </tr>-->
                <tr>
                    <th class="admin_label"><label for="username">Tên đăng nhập <span class="red">*</span>:</label></th>
                    <td>
                        <input type="text" id="username" name="username" size="30" maxlength="60"
                               value="<?php if (isset($user->username)) echo $user->username?>">
                    </td>
                </tr>
                <tr>
                    <th class="admin_label"><label for="email">Email <span class="red">*</span>:</label></th>
                    <td>
                        <input type="text" id="email" name="email" size="30" maxlength="120"
                               value="<?php if (isset($user->email)) echo $user->email?>">
                    </td>
                </tr>
                <!--<tr>
                    <th class="admin_label"><label for="mobile">Mobile:</label></th>
                    <td>
                        <input type="text" id="mobile" name="mobile" size="30" maxlength="30"
                               value="<?php /*if (isset($user->mobile)) echo $user->mobile*/?>">
                    </td>
                </tr>-->
                <tr>
                    <th class="admin_label"><label for="password">Mất khẩu<?php if (!isset($user->id) || !$user->id): ?>
                        <span class="red">*</span><?php endif;?>:</label></th>
                    <td>
                        <input type="password" id="password" name="password" size="30">
                    </td>
                </tr>
                <tr>
                    <th class="admin_label"><label for="password2">Xác nhận mất
                        khẩu<?php if (!isset($user->id) || !$user->id): ?> <span class="red">*</span><?php endif;?>
                        :</label></th>
                    <td>
                        <input type="password" id="password2" name="password2" size="30">
                    </td>
                </tr>
                <tr>
                    <th class="admin_label">Quyền:</th>
                    <td>
                        <?php
                        if (isset($user->permissions) && $user->permissions) {
                            if (!is_array($user->permissions)) $permissions = explode(',', $user->permissions);
                        } else $permissions = array();
                        foreach ($this->user->getAllPer() as $k => $per) {
                            if(is_numeric($k)) {
                                echo $per;
                            } else {
                                ?>
                                <div class="admin_check_list">
                                    <label class="checkbox">
                                        <input type="checkbox" id="per_<?php echo $k?>" name="permissions[]"
                                           autocomplete="off" value="<?php echo $k?>"
                                            <?php if (in_array($k, $permissions)): ?> checked="checked"<?php endif;?>>
                                    <?php echo $per?></label>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="2">
                        <a class="admin_button btn-success admin_submit_button admin_save<?php if (!isset($user->id)) echo '_new'?>_button"
                           href="admin/saveUser">
                            <i class="admin_button_icon">&nbsp;</i> Lưu</a>
                        <a class="admin_button admin_submit_button admin_save_close_button"
                           href="admin/saveUserAndClose">
                            <i class="admin_button_icon">&nbsp;</i> Lưu và Đóng</a>
                        <a class="admin_button admin_submit_button admin_save_add_button" href="admin/saveUserAndAdd">
                            <i class="admin_button_icon">&nbsp;</i> Lưu và Thêm</a>
                        <a class="admin_button admin_cancel_button"
                           href="admin/user<?php if ($cur_page > 1) echo '/', $cur_page?>">
                            <i class="admin_button_icon">&nbsp;</i> <?php if (isset($user->id)): ?>Đóng<?php else: ?>
                            Hủy<?php endif;?></a>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
        <?php require_once APPPATH . 'views/admin/modules/footer.php'?>
    </div>
</div>

</body>
</html>