<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<h2>Đăng ký</h2>
<div id="form_message">
	<?php if(isset($message) && $message):?>
		<?php echo $message?>
	<?php endif;?>
</div>
<form method="post" action="dang-ky<?php echo REWRITE_SUFFIX?>" id="register_form">
	<?php if(USE_SESSION_TOKEN) {
		$CI =& get_instance();
		$CI->load->library('form');
		echo $CI->form->form_token('site_register');
	}?>
	<label>
		Tên đăng nhập:
		<input type="text" name="username" maxlength="60" value="<?php echo $username?>">
	</label>
	<label>
		Email:
		<input type="text" name="email" maxlength="120" value="<?php echo $email?>">
	</label>
	<label>
		Mật khẩu:
		<input type="password" name="password">
	</label>
	<label>
		Xác nhận mật khẩu:
		<input type="password" name="password2">
	</label>
	<button type="submit" class="btn">Đăng ký</button>
</form>