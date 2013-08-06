            <?php if($n=count($rows)):?>
                <?php foreach($rows as $k => &$row):?>
                <tr<?php if($k%2)echo ' bgcolor="#EEEEEE"';else echo ' bgcolor="#FFFFFF"';?>
                    data-id="<?php echo $row->id?>"
                    data-level="<?php echo $row->level?>"
                    data-branch="<?php echo $row->branch?>">
                    <td class="admin_order_position" align="center" title="(<?php echo $row->ordering?>) Kéo thả để thay đổi thứ tự.">
                        <?php echo $row->ordering?>
                    </td>
                    <td>
                        <input type="checkbox" name="cid" value="<?php echo $row->id?>">
                    </td>
                    <td align="center">
                        <?php echo $row->status?'<i class="admin_publish_icon">&nbsp;</i> Hiện':'<i class="admin_unpublish_icon">&nbsp;</i> Ẩn'?>
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
            <?php endif;?>