<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Category extends MY_Model
{
	public static $sections = array(
		1 => 'Loại tin tức',
		2 => 'Nhóm sản phẩm',
		3 => 'Loại sản phẩm',
		4 => 'Loại địa điểm', //zzz???
		5 => 'Địa điểm',
		6 => 'Menu',
		7 => 'Nhóm banner'
	);

	public static $hasMenu = array(1, 2, 3, 5, 7);

	public static $menuPrefix = array(
		1 => 'ca',
		2 => 'cp',
		3 => 'tp',
		5 => 'cl',
		7 => 'cb'
	);

	protected static $_cKey = array('cats', 'cat');

	protected static $_table = 'categories';

	public $message = '';

	public function bindData(&$data)
	{
		$result = true;
		if (trim($data['name']) == '') {
			$this->message = 'Ban phải nhập tên.';
			$result = false;
		}
		if (trim($data['alias']) == '' && in_array($data['section_id'], self::$hasMenu)) {
			$data['alias'] = $data['name'];
			if (trim($data['alias']) != '') {
				$this->load->library('string');
				$data['alias'] = $this->string->stringURLSafe($data['alias']);
			}
		}
		return $result;
	}

	public function insert($data)
	{
		$this->load_database();
		$this->db->insert(self::$_table, $data);
		$data['id'] = $this->db->insert_id();
		if ($data['id']) $this->updateKeysById($data['id'], $data['section_id']);
		//$this->updateKeys(array(self::$_cKey[0], self::$_cKey[1] . $data['id']));
		return $data['id'];
	}

	public function update($id, $data, $update_key = true)
	{
		$section_id = $data['section_id'];
		unset($data['section_id']);
		$this->load_database();
		if (is_array($id)) $where = 'id IN (' . implode(',', $id) . ')';
		elseif (is_numeric($id)) $where = array('id' => $id);
		else $where =& $id;
		$result = $this->db->update(self::$_table, $data, $where);
		if ($result && $update_key) {
			if ($update_key !== true) {
				if ($update_key == 'db')
					$id = $this->db_result($this->select('id', $where), false, 'id');
				else
					$id =& $update_key;
			}
			if ($id) $this->updateKeysById($id, $section_id);
		}
		return $result;
	}

	public function delete($id, $section_id, $update_key = true)
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
		$result = $this->db->delete(self::$_table, $where);
		if ($result && $update_key && $id) $this->updateKeysById($id, $section_id);
		return $result;
	}

	public function totalBySectionId($section_id, $limit = null)
	{
		return $this->total(array('section_id' => $section_id), $limit);
	}

	public function getCategory($where = null, $order = null, $offset = null, $type = 'object')
	{
		return $this->getItem($where, $order, $offset, $type);
	}

	public function getCategoryById($id)
	{
		return $this->getItemById($id);
	}

	public function getCategories($where = null, $order = 'ordering', $limit = null, $offset = null, $type = 'object')
	{
		return $this->getData($where, $order, $limit, $offset, $type);
	}

	public function getCategoriesBySectionId($section_id, $order = 'ordering', $limit = null, $offset = null, $type = 'object')
	{
		return $this->getCategories(array('section_id' => $section_id), $order, $limit, $offset, $type);
	}

	public function getOrderingByParentId($section_id, $parent_id = 0)
	{
		return $this->getCategories(array('section_id' => $section_id, 'parent_id' => $parent_id));
	}

	public function filterCategoriesByField($categories, $conditions)
	{
		$result = array();
		foreach ($categories as $v) {
			$consistent = true;
			foreach ($conditions as $key => $condition) {
				if ($v->$key != $condition) {
					$consistent = false;
					break;
				}
			}
			if ($consistent) $result[] = $v;
		}
		return $result;
	}

	public function selectCategories($fields = '*', $where = null, $order = 'ordering', $limit = null, $offset = null, $type = 'object')
	{
		return $this->select($fields, $where, $order, $limit, $offset, $type);
	}

	public function selectAllCategoriesBySectionId($section_id, $fields = '*', $order = 'ordering', $type = 'object')
	{
		/*$this->load_database(null, true, false);
		$this->db->cache_keys = array(self::$_cKey[0] . $section_id);*/
		$result = $this->selectCategories($fields, array('section_id' => $section_id), $order, null, null, $type);
		/*unset($this->db->cache_keys);
		$this->db->cache_off();*/
		return $result;
	}

	public function update_ordering($id, $newOrder, $section_id, $branch = false, $old_branch = '')
	{
		$this->load_database();
		if ($branch !== false) {
			$old_children_branch = ($old_branch ? $old_branch . ',' : '') . $id;
			$children = $this->total("FIND_IN_SET($id,branch)>0");
			if ($children > 0) {
				$children_branch = ($branch ? $branch . ',' : '') . $id;
				$this->db->trans_strict(FALSE);
				$this->db->trans_start();
				$this->db->query('SET @step=0');
				if ($branch != $old_branch) {
					$pos = strlen($old_children_branch) + 1;
					$level = max(0, $branch ? substr_count($branch, ',') + 1 : 0);
					$oldLevel = max(0, $old_branch ? substr_count($old_branch, ',') + 1 : 0);
					$this->db->query(
						"UPDATE {$this->db->dbprefix}" . self::$_table . " SET ordering=$newOrder+(@step:=@step+1),
                    	branch = CONCAT('$children_branch', MID(branch, $pos)),
                    	level = level+$level-$oldLevel
                    	WHERE section_id=$section_id AND FIND_IN_SET($id,branch)>0
                    	ORDER BY ordering LIMIT $children");
				} else
					$this->db->query(
						"UPDATE {$this->db->dbprefix}" . self::$_table . " SET ordering=$newOrder+(@step:=@step+1)
                    	WHERE section_id=$section_id AND FIND_IN_SET($id,branch)>0
                    	ORDER BY ordering LIMIT $children");
				$children++;
				$this->db->query(
					"UPDATE {$this->db->dbprefix}" . self::$_table . " SET ordering=ordering+$children WHERE section_id=$section_id
                	AND ordering>=$newOrder AND id!=$id AND FIND_IN_SET($id,branch)=0");
				$this->db->trans_complete();
			} else
				$branch = false;
		}
		if ($branch === false)
			$this->db->query(
				"UPDATE {$this->db->dbprefix}" . self::$_table . " SET ordering=ordering+1
            	WHERE section_id=$section_id AND ordering>=$newOrder AND id!=$id");
		$this->db->trans_start();
		$this->db->query('SET @step=0');
		$this->db->query(
			"UPDATE {$this->db->dbprefix}" . self::$_table . " SET ordering=(@step:=@step+1)
        	WHERE section_id=$section_id ORDER BY ordering");
		$this->db->trans_complete();
	}

}