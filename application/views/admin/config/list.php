<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php if(!defined('BASEPATH')) exit('No direct script access allowed')?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php if(isset($browser_title))echo $browser_title?></title>
    <base href="<?php echo $this->config->base_url()?>">
	<link href="<?php echo APPFOLDER?>/views/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo APPFOLDER?>/views/admin/css/cupertino/jquery-ui.custom.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo APPFOLDER?>/views/admin/css/alert.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo APPFOLDER?>/views/admin/css/admin.css" rel="stylesheet" type="text/css" />
	<script src="<?php echo APPFOLDER?>/views/admin/js/jquery.min.js" type="text/javascript" language="javascript"></script>
	<script src="<?php echo APPFOLDER?>/views/bootstrap/js/bootstrap.min.js" type="text/javascript" language="javascript"></script>
	<script src="<?php echo APPFOLDER?>/views/admin/js/alert.js" type="text/javascript" language="javascript"></script>
	<script src="<?php echo APPFOLDER?>/views/admin/js/jquery-ui.custom.min.js" type="text/javascript" language="javascript"></script>
	<script src="<?php echo APPFOLDER?>/views/admin/js/admin.js" type="text/javascript" language="javascript"></script>
    <script type="text/javascript" language="javascript">
        $(document).ready(function() {
            admin.validate = function(btn) {
                if($("input[name^=cid]:checked").length==0) {
					$.alert(null, "Thông báo", "Bạn chưa chọn \"cấu hình\" nào để \""+ $.trim(btn.text())+ "\".",
                        "alert", 5000, $("#admin_message"));
                    return false;
                } else {
                    if(btn.attr("data-confirm")==1) {
						$.alert(null, "Xác nhận",
                            "Bạn có muốn \""+ $.trim(btn.text())+ "\" các \"cấu hình\" được chọn.",
                            "info", 0, null,
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
                    } else return true;
                }
            };
        });
    </script>
</head>

<body>

<?php require_once APPPATH. 'views/admin/modules/header.php'?>
<div class="admin_wrapper">
    <div class="admin_inner">
        <h2><a href="admin/config"><?php if(isset($page_heading))echo $page_heading?></a></h2>
        <div id="admin_message">
            <?php if(isset($message) && $message):?>
            <div class="sys_messages_wrapper">
                <?php echo $message?>
            </div>
            <?php endif;?>
        </div>
        <form method="post" id="admin_form" class="admin_form" action="">
            <div id="admin_top_panels" class="admin_panels">
                <div class="admin_panels">
                    <a class="admin_button admin_first_button admin_submit_button admin_edit_button" href="admin/editConfig">
                        <i class="admin_button_icon">&nbsp;</i> Sửa</a>
                </div>
                <div class="admin_panels">
                    <input type="text" name="filter_search" id="filter_search" placeholder="Tìm kiếm" value="<?php if(isset($filter_search))echo $filter_search?>" title="Từ khóa">
                    <div class="btn-group">
                        <button class="btn tip" type="submit" title="Search"><i class="icon-search"></i></button>
                        <button class="btn tip" type="button" onclick="$('#filter_search').val('');$('#admin_form').submit();" title="Clear"><i class="icon-remove"></i></button>
                    </div>
                </div>
            </div>
            <table id="admin_table" border="1" cellpadding="4" cellspacing="0" class="admin_table" width="100%">
                <thead><tr>
                    <th width="1px"></th>
                    <th>Tên</th>
                    <th>Giá trị</th>
                </tr></thead>
                <?php if(count($rows)):?>
                <tbody>
                    <?php foreach($rows as $k => &$row):?>
                    <tr>
                        <td>
                            <input type="checkbox" name="cid[]" value="<?php echo $k?>">
                        </td>
                        <td style="white-space: nowrap">
                            <a href="admin/editConfig?id=<?php echo $k?>" title="Click để sửa.">
                                <?php echo $row['name']?>
                            </a><br />
                            <span class="admin_slug"><?php echo $k?></span>
                        </td>
						<td>
							<?php echo constant($k)?>
						</td>
					</tr>
                    <?php endforeach;?>
                </tbody>
                <?php else:?>
                <tr>
                    <td colspan="4" align="center">
                        Không tìm thấy "cầu hình" nào.
                    </td>
                </tr>
                <?php endif;?>
            </table>
        </form>
        <?php require_once APPPATH. 'views/admin/modules/footer.php'?>
    </div>
</div>

</body>
</html>