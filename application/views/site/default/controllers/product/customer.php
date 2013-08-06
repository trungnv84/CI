<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$theme->loadModule(array('name' => 'Breadcrumb_Module', 'title' => null, 'params' => null));
$theme->addCSS('customer.css', 'customer');
$theme->addJS('customer.js', 'customer');
$CI =& get_instance();
$CI->load->helper('form');
?>
<div id="cart">
	<!--<h2 class="cart-title">Giỏ hàng</h2>-->
	<div class="cart-detail">

		<?php echo form_open($this->config->base_url('mua-hang'. REWRITE_SUFFIX), 'id="customer_form"'); ?>

		<table cellpadding="6" cellspacing="1" style="width:100%" border="0">

			<tr>
				<th>Sản phẩm</th>
				<th style="text-align: center">Số lượng</th>
				<th style="text-align:right">Đơn giá</th>
				<th style="text-align:right">Giá thành</th>
			</tr>

			<?php $i = 1; ?>

			<?php foreach ($cart->contents() as $items): ?>

				<tr>
					<td>
						<?php echo $items['name']; ?>

						<?php if ($cart->has_options($items['rowid']) == TRUE): ?>

							<p>
								<?php foreach ($cart->product_options($items['rowid']) as $option_name => $option_value): ?>

									<strong><?php echo $option_name; ?>:</strong> <?php echo $option_value; ?><br/>

								<?php endforeach; ?>
							</p>

						<?php endif; ?>

					</td>
					<td style="text-align: center">
						<?php echo $items['qty']; ?>
					</td>
					<td class="right"><?php echo number_format($items['price'], 0); ?></td>
					<td class="right"><?php echo number_format($items['subtotal'], 0); ?></td>
				</tr>

				<?php $i++; ?>

			<?php endforeach; ?>

			<tr>
				<td colspan="2"></td>
				<td class="right"><strong>Tổng giá</strong></td>
				<td class="right"><?php echo number_format($cart->total(), 0); ?></td>
			</tr>

		</table>

        <div class="form-customer">
	        <fieldset>
		        <legend>Thông tin người đặt</legend>
		        <div class="control-group">
			        <label class="control-label" for="cus_name">Họ tên</label>
			        <div class="controls">
				        <input type="text" id="cus_name" name="cus[name]" placeholder="Họ tên">
			        </div>
		        </div>
		        <div class="control-group">
			        <label class="control-label" for="cus_address">Địa chỉ</label>
			        <div class="controls">
				        <input type="password" id="cus_address" name="cus[address]" placeholder="Địa chỉ">
			        </div>
		        </div>
		        <div class="control-group">
			        <label class="control-label" for="cus_phone">Điện thoại</label>
			        <div class="controls">
				        <input type="password" id="cus_phone" name="cus[phone]" placeholder="Điện thoại">
			        </div>
		        </div>
		        <div class="control-group">
			        <label class="control-label" for="email">Email</label>
			        <div class="controls">
				        <input type="password" id="cus_email" name="cus[email]" placeholder="Email">
			        </div>
		        </div>
	        </fieldset>
	        <fieldset>
		        <legend>Thông tin người nhận hàng</legend>
		        <div class="control-group">
			        <label class="control-label" for="cus_recipient">Họ tên</label>
			        <div class="controls">
				        <input type="text" id="cus_recipient" name="cus[recipient]" placeholder="Họ tên">
			        </div>
		        </div>
		        <div class="control-group"><!--zzz-->
			        <label class="control-label" for="cus_recipient">Địa chỉ</label>
			        <div class="controls">
				        <input type="text" id="cus_recipient" name="cus[recipient]" placeholder="Địa chỉ">
			        </div>
		        </div>
		        <div class="control-group"><!--zzz-->
			        <label class="control-label" for="cus_recipient">Điện thoại</label>
			        <div class="controls">
				        <input type="text" id="cus_recipient" name="cus[recipient]" placeholder="Điện thoại">
			        </div>
		        </div>
	        </fieldset>
        </div>

		<p class="form-btn">
			<a href="gio-hang<?php echo REWRITE_SUFFIX?>" class="btn">Giỏ hàng</a>
			<a href="mua-hang<?php echo REWRITE_SUFFIX?>" class="btn">Mua hàng</a>
		</p>

		<?php echo form_close();?>

		<div style="clear: both"></div>
	</div>
</div>