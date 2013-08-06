<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
$this->load->model('user');
$allPer = $this->user->getAllPer();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
        $(document).ready(function() {
            admin.validate = function(btn) {
                if($("input[name^=cid]:checked").length==0) {
                    var task = $.trim(btn.text());
                    $.alert("NoSelectTo"+ task, "Thông báo", "Bạn chưa chọn \"người dùng\" nào để \""+ task+ "\".",
                            "alert", 5000, $("#admin_message"));
                    return false;
                } else {
                    if(btn.attr("data-confirm")==1) {confirm
                        $.alert("DeleteConfirm", "Xác nhận",
                            "Bạn có muốn \""+ $.trim(btn.text())+ "\" các \"người dùng\" được chọn.",
                            "info", 0, false,
                            [
                                {
                                    label: " Có ",
                                    click: function() {
                                        $("#admin_form").attr("action", btn.attr("href")).submit();
                                    }
                                },
                                {label: "Không"}
                            ]);
                        return false;
                    }
					return true;
                }
            };
        });
    </script>
</head>

<body>

<?php require_once APPPATH. 'views/admin/modules/header.php'?>
<div class="admin_wrapper">
    <div class="admin_inner user_list">
        <h2><a href="admin/user<?php if($cur_page>1)echo '/', $cur_page?>"><?php if(isset($page_heading))echo $page_heading?></a></h2>
        <div id="admin_message">
            <?php if(isset($message) && $message):?>
            <div class="sys_messages_wrapper">
                <?php echo $message?>
            </div>
            <?php endif;?>
        </div>
        <form method="post" id="admin_form" class="admin_form">
            <div id="admin_top_panels" class="admin_panels">
                <div class="admin_panels">
                    <a class="admin_button btn-success admin_first_button admin_add_button" href="admin/addUser">
                        <i class="admin_button_icon">&nbsp;</i> Thêm</a>
                    <a class="admin_button admin_submit_button admin_edit_button" href="admin/editUser">
                        <i class="admin_button_icon">&nbsp;</i> Sửa</a>
                    <a class="admin_button admin_submit_button admin_publish_button" href="admin/publishUser">
                        <i class="admin_button_icon">&nbsp;</i> Mở khóa</a>
                    <a class="admin_button admin_submit_button admin_unpublish_button" href="admin/unpublishUser">
                        <i class="admin_button_icon">&nbsp;</i> Khóa</a>
                    <a class="admin_button admin_submit_button admin_delete_button" href="admin/deleteUser" data-confirm="1">
                        <i class="admin_button_icon">&nbsp;</i> Xóa</a>
                </div>
                <div class="admin_panels">
                    <input type="text" name="filter_search" id="filter_search" placeholder="Tìm kiếm" value="<?php if(isset($filter_search))echo $filter_search?>" title="Từ khóa">
                    <div class="btn-group">
                        <button class="btn tip" type="submit" title="Search"><i class="icon-search"></i></button>
                        <button class="btn tip" type="button" onclick="$('#filter_search').val('');$('#admin_form').submit();" title="Clear"><i class="icon-remove"></i></button>
                    </div>
                </div>
				<?php if(isset($pagination) && $pagination):?>
					<div class="admin_panels">
						<div class="admin_pagination">
							<?php echo $pagination?>
						</div>
					</div>
				<?php endif;?>
            </div>
            <table id="admin_table" border="1" cellpadding="4" cellspacing="0" class="admin_table" width="100%">
                <thead><tr>
                    <th width="1px">
                        <input type="checkbox" id="admin_check_all" title="Chọn tất cả">
                    </th>
                    <th width="10%">Trạng thái</th>
                    <th>Tên đăng nhập</th>
					<th>Thời hạn</th>
                    <!--<th>Họ và tên</th>-->
                    <th>Email</th>
                    <th>Quyền</th>
                    <th>ID</th>
                </tr></thead>
                <?php if($n=count($rows)):?>
                <tbody>
                    <?php foreach($rows as $k => &$row):?>
                    <tr<?php if($k%2)echo ' bgcolor="#EEEEEE"';else echo ' bgcolor="#FFFFFF"';?>>
                        <td>
                            <input type="checkbox" name="cid[]" value="<?php echo $row->id?>">
                        </td>
                        <td align="center" nowrap="nowrap">
                            <?php echo 1==$row->status&1?'<i class="admin_publish_icon" title="Enable">&nbsp;</i> ':
                            '<i class="admin_unpublish_icon">&nbsp;</i> '. ($row->status&2?'Bị khóa':'<small>Chưa kích hoạt</small>')?>
							<?php if(1==$row->status&1 && $row->begin) echo date('d-m-Y', $row->begin)?>
                        </td>
                        <td>
                            <a href="admin/editUser?id=<?php echo $row->id?>" title="Click để sửa.">
                                <?php echo $row->username?>
                            </a>
                        </td>
						<td>
							<?php echo $row->start?'<span class="'. ($row->start>TIME_NOW?'red':'blue') .'">' . date('d-m-Y', $row->start) . '</span>':'Không đặt'?> ->
							<?php echo $row->expire?'<span class="'. ($row->expire<=TIME_NOW?'red':'blue') .'">' . date('d-m-Y', $row->expire) . '</span>':'Không đặt'?>
						</td>
                        <!--<td>
                            <?php /*echo $row->lastname, ' ', $row->firstname*/?>
                        </td>-->
                        <td><?php echo $row->email?></td>
                        <td>
                            <?php
                            if($row->permissions)
                                $row->permissions = explode(',', $row->permissions);
                            else $row->permissions = array();
                            $txt = array();
                            foreach($row->permissions as $per)
                                if(isset($allPer[$per])) $txt[] = $allPer[$per];
                            echo implode(', ', $txt);
                            ?>
                        </td>
                        <td><?php echo $row->id?></td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
                <?php else:?>
                <tr>
                    <td colspan="6" align="center">
                        Không tìm thấy "người dùng" nào.
                    </td>
                </tr>
                <?php endif;?>
            </table>
        </form>
        <div class="admin_pagination">
            <?php if(isset($pagination))echo $pagination?>
        </div>
        <?php require_once APPPATH. 'views/admin/modules/footer.php'?>
    </div>
</div>

</body>
</html>