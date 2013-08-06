<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
$theme->unShiftCSS('common.css', 'common');
$theme->unShiftCSS('application/views/admin/css/alert.css', 'jAlert', true);
$theme->unShiftCSS('application/views/bootstrap/css/bootstrap.min.css', 'bootstrap', true);
$theme->unShiftJS('application/views/admin/js/alert.js', 'jAlert', true);
$theme->unShiftJS('application/views/bootstrap/js/bootstrap.min.js', 'bootstrap', true);
$theme->unShiftJS('application/views/admin/js/jquery.min.js', 'jquery', true);
$CI =& get_instance();
$CI->load->library('form');
$_sys_message = $CI->form->getMessage();
?>
<div id="wrapper" align="center">
    <div id="body">
        <div id="header">
        </div>
		<div id="nav">
			<?php $theme->loadModules('navigation')?>
		</div>
		<div id="top">
			<?php $theme->loadModules('top')?>
		</div>
		<div id="center">
			<div id="left">
				<?php $theme->loadModules('left')?>
			</div>
			<div id="main">
				<div id="component">
					<?php
					if($_sys_message) {
						echo '<div class="sys_message">', $_sys_message, '</div>';
					}
					echo $_component;
					?>
				</div>
			</div>
		</div>
        <div id="footer">
			<div id="footer-nav">
				<div style="float: left">Copyright &copy; dienhoahanoi.com </div>
				<?php $theme->loadModules('footer-nav')?>
			</div>
			<div id="site-info">
				<div id="site-company">Công ty TNHH Thương Mại Điện Hoa Hà Nội</div>
				<div id="site-contact-info">
					Địa chỉ: Số 1A/56 Doãn Kế Thiện, Cầu Giấy, Hà Nội<br/>
					ĐT: 04.6287.3140 - 04.6287.3150<br/>
					Email: info@dienhoahanoi.com
				</div>
			</div>
        </div>
    </div>
</div>
<?php if (ENVIRONMENT == 'development'):?>
<p style="text-align: center; margin-top: 50px;">Page rendered in <strong>{elapsed_time}</strong> seconds. Memory used <strong>{memory_usage}</strong></p>
<?php endif;?>