<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Product_Module extends MY_Module
{
	protected function makeVars(&$module)
	{
		$vars = parent::makeVars($module);
		$CI =& get_instance();
		$CI->load->model('product');
		if($vars['params']->feature)
			$where['feature'] = $vars['params']->feature - 1;
		$where['status'] = 1;
		$where['(start_date = 0 OR start_date <= ' . TIME_NOW . ')'] = null;
		$where['(start_date = 0 OR end_date > ' . TIME_NOW . ')'] = null;
		if($vars['params']->promotions) {
			$where['discount !='] = 0;
			$where['(start = 0 OR start <= ' . TIME_NOW . ')'] = null;
			$where['(expire = 0 OR expire > ' . TIME_NOW . ')'] = null;
		}
		$vars['products'] = $CI->product->getProducts($where);
		return $vars;
	}
}