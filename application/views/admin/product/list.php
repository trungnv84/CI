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
                            "admin/reorderProduct",
                            {
                                cur_page: <?php echo $cur_page?>,
                                order_after: order_after,
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
					$.alert(null, "Thông báo", "Bạn chưa chọn \"sản phẩm\" nào để \""+ $.trim(btn.text())+ "\".",
                        "alert", 5000, $("#admin_message"));
                    return false;
                } else {
                    if(btn.attr("data-confirm")==1) {
						$.alert(null, "Xác nhận",
                            "Bạn có muốn \""+ $.trim(btn.text())+ "\" các \"sản phẩm\" được chọn.",
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
        <h2><a href="admin/product/<?php if($cur_page>1)echo $cur_page?>"><?php if(isset($page_heading))echo $page_heading?></a></h2>
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
                    <a class="admin_button btn-success admin_first_button admin_add_button" href="admin/addProduct">
                        <i class="admin_button_icon">&nbsp;</i> Thêm</a>
                    <a class="admin_button admin_submit_button admin_edit_button" href="admin/editProduct">
                        <i class="admin_button_icon">&nbsp;</i> Sửa</a>
                    <a class="admin_button admin_branch_button" href="admin/branchProduct" title="Cần thực hiện khi có sự thay đổi cấp cha của nhóm sản phẩm.">
                        <i class="admin_button_icon">&nbsp;</i> Chia nhánh</a>
                    <a class="admin_button admin_submit_button admin_publish_button" href="admin/publishProduct">
                        <i class="admin_button_icon">&nbsp;</i> Hiện</a>
                    <a class="admin_button admin_submit_button admin_unpublish_button" href="admin/unpublishProduct">
                        <i class="admin_button_icon">&nbsp;</i> Ẩn</a>
                    <a class="admin_button admin_submit_button admin_delete_button" href="admin/deleteProduct" data-confirm="1">
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
                    <th width="100px">Trạng thái</th>
                    <th>Tên</th>
                    <th>Giá</th>
                    <th>Ngày tháng</th>
                    <th>ID</th>
					<th width="16px"></th>
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
                            <?php if($row->images):?>
                            <img class="fl" style="height: 80px; margin-right: 10px;" src="thumb_h_80/<?php echo $row->images/*json_decode($row->images)->full_path*/?>">
                            <?php endif;?>
                            <?php if($row->code)echo "[$row->code]"?>
                            <a href="admin/editProduct?id=<?php echo $row->id?>" title="Click để sửa.">
                                <?php echo $row->name?>
                            </a><br />
                            <span class="admin_slug">(Alias: <?php echo $row->alias?>)</span>
                            <div class="admin_product_categories">
                                <?php
                                if($row->cat_ids){
                                    $row->cat_ids = explode(',', $row->cat_ids);
                                    $names = array();
                                    foreach($row->cat_ids as $cat_id)
                                        $names[] = $categories[$cat_id]->name;
                                    echo 'Nhóm: <span class="admin_inline_list"><span>', implode('</span>, <span>', $names), '</span></span>';
                                }?>
                            </div>
                            <div class="admin_product_features">
                                Nhãn: <?php echo $features[$row->feature];?>
                            </div>
                        </td>
                        <td>
                            <?php
                                //echo '<textarea>',json_encode($row), '</textarea><br />';
                                if(!$row->discount || ($row->start && $row->start>TIME_NOW) || ($row->expire && $row->expire<=TIME_NOW))
                                    echo '<span style="color: blue;">', $this->form->price_format($row->price), 'đ</span>';
                                else echo $this->form->price_format($row->price), 'đ';
                            ?>
                            <?php
                            if($row->discount) {
                                if($row->discount>99) {
                                    $km = '';
                                    $discount = $this->form->price_format($row->discount);
                                } elseif($row->discount>0) {
                                    $km = '<br />Giảm: '. $row->discount. '%';
                                    $discount = $this->form->price_format($row->price*(100-$row->discount)/100);
                                } else {
                                    $km = '<br />Giảm: '. $this->form->price_format(abs($row->discount)). 'đ';
                                    $discount = $this->form->price_format($row->price+$row->discount);
                                }
                                if((!$row->start || $row->start<=TIME_NOW) && (!$row->expire || $row->expire>TIME_NOW))
                                    $discount = '<span style="color: blue;">'. $discount. 'đ</span>';
                                else $discount .= 'đ';
                                if(!$row->start || $row->start<=TIME_NOW)
                                    $start = 'Bắt đầu: ';
                                elseif($row->start)
                                    $start = '<span style="color: red;">Bắt đầu:</span> ';
                                if(!$row->expire || $row->expire>TIME_NOW)
                                    $expire = 'Hết hạn: ';
                                elseif($row->expire)
                                        $expire = '<span style="color: red;">Hết hạn:</span> ';
                                echo $km, '<br />Giá KM: ', $discount;
                                if($row->start) echo '<br />', $start, date('d-m-Y', $row->start);
                                if($row->expire) echo '<br />', $expire, date('d-m-Y', $row->expire);
                            }?>
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
						<td>
							<a href="admin/addCategory/6?alias=<?php echo $row->alias, '-p', $row->id?>">
								<img src="application/views/admin/img/menu_item.png" title="Thêm menu cho 'bài viết' này."/>
							</a>
						</td>
					</tr>
                    <?php endforeach;?>
                </tbody>
                <?php else:?>
                <tr>
                    <td colspan="8" align="center">
                        Không tìm thấy "sản phẩm" nào.
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