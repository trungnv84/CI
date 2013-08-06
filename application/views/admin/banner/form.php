<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
$this->load->helper('form');
$imagemanager_path = 'images' . DS;
if (defined('SITE_NAME') && SITE_NAME)
	$imagemanager_path .= SITE_NAME . DS;
$imagemanager_path .= 'banner';
if (!is_dir($imagemanager_path))
	mkdir($imagemanager_path, 0777, true);
$imagemanager_path = '../../../../../../../' . str_replace(DS, '/', $imagemanager_path);
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?php if (isset($browser_title)) echo $browser_title ?></title>
	<base href="<?php echo $this->config->base_url() ?>">
	<link href="<?php echo APPFOLDER ?>/views/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
	<link href="<?php echo APPFOLDER ?>/views/admin/css/cupertino/jquery-ui.custom.min.css" rel="stylesheet" type="text/css"/>
	<link href="<?php echo APPFOLDER ?>/views/admin/css/alert.css" rel="stylesheet" type="text/css"/>
	<link href="<?php echo APPFOLDER ?>/views/admin/css/admin.css" rel="stylesheet" type="text/css"/>
	<script src="<?php echo APPFOLDER ?>/views/admin/js/jquery.min.js" type="text/javascript" language="javascript"></script>
	<script src="<?php echo APPFOLDER ?>/views/bootstrap/js/bootstrap.min.js" type="text/javascript" language="javascript"></script>
	<script src="<?php echo APPFOLDER ?>/views/admin/js/jquery-ui.custom.min.js" type="text/javascript"	language="javascript"></script>
	<script src="<?php echo APPFOLDER ?>/third_party/tinymce/jscripts/tiny_mce/jquery.tinymce.js" type="text/javascript" language="javascript"></script>
	<script src="<?php echo APPFOLDER ?>/views/admin/js/alert.js" type="text/javascript" language="javascript"></script>
	<script src="<?php echo APPFOLDER ?>/views/admin/js/admin.js" type="text/javascript" language="javascript"></script>
	<script type="text/javascript" language="javascript">
		admin.imagemanager_path = "<?php echo $imagemanager_path;?>";
	</script>
	<script src="<?php echo APPFOLDER ?>/views/admin/js/banner_form.js" type="text/javascript" language="javascript"></script>
</head>

