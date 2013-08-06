<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Extra
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

/**
 * String Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Nguyễn Văn Trung
 * @link		http://trungnv.com
 */
class MY_String {

    //$this->load->helper('string');
    //random_string('alnum', 32)
    /*public function createSecret($length=32) {
        $secret_code = '';
        for($i=0; $i<$length; $i++) {
            switch(rand(0,2)) {
                case 0:
                    $secret_code .= chr(rand(48, 57));
                    break;
                case 1:
                    $secret_code .= chr(rand(65, 90));
                    break;
                case 2:
                    $secret_code .= chr(rand(97, 122));
            }
        }
        return $secret_code;
    }*/

    public function stringURLSafe($string, $alias = true)
    {
        static $patterns = array ('á' => 'a', 'à' => 'a', 'ã' => 'a', 'ả' => 'a', 'ạ' => 'a', 'â' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẫ' => 'a', 'ẩ' => 'a', 'ậ' => 'a', 'ă' => 'a', 'ắ' => 'a', 'ằ' => 'a', 'ẵ' => 'a', 'ẳ' => 'a', 'ặ' => 'a'
        , 'Á' => 'a', 'À' => 'a', 'Ã' => 'a', 'Ả' => 'a', 'Ạ' => 'a', 'Â' => 'a', 'Ấ' => 'a', 'Ầ' => 'a', 'Ẫ' => 'a', 'Ẩ' => 'a', 'Ậ' => 'a', 'Ă' => 'a', 'Ắ' => 'a', 'Ằ' => 'a', 'Ẵ' => 'a', 'Ẳ' => 'a', 'Ặ' => 'a'
        , 'é' => 'e', 'è' => 'e', 'ẽ' => 'e', 'ẻ' => 'e', 'ẹ' => 'e', 'ê' => 'e', 'ế' => 'e', 'ề' => 'e', 'ễ' => 'e', 'ể' => 'e', 'ệ' => 'e'
        , 'É' => 'e', 'È' => 'e', 'Ẽ' => 'e', 'Ẻ' => 'e', 'Ẹ' => 'e', 'Ê' => 'e', 'Ế' => 'e', 'Ề' => 'e', 'Ễ' => 'e', 'Ể' => 'e', 'Ệ' => 'e'
        , 'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ỏ' => 'o', 'ọ' => 'o', 'ô' => 'o', 'ố' => 'o', 'ồ' => 'o', 'ỗ' => 'o', 'ổ' => 'o', 'ộ' => 'o', 'ơ' => 'o', 'ớ' => 'o', 'ờ' => 'o', 'ỡ' => 'o', 'ở' => 'o', 'ợ' => 'o'
        , 'Ó' => 'o', 'Ò' => 'o', 'Õ' => 'o', 'Ỏ' => 'o', 'Ọ' => 'o', 'Ô' => 'o', 'Ố' => 'o', 'Ồ' => 'o', 'Ỗ' => 'o', 'Ổ' => 'o', 'Ộ' => 'o', 'Ơ' => 'o', 'Ớ' => 'o', 'Ờ' => 'o', 'Ỡ' => 'o', 'Ở' => 'o', 'Ợ' => 'o'
        , 'ú' => 'u', 'ù' => 'u', 'ũ' => 'u', 'ủ' => 'u', 'ụ' => 'u', 'ư' => 'u', 'ứ' => 'u', 'ừ' => 'u', 'ữ' => 'u', 'ử' => 'u', 'ự' => 'u'
        , 'Ú' => 'u', 'Ù' => 'u', 'Ũ' => 'u', 'Ủ' => 'u', 'Ụ' => 'u', 'Ư' => 'u', 'Ứ' => 'u', 'Ừ' => 'u', 'Ữ' => 'u', 'Ử' => 'u', 'Ự' => 'u'
        , 'í' => 'i', 'ì' => 'i', 'ĩ' => 'i', 'ỉ' => 'i', 'ị' => 'i'
        , 'Í' => 'i', 'Ì' => 'i', 'Ĩ' => 'i', 'Ỉ' => 'i', 'Ị' => 'i'
        , 'ý' => 'y', 'ỳ' => 'y', 'ỹ' => 'y', 'ỷ' => 'y', 'ỵ' => 'y'
        , 'Ý' => 'y', 'Ỳ' => 'y', 'Ỹ' => 'y', 'Ỷ' => 'y', 'Ỵ' => 'y'
        , 'đ' => 'd'
        , 'Đ' => 'd'
        , 'á' => 'a', 'à' => 'a', 'ã' => 'a', 'ả' => 'a', 'ạ' => 'a', 'â' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẫ' => 'a', 'ẩ' => 'a', 'ậ' => 'a', 'ă' => 'a', 'ắ' => 'a', 'ằ' => 'a', 'ẵ' => 'a', 'ẳ' => 'a', 'ặ' => 'a'
        , 'Á' => 'a', 'À' => 'a', 'Ã' => 'a', 'Ả' => 'a', 'Ạ' => 'a', 'Â' => 'a', 'Ấ' => 'a', 'Ầ' => 'a', 'Ẫ' => 'a', 'Ẩ' => 'a', 'Ậ' => 'a', 'Ă' => 'a', 'Ắ' => 'a', 'Ằ' => 'a', 'Ẵ' => 'a', 'Ẳ' => 'a', 'Ặ' => 'a'
        , 'é' => 'e', 'è' => 'e', 'ẽ' => 'e', 'ẻ' => 'e', 'ẹ' => 'e', 'ê' => 'e', 'ế' => 'e', 'ề' => 'e', 'ễ' => 'e', 'ể' => 'e', 'ệ' => 'e'
        , 'É' => 'e', 'È' => 'e', 'Ẽ' => 'e', 'Ẻ' => 'e', 'Ẹ' => 'e', 'Ê' => 'e', 'Ế' => 'e', 'Ề' => 'e', 'Ễ' => 'e', 'Ể' => 'e', 'Ệ' => 'e'
        , 'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ỏ' => 'o', 'ọ' => 'o', 'ô' => 'o', 'ố' => 'o', 'ồ' => 'o', 'ỗ' => 'o', 'ổ' => 'o', 'ộ' => 'o', 'ơ' => 'o', 'ớ' => 'o', 'ờ' => 'o', 'ỡ' => 'o', 'ở' => 'o', 'ợ' => 'o'
        , 'Ó' => 'o', 'Ò' => 'o', 'Õ' => 'o', 'Ỏ' => 'o', 'Ọ' => 'o', 'Ô' => 'o', 'Ố' => 'o', 'Ồ' => 'o', 'Ỗ' => 'o', 'Ổ' => 'o', 'Ộ' => 'o', 'Ơ' => 'o', 'Ớ' => 'o', 'Ờ' => 'o', 'Ỡ' => 'o', 'Ở' => 'o', 'Ợ' => 'o'
        , 'ú' => 'u', 'ù' => 'u', 'ũ' => 'u', 'ủ' => 'u', 'ụ' => 'u', 'ư' => 'u', 'ứ' => 'u', 'ừ' => 'u', 'ữ' => 'u', 'ử' => 'u', 'ự' => 'u'
        , 'Ú' => 'u', 'Ù' => 'u', 'Ũ' => 'u', 'Ủ' => 'u', 'Ụ' => 'u', 'Ư' => 'u', 'Ứ' => 'u', 'Ừ' => 'u', 'Ữ' => 'u', 'Ử' => 'u', 'Ự' => 'u'
        , 'í' => 'i', 'ì' => 'i', 'ĩ' => 'i', 'ỉ' => 'i', 'ị' => 'i'
        , 'Í' => 'i', 'Ì' => 'i', 'Ĩ' => 'i', 'Ỉ' => 'i', 'Ị' => 'i'
        , 'ý' => 'y', 'ỳ' => 'y', 'ỹ' => 'y', 'ỷ' => 'y', 'ỵ' => 'y'
        , 'Ý' => 'y', 'Ỳ' => 'y', 'Ỹ' => 'y', 'Ỷ' => 'y', 'Ỵ' => 'y'
        , 'đ' => 'd', 'Đ' => 'd'
        , '_' => ' ', '-' => ' ', '/' => ' ', '\\' => ' ', '.' => ' ', ',' => ' ', ';' => ' '
        );
        $str = strtr($string, $patterns);
        if($alias)
            $str = preg_replace(array('/\s+/','/[^A-Za-z0-9\-]/'), array('-',''), $str);
        else
            $str = preg_replace(array('/\s+/','/[^A-Za-z0-9\s]/'), array(' ',''), $str);
        $str = trim(strtolower($str));
        return $str;
    }

}