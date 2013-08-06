<?php
/**
 * Created by JetBrains PhpStorm.
 * User: User
 * Date: 10/26/12
 * Time: 5:27 PM
 * To change this template use File | Settings | File Templates.
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

define('ALLOW_ALL_KEY', true);

class Tinymce extends CI_Controller {

    public function index() {
    }

    public function imagemanager() {
    }
}
$CodeIgniter = new Tinymce();
$CodeIgniter->load->model('user');
$CodeIgniter->user->load->library('session');
$CodeIgniter->load->helper('url');
$tmp_script = $CodeIgniter->uri->uri_string();
$tmp_dir = dirname($tmp_script);
if(strpos($tmp_dir, '/imagemanager')!==false) {
    $tmp_cur_dir = getcwd();
    chdir($tmp_dir);
    require_once(basename($tmp_script));
    exit;
    chdir($tmp_cur_dir);
}
