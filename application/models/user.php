<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class User extends MY_Model
{
	protected static $permissions = array(
		'admin' => 'Quản trị hệ thống',
		'system_config' => 'Cấu hình hệ thống',
		'admin_login' => 'Đăng nhập quản trị',
		'<hr/>',
		'user_manage' => 'Quản lý tài khoản',
		'<hr/>',
		'module_manage' => 'Quản lý module',
		'<hr/>',
		'category_6_manage' => 'Quản lý menu',
		'<hr/>',
		'category_7_manage' => 'Quản lý nhóm banner',
		'banner_manage' => 'Quản lý banner',
		'<hr/>',
		'category_1_manage' => 'Quản lý loại tin',
		'article_manage' => 'Quản lý bài viết',
		'<hr/>',
		'category_2_manage' => 'Quản lý nhóm sản phẩm',
		//'category_3_manage' => 'Quản lý loại sản phẩm',
		'product_manage' => 'Quản lý sản phẩm',
	);

	protected static $_cKey = array('users', 'user');

	protected static $_table = 'users';

	protected static $_users;

	protected $isExpire;

	protected $isLogin;

	protected $isAdmin;

	public $message;

	public function insert($data)
	{
		$password = $data['password'];
		$data['password'] = $this->makePass($password);
		$data['secret'] = random_string('alnum', 32);
		if ($data['status'] & 1) $data['begin'] = TIME_NOW;
		$this->load_database();
		$this->db->insert(self::$_table, $data);
		$data['id'] = $this->db->insert_id();
		if ($data['id']) {
			$this->updateKeys(array(self::$_cKey[0], self::$_cKey[1] . $data['id']));
			return $data;
		}
		return $data['id'];
	}

	public function update($id, $data)
	{
		if (isset($data['password']))
			$data['password'] = $this->makePass($data['password']);
		$this->load_database();
		$result = $this->db->update(self::$_table, $data, is_numeric($id) ? array('id' => $id) : $id);
		if ($result) $this->updateKeysById($id);
		return $result;
	}

	public function delete($condition)
	{
		$this->load_database();
		return $this->db->delete(self::$_table, $condition);
	}

	public function deleteByIds($id)
	{
		if (is_array($id))
			$condition = 'id IN (' . implode(',', $id) . ')';
		else {
			$condition = array('id' => $id);
			$id = array($id);
		}
		$result = $this->delete($condition);
		if ($result) $this->updateKeysById($id);
		return $result;
	}

	/*public function status($status, $conditions)
	{
		$this->load_database();
		return $this->db->update('users', array('status' => $status), $conditions);
	}*/

	public function block($id, $block = true)
	{
		$this->load_database();
		if ($block)
			$this->db->set(array('status' => 'status|2'), '', false);
		else
			$this->db->set(array('status' => 'status-(status&2)'), '', false);
		if (is_array($id))
			$condition = 'id IN (' . implode(',', $id) . ')';
		else {
			$condition = array('id' => $id);
			$id = array($id);
		}
		$result = $this->db->update(self::$_table, null, $condition);
		if ($result) $this->updateKeysById($id);
		return $result;
	}

	public function unBlock($id)
	{
		return $this->block($id, false);
	}

	private function makePass($password)
	{
		$this->load->helper('string');
		$key = random_string('alnum', 32);
		return md5($password . $key) . ':' . $key;
	}

	public function sendEmail($user, $subject, $password = false)
	{
		$this->load->library('email');
		$this->email->from(FROM_EMAIL_REGISTER, FROM_NAME_REGISTER);
		$this->email->to($user['email']);
		$this->email->subject($subject);
		$user['mailer'] =& $this->email;
		if (USER_NEED_ACTIVE) {
			$user['active_link'] = $this->config->base_url('user/active?k=' . TIME_NOW . '&s=' . md5(TIME_NOW . $user['password']));
		}
		if ($password) $user['password'] = $password;
		$this->load->library('theme');
		$this->email->message($this->theme->view('user/register_email', $user, true));
		$this->email->send();
	}

	public function getAllPer()
	{
		return self::$permissions;
	}

	public function getPermission($permission)
	{
		if (isset(self::$permissions[$permission]))
			return self::$permissions[$permission];
		return false;
	}

	public function login($username, $password)
	{
		$user = $this->getUserByUserName($username);
		if ($user) {
			if ($user->status & 2) {
				$this->message = 'Tài khoản của bạn đã bị khóa.';
			} elseif ($user->status & 1) {
				if ($user->start > TIME_NOW) {
					$this->message = 'Tài khoản của bạn sẽ được kích hoạt từ ngày ' . date('d-m-Y', $user->start) . '.';
					return false;
				}
				if ($user->expire && $user->expire <= TIME_NOW) {
					$this->message = 'Tài khoản của bạn đã hết hạn từ ngày ' . date('d-m-Y', $user->expire) . '.';
					return false;
				}
				$pass = explode(':', $user->password);
				if (isset($pass[1])) $password .= $pass[1];
				if (hash((($user->algorithm && in_array($user->algorithm, hash_algos())) ? $user->algorithm : 'md5'), $password) == $pass[0]) {
					$this->load->library('session');
					$this->session->unset_userdata('_system_require_permit');
					$this->session->set_userdata(array(
						'user_id' => $user->id,
						'user_name' => $user->username,
						'user_email' => $user->email,
						//'user_mobile' => $user->mobile,
						'user_secret' => $user->secret,
						'user_permissions' => explode(',', $user->permissions),
						'user_expire' => $user->expire,
						'user_last_login' => TIME_NOW,
						'user_last_access' => TIME_NOW
					));
				} else return false;
			} else {
				$this->message = 'Tài khoản của bạn chưa được kích hoạt.';
			}
			return $user->status;
		}
		return false;
	}

	public function logout()
	{
		$this->load->library('session');
		$this->session->unset_userdata(array(
			'user_id' => '',
			'user_name' => '',
			'user_email' => '',
			//'user_mobile' => '',
			'user_secret' => '',
			'user_permissions' => '',
			'user_expire' => '',
			'user_last_login' => '',
			'user_last_access' => ''
		));
	}

	public function getUserById($user_id = false)
	{
		if (!$user_id) {
			$this->load->library('session');
			$user_id = $this->session->userdata('user_id');
		}
		$user_id = (int)$user_id;
		if ($user_id) {
			if (!isset(self::$_users[$user_id])) {
				$this->load_database();
				$query = $this->db->get_where(self::$_table, array('id' => $user_id), 1);
				if ($query->num_rows() > 0)
					self::$_users[$user_id] = $query->row();
				else
					self::$_users[$user_id] = false;
				$query->free_result();
			}
			return self::$_users[$user_id];
		}
		return false;
	}

	public function getUserByUserName($username)
	{
		if ($username) {
			$username = strtolower($this->db->escape_str($username));
			if (!isset(self::$_users[$username])) {
				if (defined('USERLOGINBY')) {
					$where = array();
					if (USERLOGINBY & 1) $where['username'] = $username;
					if (USERLOGINBY & 2) $where['email'] = $username;
				} else
					$where = array('username' => $username);
				$this->load_database();
				$this->db->or_where($where);
				$query = $this->db->get(self::$_table, 1);
				if ($query->num_rows() > 0)
					self::$_users[$username] = $query->row();
				else
					self::$_users[$username] = false;
				$query->free_result();
			}
			return self::$_users[$username];
		}
		return false;
	}

	public function isExpire($setSysMsg = TRUE)
	{
		if (!isset($this->isExpire)) {
			$this->load->library('session');
			$expire = (int)$this->session->userdata('user_expire');
			if($this->isExpire = !($expire == 0 || $expire > TIME_NOW)) {
				$this->logout();
                if($setSysMsg) {
	    			$this->load->library('form');
    				$this->form->setMessage('Tài khoản của bạn đã hết hạn từ ngày ' . date('d-m-Y', $expire) . '.', 'alert');
                }
			}
		}
		return $this->isExpire;
	}

	public function isLogin()
	{
		if (!isset($this->isLogin)) {
			$this->load->library('session');
			$user_id = $this->session->userdata('user_id');
			$this->isLogin = $user_id ? !$this->isExpire() : false;
		}
		return $this->isLogin;
	}

	public function isAdmin()
	{
		if (!isset($this->isAdmin)) {
			$this->isAdmin = false;
			if ($this->isLogin()) {
				$this->load->library('session');
				$user_permissions = $this->session->userdata('user_permissions');
				if ($user_permissions && in_array('admin', $user_permissions))
					$this->isAdmin = true;
			}
		}
		return $this->isAdmin;
	}

	public function requirePermit($permissions = false)
	{
		$this->load->library('session');
		if ($permissions)
			$this->session->set_userdata('_system_require_permit', $permissions);
		else
			return $this->session->userdata('_system_require_permit');
	}

	public function requireAdmin()
	{
		$this->requirePermit('admin');
	}

	public function hasPermit($permissions = false, $and = false)
	{
		if ($this->isLogin()) {
			if (!$permissions) {
				$permissions = $this->session->userdata('_system_require_permit');
				if (!$permissions) return true;
			}
			if ($user_permissions = $this->session->userdata('user_permissions')) {
				if ($permissions === true || in_array('admin', $user_permissions))
					return true;
				if (is_array($permissions)) {
					foreach ($permissions as $permission) {
						if (in_array($permission, $user_permissions)) {
							if (!$and) return true;
						} elseif ($and) return false;
					}
					return true;
				} elseif (in_array($permissions, $user_permissions))
					return true;
			}
		}
		return false;
	}

	public function userId()
	{
		static $user_id;
		if (!isset($user_id)) {
			$this->load->library('session');
			$user_id = $this->session->userdata('user_id');
		}
		return $user_id;
	}

	public function userName($user_id = false)
	{
		if (!$user_id) {
			static $user;
			if (!isset($user)) {
				$this->load->library('session');
				$user = $this->session->userdata('user_name');
				if ($user) return $user;
			} elseif ($user) return $user;
		}
		if ($user = $this->getUserById($user_id))
			return $user->username;
		else
			return false;
	}

	public function getUsers($conditions = null, $order = false, $limit = null, $offset = null)
	{
		$this->load_database();
		if ($order) $this->db->order_by($order);
		$query = $this->db->get_where(self::$_table, $conditions, $limit, $offset);
		return $query->result();
	}
}