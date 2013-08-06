<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php if(isset($browser_title))echo $browser_title?></title>
    <base href="<?php echo $this->config->base_url()?>">
	<link href="<?php echo APPFOLDER?>/views/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo APPFOLDER?>/views/admin/css/alert.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo APPFOLDER?>/views/admin/css/admin.css" rel="stylesheet" type="text/css" />
	<script src="<?php echo APPFOLDER?>/views/admin/js/jquery.min.js" type="text/javascript" language="javascript"></script>
	<script src="<?php echo APPFOLDER?>/views/bootstrap/js/bootstrap.min.js" type="text/javascript" language="javascript"></script>
	<script src="<?php echo APPFOLDER?>/views/admin/js/alert.js" type="text/javascript" language="javascript"></script>
	<script src="<?php echo APPFOLDER?>/views/admin/js/admin.js" type="text/javascript" language="javascript"></script>
    <script type="text/javascript" language="javascript">
        admin.elemFocus = "#name";
        $(document).ready(function() {
            $("#parent_id").change(function(){
                var self = $(this);
                var option = self.find("option[value="+ self.val()+ "]");
                $("#branch").val(option.attr("data-branch"));
                $("#level").val(option.attr("data-level"));
            });
            admin.dynamic_order_select('parent_id', 'ordering', 'category_Controller/get_ordering?section_id=<?php echo $section_id?>');
            admin.validate = function() {
                var result = true;
                var el = $("#name");
                var name = $.trim(el.val());
                if(name=="") {
                    admin.invalid(el, "Bạn phải nhập tên.");
                    result = false;
                }
                return result;
            };
        });
    </script>
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
        <div class="admin_panels" align="center">
            <a class="admin_button btn-success admin_submit_button admin_save<?php if(!isset($category->id))echo '_new'?>_button" href="admin/saveCategory">
                <i class="admin_button_icon">&nbsp;</i> Lưu</a>
            <a class="admin_button admin_submit_button admin_save_close_button" href="admin/saveCategoryAndClose">
                <i class="admin_button_icon">&nbsp;</i> Lưu và Đóng</a>
            <a class="admin_button admin_submit_button admin_save_add_button" href="admin/saveCategoryAndAdd">
                <i class="admin_button_icon">&nbsp;</i> Lưu và Thêm</a>
            <a class="admin_button admin_cancel_button" href="admin/category/<?php echo $section_id?>/<?php echo $cur_page?>">
                <i class="admin_button_icon">&nbsp;</i> <?php if(isset($category->id)):?>Đóng<?php else:?>Hủy<?php endif;?></a>
        </div>
        <form method="post" id="admin_form" class="admin_form" action="">
        <input type="hidden" id="section_id" name="section_id" value="<?php echo $section_id?>">
        <table border="1" cellpadding="4" cellspacing="0" class="admin_table" width="100%">
            <tbody>
                <?php if(isset($category->id)):?>
                <tr>
                    <th class="admin_label">Id:</th>
                    <td>
                        <?php echo $category->id?>
                        <input type="hidden" id="id" name="id" value="<?php echo $category->id?>">
                        <input type="hidden" id="old_branch" name="old_branch" value="<?php echo isset($old_branch)?$old_branch:(isset($category->branch)?$category->branch:'')?>">
                    </td>
                </tr>
                <?php endif;?>
                <tr>
                    <th class="admin_label" width="30%">Trạng thái:</th>
                    <td>
                        <label for="status1" class="radio inline">
							<input type="radio" id="status1" name="status" value="1"
								<?php if(!isset($category->status) || $category->status):?> checked="checked"<?php endif;?>>
							Hiện</label>
                        <label for="status0" class="radio inline">
							<input type="radio" id="status0" name="status" value="0"
								<?php if(isset($category->status) && !$category->status):?> checked="checked"<?php endif;?>>
							Ẩn</label>
                    </td>
                </tr>
                <tr>
                    <th class="admin_label"><label for="name">Tên <span class="red">*</span>:</label></th>
                    <td>
                        <input type="text" id="name" name="name" class="input-xxlarge" size="30" value="<?php if(isset($category->name))echo $category->name?>">
                    </td>
                </tr>
                <tr>
                    <th class="admin_label"><label for="alias">Alias:</label></th>
                    <td>
                        <input type="text" id="alias" name="alias" class="input-xxlarge" size="30" value="<?php if(isset($category->alias))echo $category->alias?>">
                    </td>
                </tr>
                <tr>
                    <th class="admin_label"><label for="parent_id">Cấp trên:</label></th>
                    <td>
                        <select id="parent_id" name="parent_id" autocomplete="off">
                            <option value="0" data-level="0">- Không có -</option>
                            <?php foreach($categories as $v):?>
                            <option value="<?php echo $v->id?>"
                                <?php if(isset($category->parent_id) && $v->id==$category->parent_id) echo ' selected="selected"'?>
                                data-branch="<?php echo $v->branch?$v->branch.','.$v->id:$v->id?>"
                                data-level="<?php echo $v->level+1?>">
                                <?php echo str_repeat('- ', $v->level+1), $v->name?>
                            </option>
                            <?php endforeach;?>
                        </select>
                        <input type="hidden" id="branch" name="branch" value="<?php if(isset($category->branch))echo $category->branch?>">
                        <input type="hidden" id="level" name="level" value="<?php if(isset($category->level))echo $category->level?>">
                    </td>
                </tr>
                <tr>
                    <th class="admin_label"><label for="old_ordering">Thứ tự:</label></th>
                    <td>
                        <input type="hidden" id="old_ordering" name="old_ordering" value="<?php echo isset($old_ordering)?$old_ordering:(isset($category->ordering)?$category->ordering:0)?>">
                        <select id="ordering" name="ordering" autocomplete="off">
                            <option value="<?php echo $orderFirst?>">- Đầu tiên -</option>
                            <?php foreach($orderings as $k => $v):?>
                            <option value="<?php echo $v->ordering?>"<?php if(isset($category->id) && $v->id==$category->id) echo ' selected="selected"'?>>
                                <?php echo $k+1, '. ', $v->name?>
                            </option>
                            <?php endforeach;?>
                            <?php if(count($orderings)):?>
                                <option value="<?php echo $orderLast?>"<?php if(!isset($category->ordering)) echo ' selected="selected"'?>>- Cuối cùng -</option>
                            <?php endif;?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th class="admin_label"><label for="keywords">Keywords:</label></th>
                    <td>
                        <textarea id="keywords" name="keywords" rows="4" class="input-xxlarge"><?php if(isset($category->keywords))echo $category->keywords?></textarea>
                    </td>
                </tr>
                <tr>
                    <th class="admin_label"><label for="description">Description:</label></th>
                    <td>
                        <textarea id="description" name="description" rows="4" class="input-xxlarge"><?php if(isset($category->description))echo $category->description?></textarea>
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="2">
                        <a class="admin_button btn-success admin_submit_button admin_save<?php if(!isset($category->id))echo '_new'?>_button" href="admin/saveCategory">
                            <i class="admin_button_icon">&nbsp;</i> Lưu</a>
                        <a class="admin_button admin_submit_button admin_save_close_button" href="admin/saveCategoryAndClose">
                            <i class="admin_button_icon">&nbsp;</i> Lưu và Đóng</a>
                        <a class="admin_button admin_submit_button admin_save_add_button" href="admin/saveCategoryAndAdd">
                            <i class="admin_button_icon">&nbsp;</i> Lưu và Thêm</a>
                        <a class="admin_button admin_cancel_button" href="admin/category/<?php echo $section_id?>/<?php echo $cur_page?>">
                            <i class="admin_button_icon">&nbsp;</i> <?php if(isset($category->id)):?>Đóng<?php else:?>Hủy<?php endif;?></a>
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