<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cache extends MY_Model
{
	protected static $_table = 'cache_keys';

	public function updateKeys($keys)
	{
		$this->load_database();
		$this->db->where_in('key', $keys);
		$query = $this->db->get(self::$_table);
		$caches = $this->db_result($query->result(), 'key', 'time');
		$update = array();
		foreach ($keys as $key)
			if (isset($caches[$key])) $update[] = $key;

		if ($update) {
			$data = array('time' => TIME_NOW);
			$this->db->where_in('key', $update);
			if ($this->db->update(self::$_table, $data) !== false)
				$keys = array_diff($keys, $update);
			else return false;
		}
		if ($keys) {
			$data = array();
			foreach ($keys as $key)
				$data[] = array('key' => $key, 'time' => TIME_NOW);
			if ($this->db->insert_batch(self::$_table, $data) !== false)
				return true;
		} elseif ($update) return true;
		return false;

		//Bo di, luu tam de tham khao
		/*if (!is_array($keys)) $keys = array($keys);
		if (!is_dir($file_path = APPPATH . 'cache/' . (defined('SITE_NAME') && SITE_NAME ? SITE_NAME . '/' : '') . 'myf/key/'))
			mkdir($file_path, 0700, true);
		foreach ($keys as &$key) {
			file_put_contents($file_path . $key, '');
		}*/
	}

	public function expired($keys, $time = TIME_NOW)
	{
		$this->load_database();
		$this->db->from(static::$_table);
		$this->db->where('time >', $time);
		if(is_array($keys))
			$this->db->where_in('key', $keys);
		else
			$this->db->where('key', $keys);
		return $this->db->count_all_results();
	}
}