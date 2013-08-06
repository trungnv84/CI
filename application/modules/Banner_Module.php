<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Banner_Module extends MY_Module
{
	protected function makeVars(&$module)
	{
		$vars = parent::makeVars($module);
		$CI =& get_instance();
		$CI->load->model('banner');
		$where = array(
			'cat_id' => $vars['params']->cat_id,
			'status' => 1,
			'(start_date = 0 OR start_date <= ' . TIME_NOW . ')' => null,
			'(start_date = 0 OR end_date > ' . TIME_NOW . ')' => null
		);
		$vars['banners'] = $CI->banner->getBanners($where);
		return $vars;
	}
}