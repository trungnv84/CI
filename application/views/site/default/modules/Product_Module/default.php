<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
echo '<h3 class="mod-title">', $title, '</h3>';
$CI =& get_instance();
foreach($products as $product) {
	?>
	<div class="product">
		<div class="pro-image">
			<img src="thumb_124_122/<?php echo $product->images?>"/>
		</div>
		<div class="pro-code">Mã SP: <?php echo $product->code?></div>
		<div class="pro-price"><?php $CI->product->showPrice($product)?> VNĐ</div>
		<div class="pro-detail">
			<a href="<?php echo $product->alias, '-p', $product->id, REWRITE_SUFFIX?>">Chi tiết</a>
			<a href="mua-<?php echo $product->alias, '-p', $product->id, REWRITE_SUFFIX?>">Mua hàng</a>
		</div>
	</div>
	<?php
}
