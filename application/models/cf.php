<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cf extends CI_Model
{

	public static $_data = array(
		'SITE_NAME' => array(
			'name' => 'Tên website',
			'input' => array(
				'type' => 'input'
			)
		),
		'SITE_KEYWORDS' => array(
			'name' => 'Keywords website',
			'input' => array(
				'type' => 'textarea'
			)
		),
		'SITE_DESCRIPTION' => array(
			'name' => 'Description website',
			'input' => array(
				'type' => 'textarea'
			)
		),
		'USER_NEED_ACTIVE' => array(
			'name' => 'Tài khoản đăng ký cần kích hoạt',
			'input' => array(
				'type' => 'checkbox',
				'label' => 'Có',
				'data' => 1
			),
			'type' => 'bool'
		),
		'LOGIN_AFTER_REGISTER' => array(
			'name' => 'Tự động đăng nhập sau khi đăng ký',
			'input' => array(
				'type' => 'checkbox',
				'label' => 'Có',
				'data' => 1
			),
			'type' => 'bool'
		),
		'FROM_EMAIL_REGISTER' => array(
			'name' => 'Thư gửi email đăng ký',
			'input' => array(
				'type' => 'input'
			)
		),
		'FROM_NAME_REGISTER' => array(
			'name' => 'Tên gửi email đăng ký',
			'input' => array(
				'type' => 'input'
			)
		),
		'DEFAULT_THEME' => array(
			'name' => 'Giao diện mặc định',
			'input' => array(
				'type' => 'dropdown',
				'data' => 'getThemeList',
			)
		),
		'ASSETS_VERSION' => array(
			'name' => 'Phiên bản của CSS và JS',
			'input' => array(
				'type' => 'input'
			)
		),
		'REWRITE_SUFFIX' => array(
			'name' => 'Hậu tố của rewrite',
			'input' => array(
				'type' => 'input'
			)
		),
		'USE_SESSION_TOKEN' => array(
			'name' => 'Sử dụng token',
			'input' => array(
				'type' => 'checkbox',
				'label' => 'Có',
				'data' => 1
			),
			'type' => 'bool'
		),
		'SESSION_TOKEN_NAME' => array(
			'name' => 'Tên token',
			'input' => array(
				'type' => 'input'
			)
		),
		'CAPTCHA_FOR_REGISTER' => array(
			'name' => 'Sử dụng captcha cho form đăng ký',
			'input' => array(
				'type' => 'checkbox',
				'label' => 'Có',
				'data' => 1
			),
			'type' => 'bool'
		),
		'CAPTCHA_FOR_LOGIN' => array(
			'name' => 'Sử dụng captcha cho form đăng nhập',
			'input' => array(
				'type' => 'checkbox',
				'label' => 'Có',
				'data' => 1
			),
			'type' => 'bool'
		)
	);

	public $message = '';

	public function getConfigs($conditions = array())
	{
		if ($conditions) {
			$result = array();
			foreach (self::$_data as $k => $v) {
				if ($this->where($v, $conditions)) {
					$result[$k] = $v;
				}
			}
			return $result;
		} else return self::$_data;
	}

	private function where($item, $conditions)
	{
		foreach ($conditions as $k => $v) {
			if (strpos(strtolower($item[$k]), strtolower($v)) === false) return false;
		}
		return true;
	}

	public function getConfigById($id)
	{
		if(isset(self::$_data[$id]))
			return self::$_data[$id];
		else return false;
	}

	public function update($id, $value)
	{
		$file = APPPATH . 'config' . DS . DOMAIN_ALIAS . DS . 'constants.php';
		if (!file_exists($file))
			$file = APPPATH . 'config' . DS . 'constants.php';
		if (file_exists($file)) {
			$content = file_get_contents($file);
			if(isset(self::$_data[$id]['type'])) {
				switch (self::$_data[$id]['type']) {
					case 'bool':
						$value = (bool)$value?'TRUE':'FALSE';
						break;
					case 'int':
						$value = (int)$value;
						break;
					case 'float':
						$value = (float)$value;
						break;
				}
			}
			$content = preg_replace("/define\('$id',\s*(('?).*?('?)|.+)\)/", "define('$id', $2$99$value$3)", $content);
			if(file_put_contents($file, $content))
				return true;
		}
		return false;
	}
}