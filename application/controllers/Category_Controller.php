<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Category_Controller extends MY_Controller
{
	protected $section_id = false;

	public function manager()
	{
		$this->load->model('category');
		$this->section_id = (int)$this->uri->segment(3);
		if (!$this->section_id || !array_key_exists($this->section_id, Category::$sections)) {
			$this->load->helper('url');
			$base_url = $this->config->base_url();
			redirect($base_url . 'admin/category/1');
		}

		$this->hasPermit("category_{$this->section_id}_manage", 'admin/');

		$category_type = mb_strtolower(Category::$sections[$this->section_id], 'UTF-8');
		$data['browser_title'] = 'Quản lý ' . $category_type;
		$data['page_heading'] = 'Quản lý ' . $category_type;
		$data['category_type'] = $category_type;
		$data['section_id'] = $this->section_id;

		$this->load->library('form');
		$data['message'] = $this->form->getMessage();
		$conditions = array('section_id' => $this->section_id);
		$data['filter_search'] = trim($this->form->getState('filter_search', '', 'category' . $this->section_id));
		if ($data['filter_search'])
			$conditions['name LIKE '] = '%' . $data['filter_search'] . '%';
		$data['sortable'] = !(bool)$data['filter_search'];

		$config['per_page'] = 30;
		$config['uri_segment'] = 4;
		$config['num_links'] = 7;
		$config['use_page_numbers'] = true;
		$config['base_url'] = 'admin/category/' . $this->section_id;
		$config['total_rows'] = $this->category->total($conditions);

		$this->load->library('pagination');
		$this->pagination->initialize($config);

		$data['pagination'] = $this->pagination->create_links();
		$data['cur_page'] = $this->pagination->cur_page;
		$this->form->setState('category_manage_cur_page', $this->pagination->cur_page);

		$offset = max(0, $this->pagination->cur_page - 1) * $config['per_page'];
		$data['rows'] = $this->category->getCategories($conditions, 'ordering', $config['per_page'], $offset);
		if ($data['rows']) {
			$data['hasMenu'] = (false === in_array($this->section_id, Category::$hasMenu) ? false : Category::$menuPrefix[$this->section_id]);
		}

		$this->load->view('admin/category/list', $data);
	}

	private function getCurPage()
	{
		static $cur_page;
		if (!isset($cur_page)) {
			$this->load->library('form');
			$cur_page = $this->form->getStateIntFromSession('cur_page', 1, 'category' . $this->section_id);
			if ($cur_page < 2) $cur_page = '';
		}
		return $cur_page;
	}

	public function add()
	{
		$this->load->model('category');
		$this->section_id = (int)$this->uri->segment(3);
		if (!$this->section_id || !array_key_exists($this->section_id, Category::$sections)) {
			$this->load->helper('url');
			$base_url = $this->config->base_url();
			redirect($base_url . 'admin/category/1');
		}

		$this->hasPermit("category_{$this->section_id}_manage", 'admin/');

		$data = array('category' => new stdClass());
		$data['category']->alias = $this->input->get('alias', '');

		$this->load->library('form');
		$data['message'] = $this->form->getMessage();
		$data['cur_page'] = $this->getCurPage();

		$category_type = lcfirst(Category::$sections[$this->section_id]);
		$data['browser_title'] = 'Thêm ' . $category_type;
		$data['page_heading'] = 'Thêm ' . $category_type;
		$data['section_id'] = $this->section_id;

		$data['categories'] = $this->category->selectAllCategoriesBySectionId($this->section_id);
		$data['orderings'] = $this->category->filterCategoriesByField($data['categories'], array('parent_id' => 0));
		if (count($data['orderings'])) {
			$data['orderFirst'] = $data['orderings'][0]->ordering;
			$data['orderLast'] = end($data['orderings'])->ordering + 1;
		} else $data['orderFirst'] = 1;

		$this->load->view('admin/category/form', $data);
	}

	public function edit()
	{
		$this->load->model('category');
		$base_url = $this->config->base_url();
		$this->section_id = (int)$this->uri->segment(3);
		if (!$this->section_id || !array_key_exists($this->section_id, Category::$sections)) {
			$this->load->helper('url');
			redirect($base_url . 'admin/category/1');
		}

		$this->hasPermit("category_{$this->section_id}_manage", 'admin/');

		$this->load->library('form');
		$data['cur_page'] = $this->getCurPage();
		$id = $this->input->get('id');
		if (!$id) {
			$id = $this->input->post('cid');
			if ($id && is_array($id) && count($id))
				$id = (int)$id[0];
			if (!$id) {
				$this->load->helper('url');
				$this->section_id = (int)$this->uri->segment(2);
				$category_type = lcfirst(Category::$sections[$this->section_id]);
				$this->form->setMessage("Không tìm thấy \"$category_type\" cần sửa.", 'alert');
				redirect($base_url . 'category/' . $this->section_id . '/' . $data['cur_page']);
			}
		}

		$data['category'] = $this->category->getCategoryById($id);
		if ($data['category'] === false) {
			$this->load->helper('url');
			$this->section_id = (int)$this->uri->segment(2);
			$category_type = lcfirst(Category::$sections[$this->section_id]);
			$this->form->setMessage("Không tìm thấy \"$category_type\" cần sửa.", 'alert');
			redirect($base_url . 'category/' . $this->section_id . '/' . $data['cur_page']);
		}
		$data['message'] = $this->form->getMessage();
		$data['categories'] = $this->category->selectAllCategoriesBySectionId($data['category']->section_id);

		$category_type = lcfirst(Category::$sections[$data['category']->section_id]);
		$data['browser_title'] = 'Sửa ' . $category_type;
		$data['page_heading'] = 'Sửa ' . $category_type;
		$data['section_id'] = $data['category']->section_id;

		$data['orderings'] = $this->category->filterCategoriesByField($data['categories'], array('parent_id' => $data['category']->parent_id));
		foreach ($data['categories'] as $k => $v)
			if ($data['category']->id == $v->id || in_array($data['category']->id, explode(',', $v->branch))) unset($data['categories'][$k]);
		if (count($data['orderings'])) {
			$data['orderFirst'] = $data['orderings'][0]->ordering;
			$data['orderLast'] = end($data['orderings'])->ordering + 1;
		} else $data['orderFirst'] = 1;

		$this->load->view('admin/category/form', $data);
	}

	public function get_ordering()
	{
		$this->hasPermit('banner_manage', 'admin/');

		$this->load->model('category');
		$parent_id = $this->input->get('parent_id');
		$section_id = $this->input->get('section_id');
		$data = new stdClass();
		$data->status = 1;
		$data->orderings = $this->category->getOrderingByParentId($section_id, $parent_id);
		if (!$data->orderings || count($data->orderings) == 0) {
			$data->orderFirst = $this->category->getCategoryById($parent_id);
			if ($data->orderFirst)
				$data->orderFirst = $data->orderFirst->ordering + 1;
			else
				$data->orderFirst = 1;
		}
		header('Content-type: application/json');
		$this->output->append_output(json_encode($data));
	}

	public function save($next_action = 1)
	{
		$this->section_id = (int)$this->input->post('section_id');
		$this->hasPermit("category_{$this->section_id}_manage", 'admin/');

		$this->load->helper('url');
		$base_url = $this->config->base_url();
		$method = $_SERVER['REQUEST_METHOD'];
		switch ($method) {
			case 'POST':
				$id = (int)$this->input->post('id');
				$data['name'] = $this->input->post('name');
				$data['alias'] = $this->input->post('alias');
				$data['section_id'] = (int)$this->input->post('section_id');
				$data['parent_id'] = (int)$this->input->post('parent_id');
				$data['branch'] = trim($this->input->post('branch'));
				$old_branch = trim($this->input->post('old_branch'));
				$data['level'] = (int)$this->input->post('level');
				$data['ordering'] = (int)$this->input->post('ordering');
				$old_ordering = (int)$this->input->post('old_ordering');
				$data['status'] = (int)$this->input->post('status');
				$data['keywords'] = trim($this->input->post('keywords'));
				$data['description'] = trim($this->input->post('description'));
				$this->load->model('category');
				if ($this->category->bindData($data)) {
					if ($id) {
						if ($this->category->update($id, $data)) {
							if ($old_ordering != $data['ordering'] || $old_branch != $data['branch'])
								$this->category->update_ordering(
									$id, $data['ordering'], $data['section_id'],
									$data['branch'], $old_branch
								);
						} else {
							$data['section_id'] = $this->input->post('section_id');
							$data['id'] = $id;
							$next_action = 0;
						}
					} else {
						if ($id = $this->category->insert($data)) {
							$this->category->update_ordering($id, $data['ordering'], $data['section_id']);
						} else $next_action = 0;
					}
				} else {
					$data['section_id'] = $this->input->post('section_id');
					$data['id'] = $id;
					$next_action = 0;
				}

				$category_type = lcfirst(Category::$sections[$data['section_id']]);

				if ($next_action) {
					$this->load->library('form');
					$this->form->setMessage("Lưu \"$category_type\" thành công.", 'success');
					switch ($next_action) {
						case 0:
							break;
						case 1:
							redirect($base_url . 'admin/editCategory/' . $data['section_id'] . '?id=' . $id);
							break;
						case 2:
							redirect($base_url . 'admin/category/' . $data['section_id'] . '/' . $this->getCurPage());
							break;
						case 3:
							redirect($base_url . 'admin/addCategory/' . $data['section_id']);
							break;
						default:
							redirect($base_url . 'admin/category/' . $data['section_id']);
					}
				}

				$data = array('category' => (object)$data);
				$data['section_id'] = $data['category']->section_id;
				if (isset($data['category']->id))
					$data['categories'] = $this->category->getCategories(
						array(
							'section_id' => $data['category']->section_id,
							'id !=' => $data['category']->id
						)
					);
				else
					$data['categories'] = $this->category->selectAllCategoriesBySectionId($data['category']->section_id);
				$data['browser_title'] = ($id ? 'Sửa ' : 'Thêm') . $category_type;
				$data['page_heading'] = ($id ? 'Sửa ' : 'Thêm') . $category_type;
				$data['orderings'] = $this->category->filterCategoriesByField($data['categories'], array('parent_id' => $data['category']->parent_id));
				if (count($data['orderings'])) {
					$data['orderFirst'] = $data['orderings'][0]->ordering;
					$data['orderLast'] = end($data['orderings'])->ordering + 1;
				} else $data['orderFirst'] = 1;
				$data['old_branch'] = $old_branch;
				$data['old_ordering'] = $old_ordering;
				$data['cur_page'] = $this->getCurPage();
				$this->load->library('form');
				if ($this->category->message)
					$data['message'] = $this->form->renderMessage($this->category->message, 'Cảnh báo', 'warning');
				else $data['message'] = $this->form->renderMessage("Lỗi không lưu được \"$category_type\".", 'Báo lỗi', 'error');
				$this->load->view('admin/category/form', $data);
				break;
			default:
				redirect($base_url . 'admin/category/' . $this->getCurPage());
		}
	}

	public function saveAndClose()
	{
		$this->save(2);
	}

	public function saveAndAdd()
	{
		$this->save(3);
	}

	public function reorder()
	{
		$section_id = (int)$this->input->post('section_id');

		$this->load->model('user');
		$this->load->model('category');
		$this->section_id = $section_id;
		if (!$this->section_id || !array_key_exists($this->section_id, Category::$sections) ||
			!$this->user->hasPermit("category_{$this->section_id}_manage")
		) {
			$this->output->append_output(json_encode(array('status' => 0)));
			return;
		}

		$this->hasPermit("category_{$this->section_id}_manage", 'admin/');

		$cur_page = (int)$this->input->post('cur_page');
		$branch_after = trim($this->input->post('branch_after'));
		$level_after = max(0, $branch_after ? substr_count($branch_after, ',') + 1 : 0);
		$order_after = (int)$this->input->post('order_after');
		$branch = trim($this->input->post('branch'));
		$level = max(0, $branch ? substr_count($branch, ',') + 1 : 0);
		$id = (int)$this->input->post('id');

		$children_branch = ($branch ? $branch . ',' : '') . $id;
		$children = $this->category->total("FIND_IN_SET($id,branch)>0") + 1; //"branch LIKE '$children_branch%'"
		$this->db = & $this->category->db;
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();
		if ($children > 1) {
			$this->db->query('SET @step=-1');
			if ($branch != $branch_after) {
				$parent_id_after = $branch_after ? end(explode(',', $branch_after)) : 0;
				$children_branch_after = ($branch_after ? $branch_after . ',' : '') . $id;
				$pos = strlen($children_branch) + 1;
				$this->db->query("UPDATE {$this->db->dbprefix}" . $this->category->getTableName() . " SET ordering=$order_after+(@step:=@step+1),
                parent_id = IF(id=$id, '$parent_id_after', parent_id),
                branch = IF(id=$id, '$branch_after', CONCAT('$children_branch_after', MID(branch, $pos))),
                level = level+$level_after-$level
                WHERE section_id=$section_id AND (id=$id OR FIND_IN_SET($id,branch)>0)
                ORDER BY ordering LIMIT $children"); //branch LIKE '$children_branch%'
				$children_branch = $children_branch_after;
			} else
				$this->db->query("UPDATE {$this->db->dbprefix}" . $this->category->getTableName() . " SET ordering=$order_after+(@step:=@step+1)
                WHERE section_id=$section_id AND (id=$id OR FIND_IN_SET($id,branch)>0)
                ORDER BY ordering LIMIT $children"); //branch LIKE '$children_branch%'
			$this->db->query("UPDATE {$this->db->dbprefix}" . $this->category->getTableName() . " SET ordering=ordering+$children WHERE section_id=$section_id
            AND ordering>=$order_after AND id!=$id AND FIND_IN_SET($id,branch)=0"); //branch NOT LIKE '$children_branch%'
		} else {
			if ($branch != $branch_after)
				$data = array(
					'ordering' => $order_after,
					'parent_id' => $branch_after ? end(explode(',', $branch_after)) : 0,
					'branch' => $branch_after,
					'level' => $level_after
				);
			else $data = array('ordering' => $order_after);
			$data['section_id'] = $section_id;
			$this->category->update($id, $data, false);
			$this->db->query("UPDATE {$this->db->dbprefix}" . $this->category->getTableName() . " SET ordering=ordering+1 WHERE section_id=$section_id
            AND ordering>=$order_after AND id!=$id");
		}
		$this->db->trans_complete();
		header('Content-type: application/json');
		if ($this->db->trans_status() === FALSE) {
			$this->output->append_output(json_encode(array('status' => 0)));
		} else {
			$this->db->trans_start();
			$this->db->query('SET @step=0');
			$this->db->query("UPDATE {$this->db->dbprefix}" . $this->category->getTableName() . " SET ordering=(@step:=@step+1) WHERE section_id=$section_id ORDER BY ordering");
			$this->db->trans_complete();
			$this->category->updateKeysById(null, $this->section_id);
			$this->output->append_output(json_encode(array('status' => 1, 'html' => $this->index_ajax($section_id, $cur_page))));
		}
	}

	private function index_ajax($section_id, $cur_page)
	{
		$this->hasPermit("category_{$section_id}_manage", 'admin/');

		$this->load->library('form');
		$conditions = array('section_id' => $section_id);
		$data['filter_search'] = $this->form->getState('filter_search', '', 'category' . $section_id);
		if ($data['filter_search'])
			$conditions['name LIKE '] = '%' . $data['filter_search'] . '%';

		$this->load->model('category');

		$offset = max(0, $cur_page - 1) * 30;
		$data['rows'] = $this->category->getCategories($conditions, 'ordering', 30, $offset);
		if ($data['rows']) {
			$data['category_type'] = mb_strtolower(Category::$sections[$section_id], 'UTF-8');
			$data['section_id'] = $section_id;
			$data['sortable'] = !(bool)$data['filter_search'];
			$data['hasMenu'] = (false === in_array($section_id, Category::$hasMenu) ? false : Category::$menuPrefix[$section_id]);
		}

		return $this->load->view('admin/category/list_ajax', $data, true);
	}

	public function publish($status = 1)
	{
		$this->section_id = (int)$this->uri->segment(3);
		$this->hasPermit("category_{$this->section_id}_manage", 'admin/');

		$this->load->helper('url');
		$this->load->library('form');
		$this->load->model('category');
		$cur_page = $this->getCurPage();
		$base_url = $this->config->base_url();
		$category_type = lcfirst(Category::$sections[$this->section_id]);
		$task = $status ? 'Hiện' : 'Ẩn';
		$id = $this->input->get('id');
		if (!$id) {
			$id = $this->input->post('cid');
			if ($id && is_array($id) && count($id))
				$id = $this->form->arrayId($id);
			if (!$id || !is_array($id) || count($id) == 0) {
				$this->form->setMessage("Không tìm thấy \"$category_type\" cần \"$task\".", 'alert');
				redirect($base_url . 'admin/category/' . $this->section_id . '/' . $cur_page);
			}
		}
		if ($this->category->update($id, array('status' => $status, 'section_id' => $this->section_id))) {
			$this->form->setMessage("\"$task\" các \"$category_type\" được chọn thành công.", 'success');
		} else {
			$this->form->setMessage("Lỗi không \"$task\" được các \"$category_type\" đượcc chọn.", 'error');
		}
		redirect($base_url . 'admin/category/' . $this->section_id . '/' . $cur_page);
	}

	public function unpublish()
	{
		$this->publish(0);
	}

	public function delete()
	{
		$this->section_id = (int)$this->uri->segment(3);
		$this->hasPermit("category_{$this->section_id}_manage", 'admin/');

		$this->load->helper('url');
		$this->load->library('form');
		$this->load->model('category');
		$cur_page = $this->getCurPage();
		$base_url = $this->config->base_url();
		$category_type = lcfirst(Category::$sections[$this->section_id]);
		$id = $this->input->get('id');
		if (!$id) {
			$id = $this->input->post('cid');
			if ($id && is_array($id) && count($id))
				$id = $this->form->arrayId($id);
			if (!$id || !is_array($id) || count($id) == 0) {
				$this->form->setMessage("Không tìm thấy \"$category_type\" cần \"Xóa\".", 'alert');
				redirect($base_url . 'admin/category/' . $this->section_id . '/' . $cur_page);
			}
		}
		if ($this->category->delete($id, $this->section_id)) {
			$this->form->setMessage("\"Xóa\" các \"$category_type\" được chọn thành công.", 'success');
		} else {
			$this->form->setMessage("Lỗi không \"Xóa\" được các \"$category_type\" được chọn.", 'error');
		}
		redirect($base_url . 'admin/category/' . $this->section_id . '/' . $cur_page);
	}
}
