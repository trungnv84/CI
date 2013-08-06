<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH . 'core' . DS . 'MY_Module.php';
if (ASSETS_OPTIMIZATION & 2)
	require_once APPPATH . 'third_party' . DS . 'cssmin.php';
if (ASSETS_OPTIMIZATION & 8)
	require_once APPPATH . 'third_party' . DS . 'jsmin.php';

/**
 * CodeIgniter Extra
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package     CodeIgniter Extra
 * @author      Nguyễn Văn Trung
 * @copyright   Copyright (c) 1984 - 2012, Nguyễn Văn Trung.
 * @license     commercial
 * @link        http://trungnv.com
 * @since       Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Form Class
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Libraries
 * @author      Nguyễn Văn Trung
 * @link        http://trungnv.com
 */

class MY_Theme
{

    private $name = DEFAULT_THEME;
    private $layout = 'default';
    private $cache = false;
    private $cacheData;
    private $pageKeys = array();
	private $cache_file;
    private $_doc_title;
    private $_css = array();
    private $_js = array();
    private $_meta_tag = array();

    public function __construct()
    {
        $CI =& get_instance();
        $CI->load->library('user_agent');
        // co the phai bo !$CI->agent->is_browser();
        if (!$CI->agent->is_robot() && $CI->agent->is_mobile()) {
            $CI->load->library('session');
            $__screen_ppi = $CI->session->userdata('__screen_ppi');
            if (!$__screen_ppi) {
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $__screen_ppi = (int)$CI->input->post('__screen_ppi');
                    $__screen_width = (int)$CI->input->post('__screen_width');
                    $__screen_height = (int)$CI->input->post('__screen_height');
                    $__window_width = (int)$CI->input->post('__window_width');
                    $__window_height = (int)$CI->input->post('__window_height');
                    $CI->session->set_userdata('__screen_ppi', $__screen_ppi);
                    $CI->session->set_userdata('__screen_width', $__screen_width);
                    $CI->session->set_userdata('__screen_height', $__screen_height);
                    $CI->session->set_userdata('__window_width', $__window_width);
                    $CI->session->set_userdata('__window_height', $__window_height);
                    $CI->load->helper('url_helper');
                    redirect(current_url());
                } else {
                    echo file_get_contents(APPPATH . 'libraries' . DS . 'views' . DS . 'check_screen_solution.html');//zzz
                    exit;
                }
            }
        }
        $this->cacheData = new stdClass();
    }

    public function setTheme($name)
    {
        $this->name = $name;
    }

    public function getTheme()
    {
        return $this->name;
    }

    public function setLayout($name)
    {
        $this->layout = $name;
    }

    public function setHtmlTitle($title)
    {
        if ($this->cache) $this->cacheData->_doc_title = $title;
        $this->_doc_title = $title;
    }

    public function setMetaKeywords($keywords = '')
    {
        $this->addMetaTag("<meta name=\"keywords\" content=\"$keywords\">", 'MetaKeywords', true);
    }

    public function setMetaDescription($description = '')
    {
        $this->addMetaTag("<meta name=\"description\" content=\"$description\">", 'MetaDescription', true);
    }

    public function addAsset($asset, $type, $key = false, $overwrite = false)
    {
        $type = '_' . $type;
        if ($key) {
            if (isset($this->{$type}[$key]) && !$overwrite) return;
            $this->{$type}[$key] = $asset;
        } elseif (!in_array($asset, $this->$type)) {
            $this->{$type}[] = $asset;
        }
        if ($this->cache) $this->cacheData->{$type}[] = array($asset, $key, $overwrite);
    }

    public function addCSS($css, $key = false, $overwrite = false)
    {
        $this->addAsset($css, 'css', $key, $overwrite);
    }

    public function addJS($js, $key = false, $overwrite = false)
    {
        $this->addAsset($js, 'js', $key, $overwrite);
    }

    public function unShiftCSS($css, $key = false, $overwrite = false)
    {
        if ($key && isset($this->_css[$key]) && !$overwrite) return;
        if ($key) $css = array($key => $css);
        else $css = array($css);
        $this->_css = array_merge($css, $this->_css);
        if ($this->cache) array_unshift($this->cacheData->_css, array($css, $key, $overwrite));
    }

    public function unShiftJS($js, $key = false, $overwrite = false)
    {
        if ($key && isset($this->_js[$key]) && !$overwrite) return;
        if ($key) $js = array($key => $js);
        else $js = array($js);
        $this->_js = array_merge($js, $this->_js);
        if ($this->cache) array_unshift($this->cacheData->_js, array($js, $key, $overwrite));
    }

    public function addMetaTag($tag = '', $key = false, $overwrite = false)
    {
        $this->addAsset($tag, 'meta_tag', $key, $overwrite);
    }

    public function removeAsset($key, $type)
    {
        $type = '_' . $type;
        unset($this->{$type}[$key]);
    }

    public function removeCSS($key)
    {
        $this->removeAsset($key, 'css');
    }

    public function removeJS($key)
    {
        $this->removeAsset($key, 'js');
    }

    public function removeMetaTag($key)
    {
        $this->removeAsset($key, 'meta_tag');
    }

    public function view($view, $vars = array(), $return = FALSE)
    {
        $CI =& get_instance();
        $vars['theme'] = & $this;
        $html = $CI->load->view('site' . DS . $this->name . DS . 'controllers' . DS . $view, $vars, true);
        if ($this->cache) $this->saveCache($this->cache_file, $html);
        if ($return) return $html;
        $this->html($html);
    }

    private function html(&$component)
    {
        $vars = array();
        $vars['theme'] = & $this;
        $vars['_layout'] = $this->layout($component);
        $vars['_doc_title'] = $this->_doc_title;
        $vars['_doc_header'] = $this->htmlHeader();
        $CI =& get_instance();
        $CI->load->view('site' . DS . $this->name . DS . 'html', $vars);
        //$this->savePageKeys();//zzz
    }

    private function layout(&$component)
    {
        $vars = array();
        $vars['theme'] = & $this;
        $vars['_component'] = & $component;
        $CI =& get_instance();
        return $CI->load->view('site' . DS . $this->name . DS . 'layouts' . DS . $this->layout, $vars, true);
    }

    private function htmlHeader()
    {
        $CI =& get_instance();
		$CI->load->model('user');
		/*if($CI->user->hasPermit('module_manage')) {
			$this->addCSS('application/views/admin/css/site.css');
			$modules = & $this->getModules();
			$this->addJS('if(!site) var site = {};site.theme = "' . $this->name . '";site.layout = "' . $this->layout . '";site.modules = ' . json_encode($modules) . ';');
			$this->addJS('application/views/admin/js/site.js');
		}*/
        $base_url = $CI->config->base_url();
        $html = '<base href="' . $base_url . "\">\n";
        if (isset($this->_meta_tag) && count($this->_meta_tag))
            foreach ($this->_meta_tag as $metaTag)
                $html .= $metaTag . "\n";
        if (isset($this->_css) && count($this->_css)) {
            if (ASSETS_OPTIMIZATION & 3) {
                $nameMd5 = $cache = '';
                foreach ($this->_css as $css) {
                    if (strrpos($css, '{') === false) {
                        $nameMd5 .= $css;
                        if (strrpos($css, '/') === false) {
                            $css = APPPATH . 'views/site/' . $this->name . '/css/' . $css;
                            if (file_exists($css)) {
								if(ASSETS_OPTIMIZATION & 2) $cache .= $this->minAsset($css) . "\n";
								else $cache .= @file_get_contents($css) . "\n";
							}
                        } elseif (preg_match('/https?:\/\//', $css)) {
                            $tmp = @file_get_contents($css);
                            if (preg_match('/:\s*url\s*\(/i', $tmp)) {
                                $html .= "<link href=\"$css?v=" . ASSETS_VERSION . "\" rel=\"stylesheet\" type=\"text/css\" />\n";
                            } else {
                                $cache .= $tmp;
                            }
						} elseif (file_exists($css)) {
							if(ASSETS_OPTIMIZATION & 2) $tmp = $this->minAsset($css);
							else $tmp = @file_get_contents($css);
							$cache .= preg_replace('/:\s*url\s*\(\s*([\'"])/i', ': url($1../../../../../' . dirname($css) . '/', $tmp);
                        }/* else
							$html .= "<link href=\"$css?v=" . ASSETS_VERSION . "\" rel=\"stylesheet\" type=\"text/css\" />\n";*/
                    } else {
                        $nameMd5 .= $css;
                        $cache .= $css . "\n";
                    }
                }
                $cache = str_replace('"../', '"../../', $cache);
                $nameMd5 = md5($nameMd5);
                $cacheMd5 = md5($cache);
                $file = APPPATH . 'views/site/' . $this->name . '/css/cache/' . $nameMd5 . '.css';
                if (file_exists($file)) {
                    if (ENVIRONMENT != 'production' && $cacheMd5 != md5_file($file))
                        file_put_contents($file, $cache);
                } else {
                    $folder = APPPATH . 'views/site/' . $this->name . '/css/cache/';
                    if (!is_dir($folder)) mkdir($folder, 0755, true);
                    file_put_contents($file, $cache);
                }
				$file = APPFOLDER . "/views/site/$this->name/css/cache/$nameMd5.css?v=$cacheMd5";
                $html .= "<link href=\"$file\" rel=\"stylesheet\" type=\"text/css\" />\n";
            } else {
                foreach ($this->_css as $css) {
                    if (strrpos($css, '{') === false) {
                        if (strrpos($css, '/') === false)
                            $css = APPFOLDER . "/views/site/$this->name/css/$css";
                        $css .= '?v=' . ASSETS_VERSION;
                        $html .= "<link href=\"$css\" rel=\"stylesheet\" type=\"text/css\" />\n";
                    } else {
                        $html .= "<style type=\"text/css\">\n{$css}\n</style>\n";
                    }
                }
            }
        }
        if (isset($this->_js) && count($this->_js)) {
            if (ASSETS_OPTIMIZATION & 12) {
                $nameMd5 = $cache = '';
                foreach ($this->_js as $js) {
                    if (preg_match('/[;\(]/', $js)) {
                        $nameMd5 .= $js;
                        $cache .= $js . "\n";
                    } else {
                        $nameMd5 .= $js;
                        if (strrpos($js, '/') === false)
                            $js = APPPATH . 'views/site/' . $this->name . '/js/' . $js;
                        if (preg_match('/https?:\/\//', $js))
                            $cache .= @file_get_contents($js) . "\n";
						elseif(file_exists($js))
							if(ASSETS_OPTIMIZATION & 8) $cache .= $this->minAsset($js) . "\n";
							else $cache .= @file_get_contents($js) . "\n";
                        /*else
                            $html .= "<script src=\"$js?v=" . ASSETS_VERSION . "\" type=\"text/javascript\" language=\"javascript\"></script>\n";*/
                    }
                }
                $nameMd5 = md5($nameMd5);
                $cacheMd5 = md5($cache);
                $file = APPPATH . 'views/site/' . $this->name . '/js/cache/' . $nameMd5 . '.js';
                if (file_exists($file)) {
                    if (ENVIRONMENT != 'production' && $cacheMd5 != md5_file($file))
                        file_put_contents($file, $cache);
                } else {
                    $folder = APPPATH . 'views/site/' . $this->name . '/js/cache/';
                    if (!is_dir($folder)) mkdir($folder, 0755, true);
                    file_put_contents($file, $cache);
                }
                $file = APPFOLDER . "/views/site/$this->name/js/cache/$nameMd5.js?v=$cacheMd5";
                $html .= "<script src=\"$file\" type=\"text/javascript\" language=\"javascript\"></script>\n";
            } else {
                foreach ($this->_js as $js) {
                    if (preg_match('/[;\(]/', $js)) {
                        $html .= "<script type=\"text/javascript\" language=\"javascript\">\n{$js}\n</script>\n";
                    } else {
                        if (strrpos($js, '/') === false)
                            $js = APPFOLDER . "/views/site/$this->name/js/$js";
                        $js .= '?v=' . ASSETS_VERSION;
                        $html .= "<script src=\"$js\" type=\"text/javascript\" language=\"javascript\"></script>\n";
                    }
                }
            }
        }
        return $html;
    }

	private function minAsset($file)
	{
		$pathInfo = pathinfo($file);
		if(substr($pathInfo['filename'], -4)=='.min')
			return @file_get_contents($file);
		$minFile = "$pathInfo[dirname]/$pathInfo[filename].min.$pathInfo[extension]";
		if(file_exists($minFile) && filemtime($minFile) > @filemtime($file))
			return @file_get_contents($minFile);
		switch(strtolower($pathInfo['extension'])) {
			case 'css':
				$minContent = CssMin::minify(@file_get_contents($file));
				break;
			case 'js':
				$minContent = JSMin::minify(@file_get_contents($file));
				break;
			default:
				return @file_get_contents($file);
		}
		file_put_contents($minFile, $minContent);
		return $minContent;
	}

    /*private function optimizeCSS($code)
    {
        return CssMin::minify($code);
    }

    private function optimizeJS($code)
    {
        return JSMin::minify($code);
    }*/

    private function &getModules($position = false)
    {
        static $modules;
        if (!isset($modules)) {
            $file = APPPATH . 'views' . DS . 'site' . DS . $this->name . DS . 'params' . DS . 'layouts' . DS . $this->layout . '.php';
            if (file_exists($file)) require $file;
            else $modules = array();
        }
        if ($position) {
            if (!isset($modules[$position]))
                $modules[$position] = array();
            return $modules[$position];
        } else {
            return $modules;
        }
    }

    public function countModule($position = false)
    {
        $modules = & $this->getModules($position);
        return count($modules);
    }

    public function loadModules($position)
    {
        $modules =& $this->getModules($position);
        if (count($modules)) {
            foreach ($modules as &$module) {
                $file = APPPATH . 'modules' . DS . $module['name'] . '.php';
                if(!file_exists($file) || (isset($module['status']) && !$module['status']) ||
                    (isset($module['begin']) && $module['begin'] > TIME_NOW) ||
                    (isset($module['expire']) && $module['expire'] <= TIME_NOW)
                ) {
                    continue;
                }
                $this->cache = false;
                $this->cacheData = new stdClass();
                require_once $file;
                $module['objMod'] = new $module['name'];
                $module['objMod']->view($module, $this);
                if ($this->cache) $this->saveCache();
            }
        }
    }

	public function loadModule($module)
	{
		$file = APPPATH . 'modules' . DS . $module['name'] . '.php';
		if(file_exists($file)) {
			$this->cache = false;
			$this->cacheData = new stdClass();
			require_once $file;
			$module['objMod'] = new $module['name'];
			$module['objMod']->view($module, $this);
			if ($this->cache) $this->saveCache();
		}
	}

	public function breadcrumb($list = false, $return = false)
	{
		static $items = array();
		if($list) {
			$items = $list;
			$list = true;
		}
		if(!$list || $return) {
			if(!$items) return false;
			$html = '';$seperator = false;
			foreach($items as $item) {
				if($seperator) $html .= $seperator . '</li>';
				if(isset($item['link']))
					$html .= "<li><a href=\"$item[link]\">$item[text]</a>";
				else $html .= "<li class=\"active\">$item[text]";
				if(isset($item['seperator'])) $seperator = '<span class="divider">' . $item['seperator'] . '</span>';
				else $seperator = '<span class="divider"> > </span>';
			}
			return '<ul class="breadcrumb">' . $html . '</li></ul>';
		}
	}

	/*############################################################*/

    public function cache($keys, $type = NULL)
    {
		if(!CACHE_VIEW) return false;

        $this->cache = true;
        $this->addPageKey($keys);
        $cache_path = $this->getCachePath();

        $this->cache_file = $cache_file = implode(array_keys($keys));
        $value_file = $cache_path . 'value' . DS . $cache_file;

        if (file_exists($value_file)) {
            $mTime = filemtime($value_file);
            if (!$this->cacheExpired($keys, $mTime)) {
                $CI =& get_instance();
                $CI->load->driver('cache', array('adapter' => 'myfile', 'backup' => 'file'));
                $CI->cache->myfile->setPath('myf' . DS . 'value' . DS);
                $cache = $CI->cache->get($cache_file);

                if (isset($cache->_doc_title) && $cache->_doc_title)
                    $this->_doc_title = $cache->_doc_title;

                if (isset($cache->_css) && is_array($cache->_css) && $cache->_css)
                    foreach ($cache->_css as $value)
                        $this->addCSS($value[0], $value[1], $value[2]);

                if (isset($cache->_js) && is_array($cache->_js) && $cache->_js)
                    foreach ($cache->_js as $value)
                        $this->addJS($value[0], $value[1], $value[2]);

                if (isset($cache->_meta_tag) && is_array($cache->_meta_tag) && $cache->_meta_tag)
                    foreach ($cache->_meta_tag as $value)
                        $this->addMetaTag($value[0], $value[1], $value[2]);

                switch ($type) {
                    case 'module':
                        echo $cache->html;
                        $this->cache = false;
                        break;
                    case 'return':
                        return $cache->html;
                        break;
                    default:
                        $this->html($cache->html);
                }
                return true;
            }
        }
        if ('module' == $type) ob_start();
        return false;
    }

    private function addPageKey($keys)
    {
        $this->pageKeys = array_merge($this->pageKeys, $keys);
    }

    public function savePageKeys()
    {
        $CI =& get_instance();
        $CI->load->driver('cache', array('adapter' => 'myfile', 'backup' => 'file'));
        $CI->cache->myfile->setPath('myf' . DS . 'key' . DS);
        $key = $CI->config->item('base_url') .
            $CI->config->item('index_page') .
            $CI->uri->uri_string();
        $CI->cache->save(md5($key), $this->pageKeys, 30000000000);
    }

    private function getCachePath()
    {
        static $cache_path;
        if (!isset($cache_path)) {
            $cache_path = APPPATH . 'cache' . DS;
            if (defined('SITE_NAME') && SITE_NAME) $cache_path .= SITE_NAME . DS;
            $cache_path .= 'myf' . DS;
        }
        return $cache_path;
    }

    private function cacheExpired($keys, $cacheTime)
    {
        $cache_path = $this->getCachePath();
        $key_path = $cache_path . 'key' . DS;
		$db_keys = array();
        foreach ($keys as $key => $type) {
            switch ($type) {
                case 'file':
                    $key_file = $key_path . $key;
                    if (file_exists($key_file) && filemtime($key_file) > $cacheTime) return true;
					//unset($keys[$key]);
                break;
            case 'db':
				$db_keys[] = $key;
            }
        }
		if($db_keys) {
			$CI =& get_instance();
			$CI->load->model('cache');
			return (bool)$CI->cache->expired($db_keys, $cacheTime);
		}
        return false;
    }

    public function saveCache($key = false, $html = false)
    {
        if (!$this->cache) return;
        $data = $this->cacheData;
        if (false === $html) {
            $data->html = ob_get_contents();
            ob_end_flush();
        } else {
            $data->html = $html;
        }
        if (!$key) $key = $this->cache_file;
        $CI =& get_instance();
        $CI->load->driver('cache', array('adapter' => 'myfile', 'backup' => 'file'));
        $CI->cache->myfile->setPath('myf' . DS . 'value' . DS);
        $CI->cache->save($key, $data, 30000000000);
    }
}