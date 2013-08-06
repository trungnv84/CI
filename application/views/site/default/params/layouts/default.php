<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// Module data
$modules = array(
    'navigation' => array(
        array(
            'name' => 'Navigation_Module',
            'title' => 'Menu ngang',
            'params' => '{"menu":88}'
        )
    ),
	'top' => array(
		array(
			'name' => 'Search_Module',
			'title' => 'Search',
			'params' => ''
		),
		array(
			'name' => 'ChatLink_Module',
			'title' => 'Chat link',
			'params' => '{"yahoo0":"trungnv84@ymail.com","yahoo1":"trungnv84@ymail.com","yahoo2":""}'
		),
		array(
			'name' => 'Login_Module',
			'title' => 'Đăng nhập',
			'params' => ''
		)
	),
	'left' => array(
		array(
			'name' => 'CatMenu_Module',
			'title' => 'Điện hoa',
			'chrome' => 'default',
			'class' => '',
			'params' => '{"section_id":2,"cat_id":87}'
		),
		array(
			'name' => 'CatMenu_Module',
			'title' => 'Quà tặng',
			'chrome' => 'default',
			'class' => ' mod_gift',
			'params' => '{"section_id":2,"cat_id":102}'
		),
		array(
			'name' => 'Banner_Module',
			'title' => 'Đối tác',
			'chrome' => 'default',
			'class' => '',
			'params' => '{"cat_id":89}'
		)
	),
	'home' => array(
		array(
			'name' => 'Product_Module',
			'title' => 'Sản phẩm mới',
			'params' => '{"feature":3,"promotions":0,"limit":25}'
		),
		array(
			'name' => 'Product_Module',
			'title' => 'Sản phẩm khuyến mại',
			'params' => '{"feature":0,"promotions":1,"limit":5}'
		),
		array(
			'name' => 'Product_Module',
			'title' => 'Sản phẩm bán chạy - best sales',
			'params' => '{"feature":5,"promotions":0,"limit":5}'
		)
	),
	'footer-nav' => array(
		array(
			'name' => 'Navigation_Module',
			'title' => 'Menu ngang',
			'params' => '{"menu":88}'
		)
	)
);