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
				<?php
				if($row->images):
					switch($row->type) {
						case 0:
							?>
							<img class="fl" style="height: 80px; margin-right: 10px;" src="thumb_h_80/<?php echo $row->images/*json_decode($row->images)->full_path*/?>">
							<?php
							break;
							?>
						<?php
					}
				endif;
				?>
				<a href="admin/editBanner?id=<?php echo $row->id?>" title="Click để sửa.">
					<?php echo $row->name?>
				</a><br />
				<span class="admin_slug">(Alias: <?php echo $row->alias?>)</span>
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
<?php endif;?>