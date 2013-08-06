<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Extra
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package        CodeIgniter Extra
 * @author        Nguyễn Văn Trung
 * @copyright    Copyright (c) 1984 - 2013, Nguyễn Văn Trung.
 * @license        commercial
 * @link        http://trungnv.com
 * @since        Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Form Class
 *
 * @package        CodeIgniter
 * @subpackage    Libraries
 * @category    Libraries
 * @author        Nguyễn Văn Trung
 * @link        http://trungnv.com
 */
require_once SYSFOLDER . DS . 'libraries' . DS . 'Cache' . DS . 'Cache.php';
class MY_Cache extends CI_Cache
{
	protected $valid_drivers = array(
		'cache_myfile', 'cache_apc', 'cache_file', 'cache_memcached', 'cache_dummy'
	);
}
// End Class

/* End of file Cache.php */
/* Location: ./application/libraries/Cache/Cache.php */