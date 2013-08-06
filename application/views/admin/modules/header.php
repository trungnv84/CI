<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
$CI =& get_instance();
$CI->load->model('user');
if($CI->user->isLogin()) {
    ?>
    <div id="admin_header">
        <div id="admin_user_panel" class="fr">
            <a class="admin_user_name" href="javascript:;"><?php echo $CI->user->userName()?></a>
            <a class="admin_user_logout" href="admin/logout">Đăng xuất</a>
        </div>
        <?php
        if($CI->user->isAdmin()) {
            ?>
            <div id="admin_header_menu">
                <ul>
                    <li>
						<span>Quản lý chung</span>
                        <ul>
							<li>
								<a href="" target="_blank">Trang chủ</a>
							</li>
							<li>
								<a href="admin">Trang chủ quản trị</a>
							</li>
							<li class="sp"></li>
							<li>
								<a href="admin/config">Cấu hình chung</a>
							</li>
                            <li>
                                <a href="admin/user">Quản lý người dùng</a>
                            </li>
							<li class="sp"></li>
							<li>
								<a href="admin/category/6">Quản lý menu</a>
							</li>
                        </ul>
                    </li>
                    <li>
                        <span>Quản lý nội dung</span>
                        <ul>
                            <li>
                                <a href="admin/category/1">Quản lý loại tin</a>
                            </li>
                            <li>
                                <a href="admin/article">Quản lý tin tức</a>
                            </li>
                            <li class="sp"></li>
                            <li>
                                <a href="admin/category/2">Quản lý nhóm sản phẩm</a>
                            </li>
							<!--<li>
								<a href="admin/category/3">Quản lý loại sản phẩm</a>
							</li>-->
                            <li>
                                <a href="admin/product">Quản lý sản phẩm</a>
                            </li>
							<li class="sp"></li>
							<li>
								<a href="admin/category/7">Quản lý nhóm banner</a>
							</li>
							<li>
								<a href="admin/banner">Quản lý banner</a>
							</li>
                        </ul>
                    </li>
                    <li>
                        <a href="admin/order">Quản lý đơn hàng</a>
                    </li>
                </ul>
            </div>
            <?php
        }
        ?>
        <div class="cb"></div>
    </div><div class="cb"></div>
    <?php
}