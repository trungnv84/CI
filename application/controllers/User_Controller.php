<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_Controller extends MY_Controller
{

	public function manager()
	{
		$this->hasPermit('user_manage', 'admin/');

		$data['browser_title'] = 'Quản lý người dùng';
		$data['page_heading'] = 'Quản lý người dùng';

		$this->load->library('form');
		$data['message'] = $this->form->getMessage();
		$data['filter_search'] = trim($this->form->getState('filter_search', '', 'user'));

		if ($data['filter_search']) {
			$this->user->load_database();
			$filter_search = $this->user->db->escape_like_str($data['filter_search']);
			$conditions = "id = '$filter_search' OR username LIKE '%$filter_search%' OR email LIKE '%$filter_search%'";
		} else
			$conditions = null;

		$config['per_page'] = 30;
		$config['uri_segment'] = 3;
		$config['num_links'] = 7;
		$config['use_page_numbers'] = true;
		$config['base_url'] = 'admin/user';
		$config['total_rows'] = $this->user->total($conditions);

		$this->load->library('pagination');
		$this->pagination->initialize($config);

		$data['pagination'] = $this->pagination->create_links();
		$data['cur_page'] = $this->pagination->cur_page;
		$this->form->setState('user_manage_cur_page', $data['cur_page']);

		$offset = max(0, $data['cur_page'] - 1) * $config['per_page'];
		$data['rows'] = $this->user->getUsers($conditions, false, $config['per_page'], $offset);

		$this->load->view('admin/user/list', $data);
	}

	private function getCurPage()
	{
		static $cur_page;
		if (!isset($cur_page)) {
			$this->load->library('form');
			$cur_page = $this->form->getStateIntFromSession('cur_page', 1, 'user');
			if ($cur_page < 2) $cur_page = '';
		}
		return $cur_page;
	}

	public function add()
	{
		$this->hasPermit('user_manage', 'admin/');
		//$this->load->model('user');
		$this->load->library('form');
		$data['message'] = $this->form->getMessage();
		$data['browser_title'] = 'Thêm người dùng';
		$data['page_heading'] = 'Thêm người dùng';
		$data['cur_page'] = $this->getCurPage();
		$this->load->view('admin/user/form', $data);
	}

	public function edit()
	{
		$this->hasPermit('user_manage', 'admin/');
		//$this->load->model('user');
		$this->load->library('form');
		$data['cur_page'] = $this->getCurPage();
		$id = $this->input->get('id');
		$base_url = $this->config->base_url();
		if (!$id) {
			$id = $this->input->post('cid');
			if ($id && is_array($id) && count($id))
				$id = (int)$id[0];
			if (!$id) {
				$this->load->helper('url');
				$this->form->setMessage('Không tìm thấy "người dùng" cần sửa.', 'alert');
				redirect($base_url . 'admin/user/' . $data['cur_page']);
			}
		}
		$data['user'] = $this->user->getUserById($id);
		if ($data['user'] === false) {
			$this->load->helper('url');
			$this->form->setMessage('Không tìm thấy "người dùng" cần sửa.', 'alert');
			redirect($base_url . 'admin/user/' . $data['cur_page']);
		}
		$data['message'] = $this->form->getMessage();
		$data['browser_title'] = 'Sửa người dùng';
		$data['page_heading'] = 'Sửa người dùng';
		$this->load->view('admin/user/form', $data);
	}

	public function save($next_action = 1)
	{
		$this->hasPermit('user_manage', 'admin/');
		$this->load->helper('url');
		$method = $_SERVER['REQUEST_METHOD'];
		$base_url = $this->config->base_url();
		switch ($method) {
			case 'POST':
				$this->load->library('form_validation');
				$this->form_validation->set_error_delimiters('', '');
				$this->form_validation->set_message('required', 'Bạn phải nhập "%s".');
				$this->form_validation->set_message('max_length', '"%s" dài hơn %s ký tự.');
				$this->form_validation->set_message('alpha_dash', '"%s" không được chứa ký tự đặc biệt hoặc dấu cách.');
				$this->form_validation->set_message('is_unique', '"%s" này đã được sử dụng.');
				$this->form_validation->set_message('valid_email', '"%s" không hợp lệ.');
				$this->form_validation->set_message('matches', '"%s" không giống "%s".');

				$id = (int)$this->input->post('id');
				$data['status'] = $this->input->post('status');
				$data['status'] = $data['status'] ? array_sum($data['status']) : 0;
				//$data['firstname'] = $this->input->post('firstname');
				//$data['lastname'] = $this->input->post('lastname');
				$data['username'] = $this->input->post('username');
				$data['email'] = $this->input->post('email');
				//$data['mobile'] = $this->input->post('mobile');
				$data['password'] = $this->input->post('password');
				$data['permissions'] = $this->input->post('permissions');
				if (is_array($data['permissions']))
					$data['permissions'] = implode(',', $data['permissions']);
				elseif (!$data['permissions']) $data['permissions'] = '';
				$data['begin'] = (int)$this->input->post('begin');
				$data['start'] = trim($this->input->post('start'));
				if ($data['start']) $data['start'] = strtotime($data['start']);
				$data['expire'] = trim($this->input->post('expire'));
				if ($data['expire']) $data['expire'] = strtotime($data['expire']);

				if ($id) {
					$user = $this->user->getUserById($id);
					$this->form_validation->set_rules('username', 'Tên đăng nhập', 'trim|required|max_length[60]|alpha_dash|xss_clean' . ($user->username != $data['username'] ? '|is_unique[users.username]' : ''));
					$this->form_validation->set_rules('email', 'Email', 'trim|required|max_length[120]|valid_email' . ($user->email != $data['email'] ? '|is_unique[users.email]' : ''));
					if ($data['password'] != '') {
						$this->form_validation->set_rules('password', 'Mật khẩu', '');
						$this->form_validation->set_rules('password2', 'Xác nhận mất khẩu', 'required|matches[password]');
					} else unset($data['password']);
				}

				//$this->load->database();
				//$this->load->model('user');
				if ($this->form_validation->run('user')) {
					$data['username'] = $this->form_validation->set_value('username');
					$data['email'] = $this->form_validation->set_value('email');
					if ($id) {
						if(($data['status'] & 1) && !$data['begin'])
							$data['begin'] = TIME_NOW;
						if (!$this->user->update($id, $data)) {
							$data['id'] = $id;
							$next_action = 0;
						}
					} else {
						$id = $this->user->insert($data);
						if (!$id) $next_action = 0;
					}
				} else {
					$data['id'] = $id;
					$this->load->library('form');
					$message = $this->form->renderMessage($this->form_validation->error_string('', '<br />'), 'Cảnh báo', 'warning');
					$next_action = 0;
				}

				if ($next_action) {
					$this->load->library('form');
					$this->form->setMessage('Lưu "người dùng" thành công.', 'success');
					switch ($next_action) {
						case 0:
							break;
						case 1:
							redirect($base_url . 'admin/editUser?id=' . $id);
							break;
						case 2:
							redirect($base_url . 'admin/user/' . $this->getCurPage());
							break;
						case 3:
							redirect($base_url . 'admin/addUser');
							break;
						default:
							redirect($base_url . 'admin/user');
					}
				}

				$data = array('user' => (object)$data);
				$data['browser_title'] = ($id ? 'Sửa người dùng' : 'Thêm người dùng');
				$data['page_heading'] = ($id ? 'Sửa người dùng' : 'Thêm người dùng');
				$data['cur_page'] = $this->getCurPage();
				if (isset($message)) {
					$data['message'] = & $message;
				} else {
					$this->load->library('form');
					$data['message'] = $this->form->renderMessage('Lỗi không lưu được "người dùng".', 'Báo lỗi', 'error');
				}
				$this->load->view('admin/user/form', $data);
				break;
			default:
				redirect($base_url . 'admin/user/' . $this->getCurPage());
		}
	}

	public function saveAndClose()
	{
		$this->save(2);
	}

	public function saveAndAdd()
	{
		$this->save(3);
	}

	public function publish($status = 1)
	{
		$this->hasPermit('user_manage', 'admin/');

		$this->load->library('form');
		$this->load->helper('url');
		$cur_page = $this->getCurPage();
		$base_url = $this->config->base_url();
		$task = $status ? 'Mở khóa' : "Khóa";
		$id = $this->input->get('id');
		if (!$id) {
			$id = $this->input->post('cid');
			if ($id && is_array($id) && count($id))
				$id = $this->form->arrayId($id);
			if (!$id || !is_array($id) || count($id) == 0) {
				$this->form->setMessage("Không tìm thấy \"người dùng\" cần \"$task\".", 'alert');
				redirect($base_url . 'admin/user' . ($cur_page ? '/' . $cur_page : ''));
			}
			if (count($id) == 1) $id = $id[0];
		}
		//$this->load->model('user');
		$success = $status ? 'unBlock' : 'block';
		$success = $this->user->$success($id);
		if ($success) {
			$this->form->setMessage("\"$task\" các \"người dùng\" được chọn thành công.", 'success');
		} else {
			$this->form->setMessage("Lỗi không \"$task\" được các \"người dùng\" được chọn.", 'error');
		}
		redirect($base_url . 'admin/user' . ($cur_page ? '/' . $cur_page : ''));
	}

	public function unpublish()
	{
		$this->publish(0);
	}

	public function delete()
	{
		$this->hasPermit('user_manage', 'admin/');
		$cur_page = $this->getCurPage();
		$this->load->library('form');
		$this->load->helper('url');
		$id = $this->input->get('id');
		$base_url = $this->config->base_url();
		if (!$id) {
			$id = $this->input->post('cid');
			if ($id && is_array($id) && count($id))
				$id = $this->form->arrayId($id);
			if (!$id || !is_array($id) || count($id) == 0) {
				$this->form->setMessage('Không tìm thấy "người dùng" cần "Xóa".', 'alert');
				redirect($base_url . 'admin/user' . ($cur_page ? '/' . $cur_page : ''));
			}
			if (count($id) == 1) $id = $id[0];
		}
		//$this->load->model('user');
		if ($this->user->deleteByIds($id)) {
			$this->form->setMessage('"Xóa" các "người dùng" được chọn thành công.', 'success');
		} else {
			$this->form->setMessage('Lỗi không "Xóa" được các "người dùng" được chọn.', 'error');
		}
		redirect($base_url . 'admin/user' . ($cur_page ? '/' . $cur_page : ''));
	}

	/**
	 * ############################################################################################
	 */

	public function index()
	{
		$this->load->view('user/info');
	}

	private function redirect($admin = false)
	{
		$this->load->library('session');
		$uri = $this->session->userdata('__system_redirect_uri');
		$this->session->unset_userdata('__system_redirect_uri');
		if ($admin && !$uri) {
			if ($this->session->userdata('user_id'))
				$uri = $this->config->base_url() . 'admin';
			else
				$uri = $this->config->base_url() . 'admin/login';
		} elseif (!$uri) $uri = $this->config->base_url();
		$this->load->helper('url');
		redirect($uri);
	}

	public function login($admin = false)
	{
		$this->load->model('user');
		if ($this->user->hasPermit())
			$this->redirect($admin);
		$data = array();
		$data['message'] = '';
		$data['username'] = $this->input->get_post('username');
		$this->load->library('form');
		switch ($_SERVER['REQUEST_METHOD']) {
			case 'POST':
				$is_ajax_request = $this->input->is_ajax_request();
				if(!USE_SESSION_TOKEN || $this->form->token_verify($admin?'admin_login':'site_login')) {
					if (1 == $this->user->login($data['username'], $this->input->post('password'))) {
						if(USE_SESSION_TOKEN) $this->form->delete_token($admin?'admin_login':'site_login');
						if($is_ajax_request) {
							$this->output->append_output(json_encode(array('status' => 1)));
							return;
						} else {
							$this->redirect($admin);
						}
					}
					if ($this->user->message)
						$data['message'] = $is_ajax_request?$this->user->message:
							$this->form->renderMessage($this->user->message, '', 'alert');
					else
						$data['message'] = $is_ajax_request?'Tên hoặc mật khẩu không đúng.':
							$this->form->renderMessage('Tên hoặc mật khẩu không đúng.', '', 'alert');
				} else {
					if($is_ajax_request && USE_SESSION_TOKEN) $data['status'] = 2;
					$data['message'] = $is_ajax_request?(
							USE_SESSION_TOKEN?
							'Phiên làm việc đã hết. Để đăng nhập bạn phải tải lại trang.':
							'Phiên làm việc đã hết. Vui lòng đăng nhập lại.'
						):$this->form->renderMessage('Phiên làm việc đã hết. Vui lòng đăng nhập lại.', '', 'alert');
				}
				if($is_ajax_request) {
					if(!isset($data['status'])) $data['status'] = 0;
					unset($data['username']);
					$this->output->append_output(json_encode($data));
					return;
				}
			case 'GET':
				$data['message'] .= $this->form->getMessage();
				if ($admin) {
					$data['browser_title'] = 'Đăng nhập quản trị';
					$this->load->view('admin/user/login', $data);
				} else {
					$this->load->library('theme');
					$this->theme->view('user/login', $data);
				}
				break;
		}
	}

	public function adminLogin()
	{
		$this->login(true);
	}

	public function logout($admin = false)
	{
		$this->load->model('user');
		if ($this->user->isLogin())
			$this->user->logout();
		$this->redirect($admin);
	}

	public function adminLogout()
	{
		$this->logout(true);
	}

	public function register()
	{
		$data = array();
		$this->load->model('user');
		$this->load->library('form');
		$is_ajax_request = $this->input->is_ajax_request();
		if ($this->user->isLogin()) {
			if($is_ajax_request) {
				$this->output->append_output(json_encode(array('status' => 0, 'message' => 'Bạn đang đăng nhập. Vui lòng đăng xuất để đăng ký.')));
			} else {
				$data['username'] = $this->user->userName();
				$this->load->library('theme');
				$this->theme->view('user/logout', $data);
				$this->load->library('session');
				$this->session->set_userdata('__system_redirect_uri', $this->config->base_url($this->uri->uri_string()));
			}
		} else {
			//$data['firstname'] = $this->input->get_post('firstname');
			//$data['lastname'] = $this->input->get_post('lastname');
			$data['username'] = $this->input->get_post('username');
			$data['email'] = $this->input->get_post('email');
			//$data['mobile'] = $this->input->get_post('mobile');
			$data['password'] = $this->input->post('password');
			switch ($_SERVER['REQUEST_METHOD']) {
				case 'POST':
					if(!USE_SESSION_TOKEN || $this->form->token_verify('site_register')) {
						$this->load->library('form_validation');
						$this->form_validation->set_error_delimiters('', '');
						$this->form_validation->set_message('required', 'Bạn phải nhập "%s".');
						$this->form_validation->set_message('max_length', '"%s" dài hơn %s ký tự.');
						$this->form_validation->set_message('alpha_dash', '"%s" không được chứa ký tự đặc biệt hoặc dấu cách.');
						$this->form_validation->set_message('is_unique', '"%s" này đã được sử dụng.');
						$this->form_validation->set_message('valid_email', '"%s" không hợp lệ.');
						$this->form_validation->set_message('matches', '"%s" không giống "%s".');

						//$this->load->database();
						if ($this->form_validation->run('user')) {
							$data['username'] = $this->form_validation->set_value('username');
							$data['email'] = $this->form_validation->set_value('email');
							$data['status'] = USER_NEED_ACTIVE ? 0 : 1;
							if ($user = $this->user->insert($data)) {
								if(USE_SESSION_TOKEN) $this->form->delete_token('site_register');
								$this->user->sendEmail($user, 'Đăng ký thành công.');
								if (USER_NEED_ACTIVE) {
									if($is_ajax_request) {
										$data['status'] = 3;
										$data['type'] = 'success';
									}
									$data['message'] = "Tài khoản \"$data[username]\" của bạn đã được khởi tạo. Bạn vui lòng kiểm tra email để kích hoạt tài khoản.";
								} else {
									$data['message'] = "Tài khoản \"$data[username]\" của bạn đã được khởi tạo.";
									if(LOGIN_AFTER_REGISTER) {
										if($is_ajax_request) {
											$data['status'] = 3;
											$data['type'] = 'success';
											$data['js'] = 'setTimeout(function(){location.reload();}, 3000);';
										}
										$data['message'] .= '<br/>Hệ thông đang tự động đăng nhập.';
										$this->user->login($data['username'], $data['password']);
									} else {
										if($is_ajax_request) $data['status'] = 1;
										$data['message'] .= '<br/>Bạn có thể đăng nhập với tài khoản vừa đăng ký.';
									}
								}
								if($is_ajax_request) {
									unset($data['username'], $data['email'], $data['password']);
									$this->output->append_output(json_encode($data));
								} else {
									$this->load->library('theme');
									$this->theme->view('user/success', $data);
								}
								return;
							} elseif ($this->user->message) {
								$data['status'] = 0;
								$data['message'] = $is_ajax_request?$this->user->message:
									$this->form->renderMessage($this->user->message, 'Báo lỗi', 'error');
							}
						} else
							$data['message'] = $is_ajax_request?$this->form_validation->error_string('', '<br />'):
								$this->form->renderMessage($this->form_validation->error_string('', '<br />'), 'Cảnh báo', 'warning');

						if (!isset($data['message']))
							$data['message'] = $is_ajax_request?'Có lỗi trong quá trình tạo tài khoản.':
								$this->form->renderMessage('Có lỗi trong quá trình tạo tài khoản.', 'Báo lỗi', 'error');
					} else {
						if($is_ajax_request && USE_SESSION_TOKEN) $data['status'] = 2;
						$data['message'] = $is_ajax_request?(
								USE_SESSION_TOKEN?
								'Phiên làm việc đã hết. Để đăng ký bạn phải tải lại trang.':
								'Phiên làm việc đã hết. Vui lòng đăng ký lại.'
							):$this->form->renderMessage('Phiên làm việc đã hết. Vui lòng đăng ký lại.', '', 'alert');
					}
					if($is_ajax_request) {
						if(!isset($data['status'])) $data['status'] = 0;
						unset($data['username'], $data['email'], $data['password']);
						$this->output->append_output(json_encode($data));
						return;
					}
				case 'GET':
					if (!isset($data['message'])) $data['message'] = '';
					$data['message'] .= $this->form->getMessage();
					$this->load->library('theme');
					$this->theme->view('user/register', $data);
					break;
			}
		}
	}

	public function active()
	{

	}

	public function recovery()
	{
		$this->load->library('form');
		$this->load->model('user');
		if ($this->user->isLogin()) {
			$username = $this->user->userName();
			$this->form->setMessage("Bạn đang đăng nhập tài khoản \"$username\".", 'alert');
			$this->redirect();
		}
		$data = array();
		switch ($_SERVER['REQUEST_METHOD']) {
			case 'POST':
				//zzz

			case 'GET':
				if (isset($data['message']))
					$data['message'] .= $this->form->getMessage();
				else
					$data['message'] = $this->form->getMessage();

				$data['success'] = (bool)$this->input->get('success');

				/*$this->load->library('view', array('template'=>'register', 'ext'=>REWRITE_SUFFIX));
				$this->view->render('register_form', $data);*/

				$this->load->library('view', array('view' => 'recovery', 'template' => 'recovery', 'template_path' => 'dienhoa/'));

				break;
		}
	}

}