<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php if(!defined('BASEPATH')) exit('No direct script access allowed')?>
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
                    $.alert(null, "Thông báo", "Bạn chưa chọn \"bài viết\" nào để \""+ $.trim(btn.text())+ "\".",
                        "alert", 5000, $("#admin_message"));
                    return false;
                } else {
                    if(btn.attr("data-confirm")==1) {
						$.alert(null, "Xác nhận",
                            "Bạn có muốn \""+ $.trim(btn.text())+ "\" các \"bài viết\" được chọn.",
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
        <h2><a href="admin/article/<?php if($cur_page>1)echo $cur_page?>"><?php if(isset($page_heading))echo $page_heading?></a></h2>
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
                    <a class="admin_button btn-success admin_first_button admin_add_button" href="admin/addArticle">
                        <i class="admin_button_icon">&nbsp;</i> Thêm</a>
                    <a class="admin_button admin_submit_button admin_edit_button" href="admin/editArticle">
                        <i class="admin_button_icon">&nbsp;</i> Sửa</a>
                    <a class="admin_button admin_branch_button" href="admin/branchArticle" title="Cần thực hiện khi có sự thay đổi cấp cha của loại tin.">
                        <i class="admin_button_icon">&nbsp;</i> Chia nhánh</a>
                    <a class="admin_button admin_submit_button admin_publish_button" href="admin/publishArticle">
                        <i class="admin_button_icon">&nbsp;</i> Hiện</a>
                    <a class="admin_button admin_submit_button admin_unpublish_button" href="admin/unpublishArticle">
                        <i class="admin_button_icon">&nbsp;</i> Ẩn</a>
                    <a class="admin_button admin_submit_button admin_delete_button" href="admin/deleteArticle" data-confirm="1">
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
                    <th width="100px">Trạng thái</th>
                    <th>Tiêu đề</th>
                    <th>Ngày tháng</th>
                    <th>ID</th>
					<th width="16px"></th>
                </tr></thead>
                <?php if($n=count($rows)):?>
                <tbody>
                    <?php foreach($rows as $k => &$row):?>
                    <tr<?php if($k%2)echo ' bgcolor="#EEEEEE"';else echo ' bgcolor="#FFFFFF"';?>>
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
                            <div>
                                <a href="admin/editArticle?id=<?php echo $row->id?>" title="Click để sửa.">
                                    <?php echo $row->title?>
                                </a>
                            </div>
                            <div class="admin_slug">(<?php echo $row->alias?>)</div>
                            <div class="admin_article_categories">
                                <?php
                                if($row->cat_ids){
                                    $row->cat_ids = explode(',', $row->cat_ids);
                                    $names = array();
                                    foreach($row->cat_ids as $cat_id)
                                        $names[] = $categories[$cat_id]->name;
									echo 'Loại: <span class="admin_inline_list"><span>', implode('</span>, <span>', $names), '</span></span>';
                                }?>
                            </div>
                            <div>Thứ tự (ngày): <?php echo date('H:i d-m-Y', $row->show_date)?></div>
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
							<a href="admin/addCategory/6?alias=<?php echo $row->alias, '-a', $row->id?>">
								<img src="application/views/admin/img/menu_item.png" title="Thêm menu cho 'bài viết' này."/>
							</a>
						</td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
                <?php else:?>
                <tr>
                    <td colspan="6" align="center">
                        Không tìm thấy "bài viết" nào.
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