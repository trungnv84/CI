<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
class MY_Controller extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (ENVIRONMENT == 'development') {
			$this->load->library('session');
			if (isset($_GET['__debug'])) {
				$__debug = $this->input->get_post('__debug');
				$this->session->set_userdata('__debug', $__debug);
			} else {
				$__debug = $this->session->userdata('__debug');
			}
			if ($__debug && !$this->input->is_ajax_request()) $this->output->enable_profiler(TRUE);
		}
	}

	public function hasPermit($permit, $login_prefix = '', $msg = false)
	{
		$this->load->model('user');
		if (!$this->user->hasPermit($permit)) {
			$this->user->requirePermit($permit);
			$this->load->library('session');
			$this->session->set_userdata('__system_redirect_uri',
				$this->config->base_url() .
				$this->uri->uri_string() .
				($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '')
			);
			$permission = $this->user->getPermission($permit);
			$this->load->library('form');
			$this->form->redirect($this->config->base_url() . $login_prefix . 'login', array($msg ? $msg : "Bạn không có quyền sử dụng chức năng \"$permission\". Vui lòng đăng nhập với tài khoản có quyển \"$permission\".", 'alert'), true);
		}
	}
}