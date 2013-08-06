<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
$theme->addCSS('application/views/admin/css/tooltip.css', 'jTooltip');
$theme->addCSS('application/views/admin/css/valid.css', 'jValid');
$theme->addJS('dropdown_inline.js', 'dropdown_inline');
$theme->addJS('application/views/admin/js/tooltip.js', 'jTooltip');
$theme->addJS('application/views/admin/js/valid.js', 'jValid');
$theme->addJS('login_module.js', 'login_module');
?>
<div id="login">
	<?php if ($username) { ?>
		Xin chào <span id="username_highlight"><?php echo $username ?>!</span> <a href="dang-xuat<?php echo REWRITE_SUFFIX?>">Đăng xuất</a>.
	<?php } else { ?>
		<small>Xin chào <span id="username_highlight">Khách!</span> bạn có thể</small>
		<span class="dropdown-inline">
			<span class="dropdown-text"><small>Đăng nhập</small></span>
			<ul class="dropdown-menu">
				<li>
					<form method="post" action="dang-nhap<?php echo REWRITE_SUFFIX?>" id="mod_login_form">
						<?php if(USE_SESSION_TOKEN) {
							$CI =& get_instance();
							$CI->load->library('form');
							echo $CI->form->form_token('site_login');
						}?>
						<label>
							Tên đăng nhập:
							<input type="text" name="username" maxlength="60">
						</label>
						<label>
							Mật khẩu:
							<input type="password" name="password">
						</label>
						<button type="submit" class="btn">Đăng nhập</button>
					</form>
				</li>
			</ul>
		</span>
		<small>hoặc</small>
		<span class="dropdown-inline">
			<span class="dropdown-text"><small>Đăng ký</small></span>
			<ul class="dropdown-menu">
				<li>
					<form method="post" action="dang-ky<?php echo REWRITE_SUFFIX?>" id="mod_register_form">
						<?php if(USE_SESSION_TOKEN) {
							echo $CI->form->form_token('site_register');
						}?>
						<label>
							Tên đăng nhập:
							<input type="text" name="username" maxlength="60">
						</label>
						<label>
							Email:
							<input type="text" name="email" maxlength="120">
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
				</li>
			</ul>
		</span>
		<small>1 tài khoản mới.</small>
	<?php } ?>
</div>