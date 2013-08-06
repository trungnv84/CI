<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['post_controller'] = array(
    'class'    => 'CTL_Post',
    'function' => 'CtlPost',
    'filename' => 'CTL_Post.php',
    'filepath' => 'hooks',
    'params'   => NULL
);

/* End of file hooks.php */
/* Location: ./application/config/hooks.php */

$_file = __DIR__ . DS . DOMAIN_ALIAS . DS . 'hooks.php';
if (file_exists($_file)) require_once $_file;