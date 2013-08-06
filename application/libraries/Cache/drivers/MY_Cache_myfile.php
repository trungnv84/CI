<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package        CodeIgniter
 * @author        ExpressionEngine Dev Team
 * @copyright    Copyright (c) 2006 - 2012 EllisLab, Inc.
 * @license        http://codeigniter.com/user_guide/license.html
 * @link        http://codeigniter.com
 * @since        Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter file Caching Class
 *
 * @package        CodeIgniter
 * @subpackage    Libraries
 * @category    Core
 * @author        ExpressionEngine Dev Team
 * @link
 */

class MY_Cache_myfile extends CI_Driver
{

    protected $_cache_path;

    /**
     * Constructor
     */
    public function __construct()
    {
        $CI =& get_instance();
        $CI->load->helper('file');
        $path = $CI->config->item('cache_path');
        $this->_cache_path = ($path == '') ? APPPATH . 'cache/' . (SITE_NAME ? SITE_NAME . '/' : '') : $path;
        $this->makeCacheFolder();
    }

    // ------------------------------------------------------------------------

    /**
     * Set new path
     *
     * @param $path         path of cache file
     */
    public function setPath($path)
    {
        $this->_cache_path = APPPATH . 'cache/';
        if (SITE_NAME) $this->_cache_path .= SITE_NAME . '/';
        $this->_cache_path .= $path;
        $this->makeCacheFolder();
    }

    /**
     * Create cache folder if it does not exist.
     */
    private function makeCacheFolder()
    {
        if (!is_dir($this->_cache_path)) mkdir($this->_cache_path, 0755, true);
    }

    // ------------------------------------------------------------------------

    /**
     * Fetch from cache
     *
     * @param     mixed        unique key id
     * @return     mixed        data on success/false on failure
     */
    public function get($id)
    {
        if (!file_exists($this->_cache_path . $id)) {
            return FALSE;
        }
        $data = read_file($this->_cache_path . $id);
        if (preg_match('/\w:\d:/', $data)) {
            $id = @unserialize($data);
            if (false !== $id) $data = $id;
        }
        return $data;
    }

    // ------------------------------------------------------------------------

    /**
     * Save into cache
     *
     * @param     string        unique key
     * @param     mixed        data to store
     * @param     int            length of time (in seconds) the cache is valid
     *                        - Default is 60 seconds
     * @return     boolean        true on success/false on failure
     */
    public function save($id, $data, $ttl = 60)
    {
        if (!is_scalar($data)) {
            $data = serialize($data);
        }
        if (write_file($this->_cache_path . $id, $data)) {
            return TRUE;
        }
        return FALSE;
    }

    // ------------------------------------------------------------------------

    /**
     * Delete from Cache
     *
     * @param     mixed        unique identifier of item in cache
     * @return     boolean        true on success/false on failure
     */
    public function delete($id)
    {
        return unlink($this->_cache_path . $id);
    }

    // ------------------------------------------------------------------------

    /**
     * Clean the Cache
     *
     * @return     boolean        false on failure/true on success
     */
    public function clean()
    {
        return delete_files($this->_cache_path);
    }

    // ------------------------------------------------------------------------

    /**
     * Cache Info
     *
     * Not supported by file-based caching
     *
     * @param     string    user/filehits
     * @return     mixed     FALSE
     */
    public function cache_info($type = NULL)
    {
        return get_dir_file_info($this->_cache_path);
    }

    // ------------------------------------------------------------------------

    /**
     * Get Cache Metadata
     *
     * @param     mixed        key to get cache metadata on
     * @return     mixed        FALSE on failure, array on success.
     */
    public function get_metadata($id)
    {
        if (!file_exists($id = $this->_cache_path . $id)) {
            return FALSE;
        }

        $mtime = filemtime($id);
        $ctime = filectime($id);

        return array(
            'expire'    => null,
            'mtime'     => $mtime,
            'ctime'     => $ctime
        );

        return FALSE;
    }

    // ------------------------------------------------------------------------

    /**
     * Is supported
     *
     * In the file driver, check to see that the cache directory is indeed writable
     *
     * @return boolean
     */
    public function is_supported()
    {
        return is_really_writable($this->_cache_path);
    }

    // ------------------------------------------------------------------------
}
// End Class

/* End of file Cache_file.php */
/* Location: ./system/libraries/Cache/drivers/Cache_file.php */