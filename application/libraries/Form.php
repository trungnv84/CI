<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Extra
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package			CodeIgniter Extra
 * @author			Nguyễn Văn Trung
 * @copyright		Copyright (c) 1984 - 2012, Nguyễn Văn Trung.
 * @license			commercial
 * @link			http://trungnv.com
 * @since			Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Form Class
 *
 * @package			CodeIgniter
 * @subpackage		Libraries
 * @category		Libraries
 * @author			Nguyễn Văn Trung
 * @link			http://trungnv.com
 */
class MY_Form
{

	/*public function __construct() {

	}*/

	public function form_token($key = 'default', $length = 32, $multi = false)
	{
		return '<input type="hidden" autocomplete="off" id="' . $key . '_token" name="' . SESSION_TOKEN_NAME . '" value="' . $this->token_key($key, $length, $multi) . '"/>';
	}

	public function token_key($key = 'default', $length = 32, $multi = false)
	{
		static $tokens = array();
		$token = $key . $multi;
		if (!isset($tokens[$token])) {
			$key .= SESSION_TOKEN_NAME;
			if($length<32)
				$tokens[$token] = substr(md5(rand()), 0, $length - 32);
			else
				$tokens[$token] = md5(rand());
			$CI =& get_instance();
			$CI->load->library('session');
			if ($multi)
				$CI->session->set_userdata($key . '_' . $tokens[$token], TRUE);
			else
				$CI->session->set_userdata($key, $tokens[$token]);
		}
		return $tokens[$token];
	}

	public function token_verify($key = 'default', $token = false, $multi = false)
	{
		$CI =& get_instance();
		if (!$token) $token = (string)$CI->input->get_post(SESSION_TOKEN_NAME);
		if (!$token) return false;
		$key .= SESSION_TOKEN_NAME;
		$CI->load->library('session');
		if ($multi) {
			$key .= '_' . $token;
			$token = (bool)$CI->session->userdata($key);
		} else
			$token = ($CI->session->userdata($key) == $token);
		if (!$CI->input->is_ajax_request() && $token) $CI->session->unset_userdata($key);
		return $token;
	}

	public function delete_token($key = 'default', $token = false, $multi = false)
	{
		$CI =& get_instance();
		$key .= SESSION_TOKEN_NAME;
		if ($multi) {
			if (!$token) $token = (string)$CI->input->get_post(SESSION_TOKEN_NAME);
			if (!$token) return;
			$key .= '_' . $token;
		}
		$CI->load->library('session');
		$CI->session->unset_userdata($key);
	}

	public function setState($state, $value = '', $namespace = '_default')
	{
		$CI =& get_instance();
		$CI->load->library('session');
		if (strpos($namespace, '_') === false) $namespace = '_default_' . $namespace;
		$state = $namespace . '_' . $state;
		$CI->session->set_userdata($state, $value);
	}

	public function getStateFromSession($state, $default = '', $namespace = '_default')
	{
		$CI =& get_instance();
		$CI->load->library('session');
		if (strpos($namespace, '_') === false) $namespace = '_default_' . $namespace;
		$state = $namespace . '_' . $state;
		if (isset($CI->session->userdata[$state])) {
			return $CI->session->userdata[$state];
		} else {
			return $default;
		}
	}

	public function getStateIntFromSession($state, $default = 0, $namespace = '_default')
	{
		return (int)$this->getStateFromSession($state, $default, $namespace);
	}

	public function getState($name, $default = '', $namespace = '_default', $xss_clean = false, $state = false)
	{
		if (!$state) $state = $name;
		if (strpos($namespace, '_') === false) $namespace = '_default_' . $namespace;
		$state = $namespace . '_' . $state;
		$CI =& get_instance();
		$CI->load->library('session');
		if (isset($_POST[$name])) {
			$value = $CI->input->post($name, $xss_clean);
			$CI->session->set_userdata($state, $value);
		} elseif (isset($_GET[$name])) {
			$value = $CI->input->get($name, $xss_clean);
			$CI->session->set_userdata($state, $value);
		} elseif (isset($CI->session->userdata[$state])) {
			$value = $CI->session->userdata[$state];
		} else {
			return $default;
		}
		return $value;
	}

	public function getStateInt($name, $default = 0, $namespace = '_default', $state = false)
	{
		return (int)$this->getState($name, $default, $namespace, $state);
	}

