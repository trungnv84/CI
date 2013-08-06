<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
if(isset($product->cat_ids))
    if($product->cat_ids) $product->cat_ids = explode(',', $product->cat_ids);
    else unset($product->cat_ids);
$imagemanager_path = 'images' . DS;
if(defined('SITE_NAME') && SITE_NAME)
	$imagemanager_path .= SITE_NAME . DS;
$imagemanager_path .= 'product';
if(!is_dir($imagemanager_path))
	mkdir($imagemanager_path, 0777, true);
$imagemanager_path = '../../../../../../../' . str_replace(DS, '/', $imagemanager_path);
?>
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
	<script type="text/javascript" language="javascript">
		admin.imagemanager_path = "<?php echo $imagemanager_path;?>";
	</script>
    <script src="<?php echo APPFOLDER?>/views/admin/js/product_form.js" type="text/javascript" language="javascript"></script>
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
            <a class="admin_button btn-success admin_submit_button admin_save<?php if(!isset($product->id))echo '_new'?>_button" href="admin/saveProduct">
                <i class="admin_button_icon">&nbsp;</i> Lưu</a>
            <a class="admin_button admin_submit_button admin_save_close_button" href="admin/saveProductAndClose">
                <i class="admin_button_icon">&nbsp;</i> Lưu và Đóng</a>
            <a class="admin_button admin_submit_button admin_save_add_button" href="admin/saveProductAndAdd">
                <i class="admin_button_icon">&nbsp;</i> Lưu và Thêm</a>
            <a class="admin_button admin_cancel_button" href="admin/product/<?php echo $cur_page?>">
                <i class="admin_button_icon">&nbsp;</i> <?php if(isset($product->id)):?>Đóng<?php else:?>Hủy<?php endif;?></a>
        </div>
        <form id="admin_form" class="admin_form" accept-charset="utf-8" method="post" action="" enctype="multipart/form-data">
        <table border="1" cellpadding="4" cellspacing="0" class="admin_table" width="100%">
            <tbody>
                <?php if(isset($product->id)):?>
                <tr>
                    <th class="admin_label">Id:</th>
                    <td>
                        <?php echo $product->id?>
                        <input type="hidden" id="id" name="id" value="<?php echo $product->id?>">
                    </td>
                </tr>
                <?php endif;?>
                <tr>
                    <th class="admin_label" width="30%">Trạng thái:</th>
                    <td>
						<label class="radio inline">
                        <input type="radio" id="status1" name="status" value="1"
                            <?php if(!isset($product->status) || $product->status):?> checked="checked"<?php endif;?>>
                        Hiện</label>
						<label class="radio inline">
                        <input type="radio" id="status0" name="status" value="0"
                            <?php if(isset($product->status) && !$product->status):?> checked="checked"<?php endif;?>>
                        Ẩn</label>
                    </td>
                </tr>
                <tr>
                    <th class="admin_label">Thời hạn đăng:</th>
                    <td>
                        <input type="text" id="start_date" name="start_date" class="input-small" size="10" value="<?php if(isset($product->start_date) && $product->start_date)echo date('d-m-Y', $product->start_date)?>" placeholder="Bắt đầu">
                        <input type="text" id="end_date" name="end_date" class="input-small" size="10" value="<?php if(isset($product->end_date) && $product->end_date)echo date('d-m-Y', $product->end_date)?>" placeholder="Kết thúc">
						<button id="non_end" type="button" class="btn" style="margin-bottom: 10px"> Xóa </button>
                    </td>
                </tr>
                <tr>
                    <th class="admin_label"><label for="code">Mã sản phẩm:</label></th>
                    <td>
                        <input type="text" id="code" name="code" class="input-small" size="10" value="<?php if(isset($product->code))echo $product->code?>">
                    </td>
                </tr>
                <tr>
                    <th class="admin_label"><label for="name">Tên <span class="red">*</span>:</label></th>
                    <td>
                        <input type="text" id="name" name="name" class="input-xxlarge" size="50" value="<?php if(isset($product->name))echo $product->name?>">
                    </td>
                </tr>
                <tr>
                    <th class="admin_label"><label for="alias">Alias:</label></th>
                    <td>
                        <input type="text" id="alias" name="alias" class="input-xxlarge" size="50" value="<?php if(isset($product->alias))echo $product->alias?>">
                    </td>
                </tr>
                <tr>
                    <th class="admin_label"><label for="price">Giá:</label></th>
                    <td>
                        <input type="text" id="price" name="price" size="18" value="<?php if(isset($product->price) && $product->price)echo $product->price?>">
                        <span id="price_text"></span>
                    </td>
                </tr>
                <tr>
                    <th class="admin_label"><label for="discount">Khuyến mại:</label></th>
                    <td>
                        <input type="text" id="discount" name="discount" size="18" value="<?php if(isset($product->discount) && $product->discount)echo $product->discount?>">
                        <span id="discount_text"></span><br />
                        <input type="text" id="start" name="start" class="input-small" size="10" value="<?php if(isset($product->start) && $product->start)echo date('d-m-Y', $product->start)?>" placeholder="Bắt đầu">
                        <input type="text" id="expire" name="expire" class="input-small" size="10" value="<?php if(isset($product->expire) && $product->expire)echo date('d-m-Y', $product->expire)?>" placeholder="Hết hạn">
						<button id="non_expire" type="button" class="btn" style="margin-bottom: 10px">Xóa</button>
                        <span id="expire_text" class="red"></span>
                    </td>
                </tr>
                <tr>
                    <th class="admin_label"><label for="cat_ids">Nhóm <span class="red">*</span>:</label></th>
                    <td>
                        <select id="cat_ids" name="cat_ids[]" multiple="multiple" size="<?php echo min(count($categories), 15)?>" autocomplete="off">
                            <?php foreach($categories as $v):?>
                            <option value="<?php echo $v->id?>"
                                <?php if(isset($product->cat_ids) && in_array($v->id, $product->cat_ids))echo ' selected="selected"'?>
                                data-branch=",<?php if($v->branch)echo $v->branch, ',';echo $v->id?>">
                                <?php echo str_repeat('- ', $v->level+1), $v->name?>
                            </option>
                            <?php endforeach;?>
                        </select>
                        <input type="hidden" id="branches" name="branches" value="<?php if(isset($product->branches))echo $product->branches?>">
                    </td>
                </tr>
                <tr>
                    <th class="admin_label"><label for="ordering">Thứ tự:</label></th>
                    <td>
                        <input type="hidden" id="old_ordering" name="old_ordering" value="<?php echo isset($old_ordering)?$old_ordering:(isset($product->ordering)?$product->ordering:0)?>">
                        <select id="ordering" name="ordering" autocomplete="off">
                            <option value="<?php echo $orderFirst?>">- Đầu tiên -</option>
                        <?php
                            if($products) {
                                foreach($products as $key => $value) {
                                    ?>
                                    <option value="<?php echo $value->ordering?>"<?php if(isset($product->id) && $value->id==$product->id) {$oex = 1;echo ' selected="selected"';}?>><?php echo $key+1?>. <?php echo $value->name?></option>
                                    <?php
                                }
                                if(isset($product->id) && !isset($oex)) {
                                    ?>
                                    <option value="<?php echo $product->ordering?>" disabled="disabled">---</option>
                                    <option value="<?php echo $product->ordering?>" selected="selected"><?php echo $total_products-$product->ordering+1?>. <?php echo $product->name?></option>
                                    <?php if($product->ordering>2):?>
                                    <option value="<?php echo $product->ordering?>" disabled="disabled">---</option>
                                    <?php
                                    endif;
                                }
                                ?>
                                <option value="1">- Cuối cùng -</option>
                                <?php
                            }
                        ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th class="admin_label">Nhãn sản phẩm:</th>
                    <td>
                        <?php foreach($features as $key => $value):?>
						<label class="radio">
                        <input type="radio" id="feature<?php echo $key?>" name="feature" value="<?php echo $key?>"
                            <?php if(isset($product->feature) && $product->feature==$key)echo ' checked="checked"'?>>
                        <?php echo $value?></label>
                        <?php endforeach;?>
                    </td>
                </tr>
                <tr>
                    <th class="admin_label">Ảnh:</th>
                    <td>
                        <?php if(isset($product->images) && $product->images):?>
							<img style="max-width: 100px; max-height: 100px; float: left; margin-right: 5px;" src="thumb_max_100/<?php echo $product->images/*json_decode($product->images)->full_path*/?>">
							<input type="hidden" name="old_images" value="<?php echo htmlspecialchars($product->images)?>">
							<div style="clear: both"></div>
                        <?php endif;?>
                        <input type="file" name="images" size="50">
                    </td>
                </tr>
                <tr>
                    <th class="admin_label"><label for="content">Mô tả:</label></th>
                    <td>
                        <textarea id="content" name="content" rows="20" cols="80" class="tinymce"><?php if(isset($product->content))echo $product->content?></textarea>
                    </td>
                </tr>
                <tr>
                    <th class="admin_label"><label for="keywords">Keywords:</label></th>
                    <td>
                        <textarea id="keywords" name="keywords" rows="6" cols="80" class="input-xxlarge"><?php if(isset($product->keywords))echo $product->keywords?></textarea>
                    </td>
                </tr>
                <tr>
                    <th class="admin_label"><label for="description">Description:</label></th>
                    <td>
                        <textarea id="description" name="description" rows="6" cols="80" class="input-xxlarge"><?php if(isset($product->description))echo $product->description?></textarea>
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="2">
                        <a class="admin_button btn-success admin_submit_button admin_save<?php if(!isset($product->id))echo '_new'?>_button" href="admin/saveProduct">
                            <i class="admin_button_icon">&nbsp;</i> Lưu</a>
                        <a class="admin_button admin_submit_button admin_save_close_button" href="admin/saveProductAndClose">
                            <i class="admin_button_icon">&nbsp;</i> Lưu và Đóng</a>
                        <a class="admin_button admin_submit_button admin_save_add_button" href="admin/saveProductAndAdd">
                            <i class="admin_button_icon">&nbsp;</i> Lưu và Thêm</a>
                        <a class="admin_button admin_cancel_button" href="admin/product/<?php echo $cur_page?>">
                            <i class="admin_button_icon">&nbsp;</i> <?php if(isset($product->id)):?>Đóng<?php else:?>Hủy<?php endif;?></a>
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