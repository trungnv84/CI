<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Navigation_Module extends MY_Module {

    /*public function __construct()
    {
        parent::__construct();
    }*/

	protected function makeVars(&$module)
    {
		$vars = parent::makeVars($module);
		if($vars['params']->menu) {
			$CI =& get_instance();
			$CI->load->model('category');
			$vars['menus'] = $CI->category->selectCategories(null, array(
				'section_id' => 6,
				'status' => 1,
				'FIND_IN_SET('. $vars['params']->menu. ',branch) >' => 0
			));
		}
        return $vars;
    }

    /*public function view(&$module)
    {
		parent::view($module);
    }*/
}