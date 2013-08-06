<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends MY_Controller
{
	public function index()
	{
		$this->hasPermit('admin_login', 'admin/', 'Bạn không có quyền sử dụng chức năng quản trị. Vui lòng đăng nhập với tài khoản quản trị.');
		$this->load->library('form');
		$data['message'] = $this->form->getMessage();
		$data['browser_title'] = 'Trang quản trị';
		$this->load->view('admin/dashboard', $data);
	}
}
