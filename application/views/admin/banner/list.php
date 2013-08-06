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
                placeholder: "admin-placeholder-highlight",
                start: function(event, ui) {
                    ui.placeholder.html("<td colspan='7'> </td>").height(ui.item.height());
                },
                stop: function(event, ui) {
                    var self = $(this);
                    self.sortable("disable");
                    self.disableSelection();

                    if(ui.item.index()!=admin.sortableid) {
                        $("#admin_table").fadeTo(1000, 0.3);
                        var item_next = ui.item.prev("tr");
                        if(item_next.length==0) {
                            item_next = ui.item.next("tr");
                            var order_after = parseInt(item_next.find("td.admin_order_position").first().html())+1;
                        } else
                            var order_after = parseInt(item_next.find("td.admin_order_position").first().html());
                        var id = ui.item.attr("data-id");
                        $.post(
                            "admin/reorderBanner",
                            {
                                cur_page: <?php echo $cur_page?>,
                                order_after: order_after,
								cat_id: <?php echo $filter_cat_id?>,
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

                    self.find("tr:even").attr("bgcolor", "#FFFFFF");
                    self.find("tr:odd").attr("bgcolor", "#EEEEEE");
                    ui.item.effect("highlight", 1500);
                }
            });
            admin.validate = function(btn) {
                if($("input[name^=cid]:checked").length==0) {
					$.alert(null, "Thông báo", "Bạn chưa chọn \"banner\" nào để \""+ $.trim(btn.text())+ "\".",
                        "alert", 5000, $("#admin_message"));
                    return false;
                } else {
                    if(btn.attr("data-confirm")==1) {
						$.alert(null, "Xác nhận",
                            "Bạn có muốn \""+ $.trim(btn.text())+ "\" các \"banner\" được chọn.",
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
        <h2><a href="admin/banner/<?php if($cur_page>1)echo $cur_page?>"><?php if(isset($page_heading))echo $page_heading?></a></h2>
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
                    <a class="admin_button btn-success admin_first_button admin_add_button" href="admin/addBanner">
                        <i class="admin_button_icon">&nbsp;</i> Thêm</a>
                    <a class="admin_button admin_submit_button admin_edit_button" href="admin/editBanner">
                        <i class="admin_button_icon">&nbsp;</i> Sửa</a>
                    <a class="admin_button admin_branch_button" href="admin/branchBanner" title="Cần thực hiện khi có sự thay đổi cấp cha của nhóm banner.">
                        <i class="admin_button_icon">&nbsp;</i> Chia nhánh</a>
                    <a class="admin_button admin_submit_button admin_publish_button" href="admin/publishBanner">
                        <i class="admin_button_icon">&nbsp;</i> Hiện</a>
                    <a class="admin_button admin_submit_button admin_unpublish_button" href="admin/unpublishBanner">
                        <i class="admin_button_icon">&nbsp;</i> Ẩn</a>
                    <a class="admin_button admin_submit_button admin_delete_button" href="admin/deleteBanner" data-confirm="1">
                        <i class="admin_button_icon">&nbsp;</i> Xóa</a>
                </div>
                <div class="admin_panels">
                    <input type="text" id="filter_search" name="filter_search" placeholder="Tìm kiếm" value="<?php if(isset($filter_search))echo $filter_search?>" title="Từ khóa">
					&nbsp;
					<select id="filter_cat_id" name="filter_cat_id" style="margin-bottom: 0">
						<option value="">- Chọn nhóm -</option>
						<?php foreach($categories as &$category):?>
							<option value="<?php echo $category->id;?>"<?php if($category->id==$filter_cat_id) echo ' selected="selected"';?>>
								<?php echo $category->name;?>
							</option>
						<?php endforeach;?>
					</select>
                    <div class="btn-group">
                        <button class="btn tip" type="submit" title="Search"><i class="icon-search"></i></button>
                        <button class="btn tip" type="button" onclick="$('#filter_search').val('');$('#filter_cat_id').val('');$('#admin_form').submit();" title="Clear"><i class="icon-remove"></i></button>
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
                    <th width="100px">Trạng thái</th>
                    <th>Tên</th>
                    <th>Ngày tháng</th>
                    <th>ID</th>
                </tr></thead>
                <?php if(count($rows)):?>
                <tbody<?php if($sortable)echo' id="sortable"'?>>
                    <?php foreach($rows as $k => &$row):?>
                    <tr<?php if($k%2)echo ' bgcolor="#EEEEEE"';else echo ' bgcolor="#FFFFFF"';?> data-id="<?php echo $row->id?>">
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
                            <?php
							if($row->images):
								switch($row->type) {
									case 0:
							?>
							<img class="fl" style="height: 80px; margin-right: 10px;" src="thumb_h_80/<?php echo $row->images;?>">
							<?php
										break;
									case 1:
							?>
							<embed class="fl" style="height: 80px; margin-right: 10px;" width="80" height="80" src="images/<?php echo $row->images;?>"/>';
                            <?php
										break;
								}
							endif;
							?>
                            <a href="admin/editBanner?id=<?php echo $row->id?>" title="Click để sửa.">
                                <?php echo $row->name?>
                            </a><br />
                            <span class="admin_slug">(Link: <?php echo $row->alias?>)</span>
                            <div class="admin_banner_categories">
								Nhóm: <span class="admin_inline_list"><span>
									<?php echo $categories[$row->cat_id]->name;?>
								</span></span>
                            </div>
							Giá: <?php echo $this->form->price_format($row->cost);?> đ
                        </td>
                        <td>
                            Bắt đầu:&nbsp;
                            <?php
                            if($row->start_date) {
                                echo date('d-m-Y', $row->start_date);
                                if($row->start_date>TIME_NOW) echo ' <span style="color: red;">[chưa tới]</span>';
                            } else
                                echo "không đặt"
                            ?><br />
                            Kết thúc:
                            <?php
                            if($row->end_date) {
                                echo date('d-m-Y', $row->end_date);
                                if($row->end_date<TIME_NOW) echo ' <span style="color: red;">[hết hạn]</span>';
                            } else
                                echo "không có";
                            ?>
                            <?php if($row->create_date) echo '<br />Tạo: ', date('H:i d-m-Y', $row->create_date)?>
                            <?php if($row->modify_date) echo '<br />Sửa: ', date('H:i d-m-Y', $row->modify_date)?>
                        </td>
                        <td><?php echo $row->id?></td>
					</tr>
                    <?php endforeach;?>
                </tbody>
                <?php else:?>
                <tr>
                    <td colspan="8" align="center">
                        Không tìm thấy "banner" nào.
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