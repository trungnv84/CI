<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<div style="">
	Đăng ký thành công! <br/>
	<?php if (USER_NEED_ACTIVE):?>
		<a href="<?php echo $active_link?>">Kích hoạt tài khoản.</a>
	<?php endif;?>
</div>