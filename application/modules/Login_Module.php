<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Login_Module extends MY_Module {

	protected function makeVars(&$module)
    {
        $vars = parent::makeVars($module);
		$CI =& get_instance();
		$CI->load->model('user');
		if($CI->user->isLogin())
			$vars['username'] = $CI->user->userName();
		else
			$vars['username'] = false;
        return $vars;
    }

}