	public function setMessage($message, $type = 'message', $key = false, $namespace = '_default')
	{
		$CI =& get_instance();
		$CI->load->library('session');
		if (strpos($namespace, '_') === false) $namespace = '_default_' . $namespace;
		$type = $namespace . '_' . $type;
		$messages = $CI->session->userdata($type);
		if ($key) $messages[$key] = $message;
		else $messages[] = $message;
		$CI->session->set_userdata($type, $messages);
	}

	public function getMessage($type = 'all', $namespace = '_default', $unset = true)
	{
		if ($type == 'all') {
			$types = array('error', 'danger', 'success', 'message', 'info', 'warning', 'alert');
		} else $types = array($type);
		$message = '';
		$CI =& get_instance();
		$CI->load->library('session');
		if (strpos($namespace, '_') === false) $namespace = '_default_' . $namespace;
		foreach ($types as $type) {
			switch ($type) {
				case 'error':
					$title = 'Báo lỗi';
					break;
				case 'danger':
				case 'warning':
					$title = 'Cảnh báo';
					break;
				default:
					$title = 'Thông báo';
			}
			$state = $namespace . '_' . $type;
			$messages = $CI->session->userdata($state);
			if ($messages && count($messages)) {
				$messages = implode('<br />', $messages);
				$message .= "<div class='alert_box alert_box_$type'>
                    <div class='alert_close_button alert_flat_close_button' title='Đóng'>x</div>
                    <div class='alert_title alert_title_$type'>$title</div>
                    <div class='alert_message alert_message_$type'>$messages</div></div>";
				if ($unset) $CI->session->unset_userdata($state);
			}
		}
		return $message;
	}

	public function renderMessage($message, $title = '', $type = 'message')
	{
		if ($title) $title = "<div class='alert_title alert_title_$type'>$title</div>";
		return "<div class='alert_box alert_box_$type'><div class='alert_close_button alert_flat_close_button' title='Đóng'>x</div>$title<div class='alert_message alert_message_$type'>$message</div></div>";
	}

	public function redirect($uri = '', $message = array(), $back = false, $method = 'location', $http_response_code = 302)
	{
		$CI =& get_instance();
		if ($message) {
			if (is_array($message))
				$this->setMessage($message[0], $message[1]);
			else $this->setMessage($message);
		}

		if ($back) {
			$CI->load->library('session');
			if ($back === true || $back == 'current' || $back == 'auto' || $back == 'this')
				$back = $CI->config->base_url() . $CI->uri->uri_string() . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '');
			$CI->session->set_userdata('_system_redirect_uri', $back);
		}

		$CI->load->helper('url');
		redirect($uri, $method, $http_response_code);
	}

	public function arrayInt($array)
	{
		foreach ($array as &$v) {
			$v = (int)$v;
		}
		return $array;
	}

	public function arrayId($array)
	{
		foreach ($array as $k => &$v) {
			$v = (int)$v;
			if (!$v) unset($array[$k]);
		}
		$array = array_values($array);
		return $array;
	}

	public function price_format($num)
	{
		return number_format($num, 0, ',', ' ');
	}

	public function form_input($input)
	{
		$CI =& get_instance();
		$CI->load->helper('form');
		switch ($input['type']) {
			case 'input':
			case 'textarea':
				$func = 'form_' . $input['type'];
				return $func($input['name'], $input['value'], 'class="input-xxlarge"');
				break;
			case 'dropdown':
				return form_dropdown($input['name'], $input['data'](), $input['value']);
				break;
			case 'checkbox':
				return form_label(form_checkbox($input['name'], $input['data'], $input['value']) . $input['label'], null, array('class' => 'checkbox', 'style' => 'margin-bottom:0'));
				break;
		}
	}
}

/*##############################################################################*/

function getThemeList()
{
	$themes = array();
	$path = APPPATH . 'views' . DS . 'site';
	if ($handle = opendir($path)) {
		while (false !== ($entry = readdir($handle)))
		{
			if ($entry == '.' || $entry == '..')
				continue;

			if (is_dir($path . DS . $entry))
				$themes[$entry] = $entry;
		}

		closedir($handle);
	}
	if(!$themes) $themes = array('' => 'Không có giao diện nào');
	return $themes;
}