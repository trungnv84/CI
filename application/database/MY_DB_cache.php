<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once BASEPATH . 'database/DB_cache.php';

/**
 * Class MY_DB_cache
 */
class MY_DB_cache extends CI_DB_Cache {

	/**
	 * Retrieve a cached query
	 *
	 * The URI being requested will become the name of the cache sub-folder.
	 * An MD5 hash of the SQL statement will become the cache file name
	 *
	 * @access	public
	 * @param 	$sql
	 * @return	string
	 */
	function read($sql)
	{
		if ( ! $this->check_path())
		{
			return $this->db->cache_off();
		}

        $filepath = $this->db->cachedir.md5($sql);
		if(!file_exists($filepath)) return FALSE;

		if(isset($this->db->cache_keys) && $this->db->cache_keys) {
			$CI =& get_instance();
			$CI->load->model('cache');
			$mTime = filemtime($filepath);
			if($CI->cache->expired($this->db->cache_keys, $mTime)) return FALSE;
		}

        //zzz bo phan cache key theo file
		/*$mixed = preg_split('/\sfrom\s/i', $sql);
        if(count($mixed)>1) {
            $mixed = preg_split('/\swhere\s/i', $mixed[1]);
            if(preg_match_all("/{$this->db->dbprefix}(\w+)/i", $mixed[0], $matches)) {
                $mTime = filemtime($filepath);
                $key_path = APPPATH . 'cache/';
                if (defined('SITE_NAME') && SITE_NAME) $key_path .= SITE_NAME . '/';
                $key_path .= 'myf/key/';
                foreach($matches[1] as $key) {
                    $key_file = $key_path . $key;
                    if (file_exists($key_file) && filemtime($key_file) > $mTime) return FALSE;
                }
            }
        }*/

		if (FALSE === ($cachedata = read_file($filepath)))
		{
			return FALSE;
		}

		return unserialize($cachedata);
	}

	// --------------------------------------------------------------------

	/**
	 * Write a query to a cache file
	 *
	 * @access	public
	 * @param $sql
	 * @param $object
	 * @return	bool
	 */
	function write($sql, $object)
	{
		if ( ! $this->check_path())
		{
			return $this->db->cache_off();
		}

		$dir_path = $this->db->cachedir;

		$filename = md5($sql);

		if ( ! @is_dir($dir_path))
		{
			if ( ! @mkdir($dir_path, DIR_WRITE_MODE))
			{
				return FALSE;
			}

			@chmod($dir_path, DIR_WRITE_MODE);
		}

		if (write_file($dir_path.$filename, serialize($object)) === FALSE)
		{
			return FALSE;
		}

		@chmod($dir_path.$filename, FILE_WRITE_MODE);
		return TRUE;
	}

}


/* End of file DB_cache.php */
/* Location: ./system/database/DB_cache.php */