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
            $("#sortable").sortable({
                axis: "y",
                revert: 150,
                distance: 10,
                cursor: "move",
                helper: function(event, el) {
                    admin.sortableid =  el.index();
                    el.css("display", "");
                    return el;
                },
                forceHelperSize: false,
                handle: "td.admin_order_position",
                //forcePlaceholderSize: true,
                placeholder: "admin-placeholder-highlight",
                start: function(event, ui) {
                    var id = ui.item.attr("data-id");
                    var branch = $.trim(ui.item.attr("data-branch"));
                    if(branch!="") branch += "-";
                    branch += id;
                    $("#sortable").find("tr[data-branch|="+ branch+ "]").hide();
                    ui.placeholder.html("<td colspan='5'> </td>").height(ui.item.height());
                },
                /*change: function(event, ui) {
                    console.log("change");
                },
                beforeStop: function(event, ui) {
                    console.log("beforeStop");
                },
                update: function(event, ui) {
                    console.log("update");
                },
                deactivate: function(event, ui) {
                    console.log("deactivate");
                },*/
                stop: function(event, ui) {
                    //console.log(ui);
                    var self = $(this);
                    self.sortable("disable");
                    self.disableSelection();

                    var id = ui.item.attr("data-id");
                    var branch = $.trim(ui.item.attr("data-branch"));
                    var branch_children = branch;
                    if(branch!="") branch_children += "-";
                    branch_children += id;

                    if((ui.item.index()-ui.item.prevAll("tr[data-branch|="+ branch_children+ "]").length)!=admin.sortableid) {
                        $("#admin_table").fadeTo(1000, 0.3);
                        var item_next = ui.item.next("tr");
                        if(item_next.length==0) {
                            item_next = ui.item.prev("tr");
                            var order_after = parseInt(item_next.find("td.admin_order_position").first().text())+1;
                        } else
                            var order_after = parseInt(item_next.find("td.admin_order_position").first().text());
                        var branch_after = item_next.attr("data-branch");
                        $.post(
                            "admin/reorderCategory/<?php echo $section_id?>",
                            {
                                section_id: <?php echo $section_id?>,
                                cur_page: <?php echo $cur_page?>,
                                order_after: order_after,
                                branch_after: branch_after.replace('-', ','),
                                branch: branch.replace('-', ','),
                                id: id
                            },
                            function(data){
                                if(data.status==1) {
									$.alert(null, null, "Sắp xếp thành công.", "success");
                                    self.html(data.html);
                                    $("#admin_table").stop(true).fadeTo(0, 1);
                                    self.sortable("refresh");
                                    self.sortable("enable");
                                    self.enableSelection();
                                } else {
									$.alert(null, "Thông báo lỗi", "Có lỗi trong quá trình sắp xếp.", "error", 0, null,
                                        [{
                                            label: "Tải lại trang",
                                            click: function(){
                                                location.href = location.href;
                                            }
                                        }]
                                    );
                                }
                            },
                            "json"
                        );
                    } else {
						$.alert(null, null, "Thứ tự không thay đổi.", "message");
                        self.sortable("enable");
                        self.enableSelection();
                    }

                    branch_children = self.find("tr[data-branch|="+ branch_children+ "]");
                    if(branch_children.length>0) {
                        branch_children.detach().insertAfter(ui.item);
                        branch_children.css("display", "");
                    }

                    self.find("tr:even").attr("bgcolor", "#FFFFFF");
                    self.find("tr:odd").attr("bgcolor", "#EEEEEE");
                    ui.item.effect("highlight", 1500);
                }
            });
            admin.validate = function(btn) {
                if($("input[name^=cid]:checked").length==0) {
					$.alert(null, "Thông báo", "Bạn chưa chọn \"<?php echo $category_type?>\" nào để \""+ $.trim(btn.text())+ "\".",
                        "alert", 5000, $("#admin_message"));
                    return false;
                } else {
                    if(btn.attr("data-confirm")==1) {
						$.alert(null, "Xác nhận",
                            "Bạn có muốn \""+ $.trim(btn.text())+ "\" các \"<?php echo $category_type?>\" được chọn.",
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
    <div class="admin_inner">
        <h2><a href="admin/category/<?php echo $section_id?>/<?php if($cur_page>1)echo $cur_page?>"><?php if(isset($page_heading))echo $page_heading?></a></h2>
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
                    <a class="admin_button btn-success admin_first_button admin_add_button" href="admin/addCategory/<?php echo $section_id?>">
                        <i class="admin_button_icon">&nbsp;</i> Thêm</a>
                    <a class="admin_button admin_submit_button admin_edit_button" href="admin/editCategory/<?php echo $section_id?>">
                        <i class="admin_button_icon">&nbsp;</i> Sửa</a>
                    <a class="admin_button admin_submit_button admin_publish_button" href="admin/publishCategory/<?php echo $section_id?>">
                        <i class="admin_button_icon">&nbsp;</i> Hiện</a>
                    <a class="admin_button admin_submit_button admin_unpublish_button" href="admin/unpublishCategory/<?php echo $section_id?>">
                        <i class="admin_button_icon">&nbsp;</i> Ẩn</a>
                    <a class="admin_button admin_submit_button admin_delete_button" href="admin/deleteCategory/<?php echo $section_id?>" data-confirm="1">
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
                    <?php if($sortable):?>
                    <th class="admin_order_heading" title="Thứ tự">Thứ tự</th>
                    <?php endif;?>
                    <th width="1px">
                        <input type="checkbox" id="admin_check_all" title="Chọn tất cả">
                    </th>
                    <th width="10%">Trạng thái</th>
                    <th>Tên</th>
                    <th>ID</th>
                </tr></thead>
                <?php if($n=count($rows)):?>
                <tbody<?php if($sortable)echo' id="sortable"'?>>
                    <?php foreach($rows as $k => &$row):?>
                    <tr<?php if($k%2)echo ' bgcolor="#EEEEEE"';else echo ' bgcolor="#FFFFFF"';?>
                        data-id="<?php echo $row->id?>"
                        data-level="<?php echo $row->level?>"
                        data-branch="<?php echo str_replace(',', '-', $row->branch)?>">
                        <?php if($sortable):?>
                        <td class="admin_order_position" align="center" title="(<?php echo $row->ordering?>) Kéo thả để thay đổi thứ tự.">
                            <?php echo $row->ordering?>
                        </td>
                        <?php endif;?>
                        <td>
                            <input type="checkbox" name="cid[]" value="<?php echo $row->id?>">
                        </td>
                        <td align="center">
                            <?php echo $row->status?'<i class="admin_publish_icon" title="Hiện">&nbsp;</i> Hiện':'<i class="admin_unpublish_icon" title="Ẩn">&nbsp;</i> Ẩn'?>
                        </td>
                        <td>
                            <?php if($row->level):?>
                                <span class="admin_gi">
                                    <?php echo str_repeat('— ', $row->level)?>
                                </span>
                            <?php endif;?>
                            <a href="admin/editCategory/<?php echo $section_id?>?id=<?php echo $row->id?>" title="Click để sửa.">
                                <?php echo $row->name?>
                            </a>
                            <span class="admin_slug">(Alias: <?php echo $row->alias?>)</span>
							<?php if($hasMenu):?>
								<span style="float: right;">
									<a href="admin/addCategory/6?alias=<?php echo $row->alias, '-', $hasMenu, $row->id?>">
										<img src="application/views/admin/img/menu_item.png" title="Thêm menu cho '<?php echo $category_type?>' này."/>
									</a>
								</span>
							<?php endif;?>
                        </td>
                        <td><?php if($row->branch) echo $row->branch, ','; echo $row->id?></td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
                <?php else:?>
                <tr>
                    <td colspan="5" align="center">
                        Không tìm thấy "<?php echo $category_type?>" nào.
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