<body>
<?php require_once APPPATH . 'views/admin/modules/header.php' ?>
<div class="admin_wrapper">
	<div class="admin_inner">
		<h2><?php if (isset($page_heading)) echo $page_heading ?></h2>

		<div id="admin_message">
			<?php if (isset($message) && $message): ?>
				<?php echo $message ?>
			<?php endif; ?>
		</div>
		<div id="admin_top_panels" class="admin_panels" align="center">
			<a class="admin_button btn-success admin_submit_button admin_save<?php if (!isset($banner->id)) echo '_new' ?>_button"
			   href="admin/saveBanner">
				<i class="admin_button_icon">&nbsp;</i> Lưu</a>
			<a class="admin_button admin_submit_button admin_save_close_button" href="admin/saveBannerAndClose">
				<i class="admin_button_icon">&nbsp;</i> Lưu và Đóng</a>
			<a class="admin_button admin_submit_button admin_save_add_button" href="admin/saveBannerAndAdd">
				<i class="admin_button_icon">&nbsp;</i> Lưu và Thêm</a>
			<a class="admin_button admin_cancel_button" href="admin/banner/<?php echo $cur_page ?>">
				<i class="admin_button_icon">&nbsp;</i>
				<?php if (isset($banner->id)): ?>Đóng<?php else: ?>Hủy<?php endif; ?></a>
		</div>
		<form id="admin_form" class="admin_form" accept-charset="utf-8" method="post" action=""
			  enctype="multipart/form-data">
			<table border="1" cellpadding="4" cellspacing="0" class="admin_table" width="100%">
				<tbody>
				<?php if (isset($banner->id)): ?>
					<tr>
						<th class="admin_label">Id:</th>
						<td>
							<?php echo $banner->id ?>
							<input type="hidden" id="id" name="id" value="<?php echo $banner->id ?>">
						</td>
					</tr>
				<?php endif; ?>
				<tr>
					<th class="admin_label" width="30%">Trạng thái:</th>
					<td>
						<label class="radio inline">
							<input type="radio" id="status1" name="status" value="1"
								<?php if (!isset($banner->status) || $banner->status): ?> checked="checked"<?php endif; ?>>
							Hiện</label>
						<label class="radio inline">
							<input type="radio" id="status0" name="status" value="0"
								<?php if (isset($banner->status) && !$banner->status): ?> checked="checked"<?php endif; ?>>
							Ẩn</label>
					</td>
				</tr>
				<tr>
					<th class="admin_label">Thời hạn đăng:</th>
					<td>
						<input type="text" id="start_date" name="start_date" class="input-small" size="10"
							   value="<?php if (isset($banner->start_date) && $banner->start_date) echo date('d-m-Y', $banner->start_date) ?>"
							   placeholder="Bắt đầu">
						<input type="text" id="end_date" name="end_date" class="input-small" size="10"
							   value="<?php if (isset($banner->end_date) && $banner->end_date) echo date('d-m-Y', $banner->end_date) ?>"
							   placeholder="Kết thúc">
						<button id="non_end" type="button" class="btn" style="margin-bottom: 10px"> Xóa</button>
					</td>
				</tr>
				<tr>
					<th class="admin_label"><label for="name">Tên <span class="red">*</span>:</label></th>
					<td>
						<input type="text" id="name" name="name" class="input-xxlarge" size="50"
							   value="<?php if (isset($banner->name)) echo $banner->name ?>">
					</td>
				</tr>
				<tr>
					<th class="admin_label"><label for="alias">Link:</label></th>
					<td>
						<input type="text" id="alias" name="alias" class="input-xxlarge" size="50"
							   value="<?php if (isset($banner->alias)) echo $banner->alias ?>">
					</td>
				</tr>
				<tr>
					<th class="admin_label"><label for="cost">Chi phí:</label></th>
					<td>
						<input type="text" id="cost" name="cost" size="18"
							   value="<?php if (isset($banner->cost) && $banner->cost) echo $banner->cost ?>">
						<span id="cost_text"></span>
					</td>
				</tr>
				<tr>
					<th class="admin_label"><label for="cat_id">Nhóm <span class="red">*</span>:</label></th>
					<td>
						<select id="cat_id" name="cat_id" autocomplete="off">
							<option value="">-- Chọn nhóm banner --</option>
							<?php foreach ($categories as $v): ?>
								<option value="<?php echo $v->id ?>"
									<?php if (isset($banner->cat_id) && $v->id == $banner->cat_id) echo ' selected="selected"' ?>
										data-branch="<?php if ($v->branch) echo $v->branch, ','; echo $v->id; ?>">
									<?php echo str_repeat('- ', $v->level + 1), $v->name ?>
								</option>
							<?php endforeach; ?>
						</select>
						<input type="hidden" id="branch" name="branch"
							   value="<?php if (isset($banner->branch)) echo $banner->branch ?>">
					</td>
				</tr>
				<tr>
					<th class="admin_label"><label for="ordering">Thứ tự:</label></th>
					<td>
						<input type="hidden" id="old_ordering" name="old_ordering"
							   value="<?php echo isset($old_ordering) ? $old_ordering : (isset($banner->ordering) ? $banner->ordering : 0) ?>">
						<select id="ordering" name="ordering" autocomplete="off">
							<option value="<?php echo $orderFirst ?>">- Đầu tiên -</option>
							<?php
							if ($banners) {
								foreach ($banners as $key => $value) {
									?>
									<option
										value="<?php echo $value->ordering ?>"<?php if (isset($banner->id) && $value->id == $banner->id) {
										$oex = 1;
										echo ' selected="selected"';
									} ?>><?php echo $key + 1 ?>. <?php echo $value->name ?></option>
								<?php
								}
								if (isset($banner->id) && !isset($oex)) {
									?>
									<option value="<?php echo $banner->ordering ?>" disabled="disabled">---</option>
									<option value="<?php echo $banner->ordering ?>"
											selected="selected"><?php echo $total_banners - $banner->ordering + 1 ?>
										. <?php echo $banner->name ?></option>
									<?php if ($banner->ordering > 2): ?>
										<option value="<?php echo $banner->ordering ?>" disabled="disabled">---</option>
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
					<th class="admin_label">Loại:</th>
					<td>
						<?php echo form_dropdown('type', Banner::$_types, (isset($banner->type)?$banner->type:null), 'id="type"');?>
					</td>
				</tr>
				<tr>
					<th class="admin_label">File:</th>
					<td>
						<?php if (isset($banner->images) && $banner->images):
							switch(strtolower(substr($banner->images, -3))) {
								case 'swf':
									$size = getimagesize(APPPATH . 'images' . DS . $banner->images);
							?>
							<embed style="max-height: 100px; float: left; margin-right: 5px;" width="<?php echo ceil($size[0]*100/$size[1])?>" height="100" src="images/<?php echo $banner->images;?>"/>';
							<?php
									break;
								default:
							?>
							<img style="max-width: 100px; max-height: 100px; float: left; margin-right: 5px;" src="thumb_max_100/<?php echo $banner->images;?>">
							<?php }?>
							<input type="hidden" name="old_images" value="<?php echo htmlspecialchars($banner->images) ?>">
							<div style="clear: both"></div>
						<?php endif; ?>
						<input type="file" name="images" size="50">
					</td>
				</tr>
				<tr>
					<th class="admin_label"><label for="content">Nội dung:</label></th>
					<td>
						<textarea id="content" name="content" rows="20" cols="80"
								  class="tinymce"><?php if (isset($banner->content)) echo $banner->content ?></textarea>
					</td>
				</tr>
				<tr>
					<th class="admin_label"><label for="keywords">Keywords:</label></th>
					<td>
						<textarea id="keywords" name="keywords" rows="6" cols="80"
								  class="input-xxlarge"><?php if (isset($banner->keywords)) echo $banner->keywords ?></textarea>
					</td>
				</tr>
				<tr>
					<th class="admin_label"><label for="description">Description:</label></th>
					<td>
						<textarea id="description" name="description" rows="6" cols="80"
								  class="input-xxlarge"><?php if (isset($banner->description)) echo $banner->description ?></textarea>
					</td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<a class="admin_button btn-success admin_submit_button admin_save<?php if (!isset($banner->id)) echo '_new' ?>_button"
						   href="admin/saveBanner">
							<i class="admin_button_icon">&nbsp;</i> Lưu</a>
						<a class="admin_button admin_submit_button admin_save_close_button"
						   href="admin/saveBannerAndClose">
							<i class="admin_button_icon">&nbsp;</i> Lưu và Đóng</a>
						<a class="admin_button admin_submit_button admin_save_add_button" href="admin/saveBannerAndAdd">
							<i class="admin_button_icon">&nbsp;</i> Lưu và Thêm</a>
						<a class="admin_button admin_cancel_button" href="admin/banner/<?php echo $cur_page ?>">
							<i class="admin_button_icon">&nbsp;</i>
							<?php if (isset($banner->id)): ?>Đóng<?php else: ?>Hủy<?php endif; ?></a>
					</td>
				</tr>
				</tbody>
			</table>
		</form>
		<?php require_once APPPATH . 'views/admin/modules/footer.php' ?>
	</div>
</div>

</body>
</html>