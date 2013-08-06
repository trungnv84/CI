<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$theme->loadModule(array('name' => 'Breadcrumb_Module', 'title' => null, 'params' => null));
$CI =& get_instance();
?>
<div id="product-detail">
	<h2 class="pro-title"><?php echo $product->name?></h2>
	<div class="pro-detail">
		<div class="pro-image">
			<img src="thumb_374_364/<?php echo $product->images?>"/>
		</div>
		<div class="pro-desc">
			<b>Mô tả sản phẩm:</b><br/>
			<?php echo $product->content?>
		</div>
		<div class="pro-bottom">
			<div class="pro-code">
				Mã sản phẩm: <span class="pro-hl"><?php echo $product->code?></span>
			</div>
			<div class="pro-status">
				Trạng thái: <span class="pro-hl">Còn sản phẩm</span>
			</div>
			<div class="pro-price">
				Giá: <span class="pro-hl"><?php $CI->product->showPrice($product)?> VNĐ</span>
				<a class="pro-add-cart" href="mua-<?php echo $product->alias, '-p', $product->id, REWRITE_SUFFIX?>">Mua hàng</a>
			</div>
			<div class="pro-help">
				<?php $theme->loadModules('pro-help');?>
			</div>
		</div>
		<div style="clear: both"></div>
	</div>
</div>
<div id="pro-also">
	<?php $theme->loadModule(array(
		'name' => 'ProAlso_Module',
		'title' => 'Sản phẩm có thể mua thêm:',
		'params' => '{"cat_id":' . (isset($product->also_cat_id)?$product->also_cat_id:0) . ',"product_ids":[' . (isset($product->also_product_ids)?$product->also_product_ids:'') . '],"product_id":' . $product->id . '}'
	));?>
</div>
<div id="pro-same">

</div>