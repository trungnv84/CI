<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter Extra
 * @author		Nguyễn Văn Trung
 * @copyright	Copyright (c) 1984 - 2012, Nguyễn Văn Trung.
 * @license		commercial
 * @link		http://trungnv.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

class MY_Module {

	/**
	 * Constructor
	 */
	/*public function __construct()
	{
		$this->ci =& get_instance();
	}*/

	protected function makeVars(&$module)
    {
        $vars['name'] = $module['name'];
        $vars['title'] = $module['title'];
        $vars['view'] = isset($module['view'])?$module['view']:'default';
		$vars['chrome'] = isset($module['chrome'])?$module['chrome']:'none';
		$vars['class'] = isset($module['class'])?$module['class']:'';
        $vars['params'] = json_decode($module['params']);
        return $vars;
    }

    public function view(&$module, &$theme)
	{
        $CI =& get_instance();
        $vars = $this->makeVars($module);
		$vars['theme'] = & $theme;
        $themeName = $CI->theme->getTheme();
		if($vars['chrome']=='none')
			$CI->load->view("site/$themeName/modules/$vars[name]/$vars[view]", $vars);
		else {
			$vars['_module'] = $CI->load->view("site/$themeName/modules/$vars[name]/$vars[view]", $vars, true);
			$CI->load->view("site/$themeName/chromes/$vars[chrome]", $vars);
		}
        //$theme->saveCache();
	}
}
// END Module class

/* End of file Module.php */
/* Location: ./application/core/MY_Module.php */