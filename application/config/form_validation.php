<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Validation config
|--------------------------------------------------------------------------
|
| Validation config
|
*/
$config	= array(
    'user' => array(
        array(
            'field' => 'username',
            'label' => 'Tên đăng nhập',
            'rules' => 'trim|required|max_length[60]|alpha_dash|xss_clean|is_unique[users.username]'
        ),
        array(
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'trim|required|max_length[120]|valid_email|is_unique[users.email]'
        ),
        array(
            'field' => 'password',
            'label' => 'Mật khẩu',
            'rules' => 'required'
        ),
        array(
            'field' => 'password2',
            'label' => 'Xác nhận mất khẩu',
            'rules' => 'required|matches[password]'
        )
    )
);

/* End of file form_validation.php */
/* Location: ./application/config/form_validation.php */
