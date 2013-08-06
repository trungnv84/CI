<?php
/**
 * Created by JetBrains PhpStorm.
 * User: User
 * Date: 10/30/12
 * Time: 12:02 PM
 * To change this template use File | Settings | File Templates.
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Thumb extends CI_Controller {

    public function index() {
        $this->load->helper('url');
        $segments = $this->uri->segment_array();
        $task = explode('_', $segments[1]);
        $task[2] = (int)$task[2];
        unset($segments[1]);
        $path = implode('/', $segments);
        $pos = strrpos($path, '.');
        $thumb = substr($path, 0, $pos). '-'. $task[1]. '-'. $task[2]. substr($path, $pos);
        if(defined('SITE_NAME') && SITE_NAME) $thumb = SITE_NAME. '/'. $thumb;
        $thumb = 'images/cache/'. $thumb;
        $base_url = $this->config->base_url();
        if(file_exists($thumb)) redirect($base_url. $thumb, 'location', 301);
        $path = 'images/'. $path;
        if(file_exists($path)) {
            $imgSize = getimagesize($path);
            switch($task[1]) {
                case 'max':
                    if($imgSize[0]>$imgSize[1]) {
                        $config['width'] = $task[2];
                    } else {
                        $config['height'] = $task[2];
                    }
                    break;
                case 'min':
                    if($imgSize[0]>$imgSize[1]) {
                        $config['height'] = $task[2];
                    } else {
                        $config['width'] = $task[2];
                    }
                    break;
                case 'h':
                    $config['height'] = $task[2];
                    break;
                case 'w':
                    $config['width'] = $task[2];
                    break;
                case 'b':
                    $config['width'] = $config['height'] = $task[2];
                    break;
				default:
					$config['width'] = (int)$task[1];
					$config['height'] = $task[2];
            }
            if(!isset($config['width'])) $config['width'] = (int)($task[2]*$imgSize[0]/$imgSize[1]);
            if(!isset($config['height'])) $config['height'] = (int)($task[2]*$imgSize[1]/$imgSize[0]);
            $config['image_library'] = 'gd2';
            $config['source_image']	= $path;
            $config['new_image']	= $thumb;
            $config['maintain_ratio'] = TRUE;
            $this->load->library('image_lib', $config);
            $folder = substr($thumb, 0, strrpos($thumb, '/'));
            if(!is_dir($folder)) mkdir($folder, 0777, true);
            if ($this->image_lib->resize())
                redirect($base_url. $thumb, 'location', 301);
            /*else
                echo $this->image_lib->display_errors();*/
        }
        redirect($base_url. $path);
    }

}
