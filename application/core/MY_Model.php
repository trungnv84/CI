<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
class MY_Model extends CI_Model
{
	/*function __isset($key)
	{
		$CI =& get_instance();
		return isset($CI->$key);
	}*/

	public function getTableName()
	{
		return static::$_table;
	}

	public function load_database($name = null, $cache_on = false, $reference = true)
	{
		if (!isset($this->db)) {
			if (!defined('ENVIRONMENT') OR !file_exists($file_path = APPPATH . 'config/' . ENVIRONMENT . '/database.php'))
				if (!file_exists($file_path = APPPATH . 'config/database.php'))
					show_error('The configuration file database.php does not exist.');

			include $file_path;

			if (!isset($db) OR count($db) == 0)
				show_error('No database connection settings were found in the database config file.');

			if ($name) $name = strtolower(get_class($this));
			if ((($reference && !isset($db[$name])) && !isset(get_instance()->db) && !isset($this->db)) || ((!$reference || isset($db[$name])) && !isset($this->db))) {
				if ($reference && !isset($db[$name]))
					$this->load->database();
				else
					$this->db = $this->load->database(isset($db[$name]) ? $name : '', true);

				if (!$this->db->cachedir) {
					if (!is_dir($file_path = APPPATH . 'cache/' . (defined('SITE_NAME') && SITE_NAME ? SITE_NAME . '/' : '') . 'myf/db/'))
						mkdir($file_path, 0700, true);
					$this->db->cache_set_path($file_path);
				}
			}
		}

		if (!is_object($this->db->CACHE)) {
			require_once APPPATH . 'database/MY_DB_cache.php';
			$this->db->CACHE = new MY_DB_cache($this->db);
		}

		if ($cache_on) {
			$this->db->cache_on();
		}
	}

	public function select($fields = '*', $where = null, $order = null, $limit = null, $offset = null, $type = 'object')
	{
		$this->load_database();
		if ($fields && $fields != '*')
			$this->db->select($fields);
		if ($order) $this->db->order_by($order);
		$query = $this->db->get_where(static::$_table, $where, $limit, $offset);
		return $query->result($type);
	}

	public function getData($where = null, $order = null, $limit = null, $offset = null, $type = 'object')
	{
		return $this->select('*', $where, $order, $limit, $offset, $type);
	}

	public function getItems($where = null, $order = null, $limit = null, $offset = null, $type = 'object')
	{
		return $this->select('*', $where, $order, $limit, $offset, $type);
	}

	public function selectByIds($ids, $fields = '*', $order = null, $limit = null, $offset = null, $type = 'object', $key = 'id')
	{
		if (is_array($ids)) $ids = implode(',', $ids);
		$ids = "$key IN ($ids)";
		return $this->select($fields, $ids, $order, $limit, $offset, $type);
	}

	public function getItemsByIds($ids, $order = null, $limit = null, $offset = null, $type = 'object', $key = 'id')
	{
		return $this->selectByIds($ids, '*', $order, $limit, $offset, $type, $key);
	}

	public function selectOne($fields = '*', $where = null, $order = null, $offset = null, $type = 'object')
	{
		$this->load_database();
		if ($fields && $fields != '*')
			$this->db->select($fields);
		if ($order) $this->db->order_by($order);
		$query = $this->db->get_where(static::$_table, $where, 1, $offset);
		return $query->first_row($type);
	}

	public function getItem($where = null, $order = null, $offset = null, $type = 'object')
	{
		return $this->selectOne('*', $where, $order, $offset, $type);
	}

	public function selectOneById($id, $fields = '*', $type = 'object', $key = 'id')
	{
		return $this->selectOne($fields, array($key => $id), null, null, $type);
	}

	public function getItemById($id, $type = 'object', $key = 'id')
	{
		return $this->getItem(array($key => $id), null, null, $type);
	}

	public function total($where = null, $limit = null)
	{
		$this->load_database();
		$this->db->from(static::$_table);
		if ($where)
			$this->db->where($where);
		if ($limit)
			$this->db->limit($limit);
		return $this->db->count_all_results();
	}

	public function db_result($data, $key = 'id', $field = false)
	{
		$result = array();
		if (!empty($data)) {
			if (is_array(end($data)))
				if ($field !== false)
					if (is_array($field))
						foreach ($data as $row)
							if ($key)
								$result[$row[$key]] = array_intersect_key($row, array_flip($field));
							else
								$result[] = array_intersect_key($row, array_flip($field));
					else
						foreach ($data as $row)
							if ($key)
								$result[$row[$key]] = $row[$field];
							else
								$result[] = $row[$field];
				else
					foreach ($data as $row)
						if ($key)
							$result[$row[$key]] = $row;
						else
							$result[] = $row;
			else
				if ($field !== false)
					if (is_array($field))
						foreach ($data as $row)
							if ($key)
								$result[$row->$key] = (object)array_intersect_key((array)$row, array_flip($field));
							else
								$result[] = (object)array_intersect_key((array)$row, array_flip($field));
					else
						foreach ($data as $row)
							if ($key)
								$result[$row->$key] = $row->$field;
							else
								$result[] = $row->$field;
				else
					foreach ($data as $row)
						if ($key)
							$result[$row->$key] = $row;
						else
							$result[] = $row;
		}
		return $result;
	}

	public function insert($data)
	{
		$this->load->database();
		$this->db->insert(static::$_table, $data);
		$data['id'] = $this->db->insert_id();
		if ($data['id']) $this->updateKeysById($data['id']);
		return $data['id'];
	}

	public function update($id, $data, $update_key = true)
	{
		$this->load_database();
		if (is_array($id)) $where = 'id IN (' . implode(',', $id) . ')';
		elseif (is_numeric($id)) $where = array('id' => $id);
		else $where =& $id;
		$result = $this->db->update(static::$_table, $data, $where);
		if ($result && $update_key) {
			if ($update_key !== true) {
				if ($update_key == 'db')
					$id = $this->db_result($this->select('id', $where), false, 'id');
				else
					$id =& $update_key;
			}
			if ($id) $this->updateKeysById($id);
		}
		return $result;
	}

	public function delete($id, $update_key = true)
	{
		$this->load_database();
		if (is_array($id)) $where = 'id IN (' . implode(',', $id) . ')';
		elseif (is_numeric($id)) $where = array('id' => $id);
		else $where = $id;
		if ($update_key && $update_key !== true) {
			if ($update_key == 'db')
				$id = $this->db_result($this->select('id', $where), false, 'id');
			else
				$id =& $update_key;
		}
		$result = $this->db->delete(static::$_table, $where);
		if ($result && $update_key && $id) $this->updateKeysById($id);
		return $result;
	}

	public function updateKeys($keys)
	{
		$this->load->model('cache');
		return $this->cache->updateKeys($keys);
	}

	public function updateKeysById($id, $prefix = false)
	{
		$data = array(static::$_cKey[0]);
		if (is_array($id) && $id) foreach ($id as &$k) $data[] = static::$_cKey[1] . $k;
		elseif ($id) $data[] = static::$_cKey[1] . $id;
		if ($prefix) {
			$data[] = static::$_cKey[0] . $prefix;
			if (is_array($id) && $id) foreach ($id as &$k) $data[] = static::$_cKey[1] . $prefix . '_' . $k;
			elseif ($id) $data[] = static::$_cKey[1] . $prefix . '_' . $id;
		}
		return $this->updateKeys($data);
	}
}