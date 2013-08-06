<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
echo '<h3 class="mod-title">', $title, '</h3>';
foreach($banners as $banner) {
	if($banner->type==2 || $banner->images) {
		switch($banner->type) {
			case 0:
				echo '<a href="', $banner->alias, '"><img src="thumb_w_210/', $banner->images, '"/></a>';
				break;
			case 1:
				$size = getimagesize(APPPATH . 'images' . DS . $banner->images);
				echo '<a href="', $banner->alias, '"><embed width="210" height="' , ceil($size[1]*210/$size[0]), '" src="images/', $banner->images, '"/></a>';
				break;
			case 2:
				echo $banner->content;
		}
	}
}