<?php if(count($rows)):?>
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
            <a href="product/edit?id=<?php echo $row->id?>" title="Click để sửa.">
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
<?php endif;?>