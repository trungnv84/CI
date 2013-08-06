<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Extra
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package        CodeIgniter Extra
 * @author        Nguyễn Văn Trung
 * @copyright    Copyright (c) 1984 - 2012, Nguyễn Văn Trung.
 * @license        commercial
 * @link        http://trungnv.com
 * @since        Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Shopping Cart Class
 *
 * @package        CodeIgniter
 * @subpackage    Libraries
 * @category    Shopping Cart
 * @author        Nguyễn Văn Trung
 * @link        http://codeigniter.com/user_guide/libraries/cart.html
 */
//require_once BASEPATH . 'libraries' . DS . 'Cart.php';
class MY_Cart extends CI_Cart
{
	var $product_name_rules = '\D\d';
}