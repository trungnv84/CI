<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class CatMenu_Module extends MY_Module
{

	protected function makeVars(&$module)
	{
		$vars = parent::makeVars($module);
		$CI =& get_instance();
		$CI->load->model('category');
		$where = array('section_id' => $vars['params']->section_id, 'status' => 1);
		if ($vars['params']->cat_id) $where['FIND_IN_SET(' . $vars['params']->cat_id . ',branch) >'] = 0;
		$vars['menus'] = $CI->category->selectCategories(null, $where);
		return $vars;
	}

}