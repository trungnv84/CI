<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Banner_Controller extends MY_Controller
{

	public function manager()
	{
		$this->hasPermit('banner_manage', 'admin/');

		$data['browser_title'] = 'Quản lý banner';
		$data['page_heading'] = 'Quản lý banner';

		$this->load->library('form');
		$data['message'] = $this->form->getMessage();

		$conditions = array();
		$data['sortable'] = false;
		$data['filter_search'] = trim($this->form->getState('filter_search', '', 'banner'));
		if ($data['filter_search'])
			$conditions['name LIKE '] = '%' . $data['filter_search'] . '%';
		$data['filter_cat_id'] = trim($this->form->getState('filter_cat_id', '', 'banner'));
		if ($data['filter_cat_id']) {
			$data['sortable'] = !(bool)$data['filter_search'];
			$conditions['cat_id'] = (int)$data['filter_cat_id'];
		}

		$this->load->model('banner');
		$config['per_page'] = 20;
		$config['uri_segment'] = 3;
		$config['num_links'] = 7;
		$config['use_page_numbers'] = true;
		$config['base_url'] = 'admin/banner';
		$config['total_rows'] = $this->banner->total($conditions);

		$this->load->library('pagination');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['cur_page'] = $this->pagination->cur_page;
		$this->form->setState('banner_cur_page', $this->pagination->cur_page);

		$offset = max(0, $this->pagination->cur_page - 1) * 20;
		$data['rows'] = $this->banner->getBanners($conditions, 'ordering desc', $config['per_page'], $offset);

		if ($data['rows']) {
			$this->load->model('category');
			$data['categories'] = $this->category->db_result($this->category->selectAllCategoriesBySectionId(7));
		}

		$this->load->view('admin/banner/list', $data);
	}

	private function getCurPage()
	{
		static $cur_page;
		if (!isset($cur_page)) {
			$this->load->library('form');
			$cur_page = $this->form->getStateIntFromSession('cur_page', 1, 'banner');
			if ($cur_page < 2) $cur_page = '';
		}
		return $cur_page;
	}

	public function add()
	{
		$this->hasPermit('banner_manage', 'admin/');

		$this->load->library('form');
		$data['message'] = $this->form->getMessage();
		$data['cur_page'] = $this->getCurPage();

		$data['browser_title'] = 'Thêm banner';
		$data['page_heading'] = 'Thêm banner';

		$this->load->model('category');
		$data['categories'] = $this->category->selectAllCategoriesBySectionId(7);

		$this->load->model('banner');
		$data['banners'] = array();//$this->banner->getBanners(null, 'ordering DESC', 50);
		if ($data['banners']) {
			$data['orderFirst'] = current($data['banners']);
			$data['orderFirst'] = $data['orderFirst']->ordering + 1;
		} else $data['orderFirst'] = 1;

		$this->load->view('admin/banner/form', $data);
	}

	public function edit()
	{
		$this->hasPermit('banner_manage', 'admin/');

		$this->load->library('form');
		$base_url = $this->config->base_url();
		$data['cur_page'] = $this->getCurPage();
		$id = $this->input->get('id');
		if (!$id) {
			$id = $this->input->post('cid');
			if ($id && is_array($id) && count($id))
				$id = (int)$id[0];
			if (!$id) {
				$this->load->helper('url');
				$this->form->setMessage('Không tìm thấy "banner" cần sửa.', 'alert');
				redirect($base_url . 'admin/banner/' . $data['cur_page']);
			}
		}

		$this->load->model('banner');
		$data['banner'] = $this->banner->getBannerById($id);
		if ($data['banner'] === false) {
			$this->load->helper('url');
			$this->form->setMessage('Không tìm thấy "banner" cần sửa.', 'alert');
			redirect($base_url . 'admin/banner/' . $data['cur_page']);
		}

		$data['message'] = $this->form->getMessage();
		$data['browser_title'] = 'Sửa banner';
		$data['page_heading'] = 'Sửa banner';

		$this->load->model('category');
		$data['categories'] = $this->category->selectAllCategoriesBySectionId(7);

		$data['banners'] = $this->banner->getBanners(array('cat_id' => $data['banner']->cat_id), 'ordering DESC', 50);
		if ($data['banners']) {
			$data['orderFirst'] = current($data['banners']);
			$data['orderFirst'] = $data['orderFirst']->ordering + 1;
		} else $data['orderFirst'] = 1;
		$data['total_banners'] = $this->banner->total();

		$this->load->view('admin/banner/form', $data);
	}

	public function get_ordering()
	{
		$this->hasPermit('banner_manage', 'admin/');

		$this->load->model('banner');
		$parent_id = (int)$this->input->get('parent_id');
		$data = new stdClass();
		$data->status = 1;
		$data->orderings = $this->banner->getBanners(array('cat_id' => $parent_id));
		if (!$data->orderings || count($data->orderings) == 0)
			$data->orderFirst = 1;
		header('Content-type: application/json');
		$this->output->append_output(json_encode($data));
	}

	public function save($next_action = 1)
	{
		$this->hasPermit('banner_manage', 'admin/');

		$this->load->helper('url');
		$base_url = $this->config->base_url();
		$method = $_SERVER['REQUEST_METHOD'];
		switch ($method) {
			case 'POST':
				$id = (int)$this->input->post('id');
				$data['name'] = $this->input->post('name');
				$data['alias'] = $this->input->post('alias');
				$data['cost'] = (int)trim($this->input->post('cost'));
				$data['cat_id'] = (int)$this->input->post('cat_id');
				$data['branch'] = trim($this->input->post('branch'));
				$data['ordering'] = (int)$this->input->post('ordering');
				$old_ordering = (int)$this->input->post('old_ordering');
				$data['status'] = (int)$this->input->post('status');
				$data['type'] = (int)$this->input->post('type');
				$data['content'] = $this->input->post('content');
				$data['start_date'] = trim($this->input->post('start_date'));
				if ($data['start_date']) $data['start_date'] = strtotime($data['start_date']);
				$data['end_date'] = trim($this->input->post('end_date'));
				if ($data['end_date']) $data['end_date'] = strtotime($data['end_date']);
				$data['keywords'] = $this->input->post('keywords');
				$data['description'] = $this->input->post('description');
				$this->load->model('banner');
				if ($this->banner->bindData($data, $id)) {
					if ($id) {
						$data['modify_date'] = TIME_NOW;
						if ($this->banner->update($id, $data)) {
							if ($old_ordering != $data['ordering'])
								$this->banner->update_ordering($id, $data['ordering'], $data['cat_id']);
						} else {
							$data['id'] = $id;
							$next_action = 0;
						}
					} else {
						$data['create_date'] = TIME_NOW;
						if ($id = $this->banner->insert($data)) {
							$this->banner->update_ordering($id, $data['ordering'], $data['cat_id']);
						} else $next_action = 0;
					}
				} else {
					$data['id'] = $id;
					$next_action = 0;
				}

				if ($next_action) {
					$this->load->library('form');
					$this->form->setMessage('Lưu "banner" thành công.', 'success');
					switch ($next_action) {
						case 0:
							break;
						case 1:
							redirect($base_url . 'admin/editBanner?id=' . $id);
							break;
						case 2:
							redirect($base_url . 'admin/banner/' . $this->getCurPage());
							break;
						case 3:
							redirect($base_url . 'admin/addBanner');
							break;
						default:
							redirect($base_url . 'admin/banner');
					}
				}

				$data['images'] = trim($this->input->post('old_images'));
				$data = array('banner' => (object)$data);

				$data['browser_title'] = ($id ? 'Sửa banner' : 'Thêm banner');
				$data['page_heading'] = ($id ? 'Sửa banner' : 'Thêm banner');

				$this->load->model('category');
				$data['categories'] = $this->category->selectAllCategoriesBySectionId(7);
				$data['features'] = $this->banner->getFeatures();

				$data['banners'] = $this->banner->getBanners(null, 'ordering DESC', 50);
				if ($data['banners']) {
					$data['orderFirst'] = current($data['banners']);
					$data['orderFirst'] = $data['orderFirst']->ordering + 1;
				} else $data['orderFirst'] = 1;

				$data['old_ordering'] = $old_ordering;
				$data['cur_page'] = $this->getCurPage();

				$this->load->library('form');
				if ($this->banner->message)
					$data['message'] = $this->form->renderMessage($this->banner->message, 'Cảnh báo', 'warning');
				else $data['message'] = $this->form->renderMessage('Lỗi không lưu được "banner".', 'Báo lỗi', 'error');
				$this->load->view('admin/banner/form', $data);
				break;
			default:
				redirect($base_url . 'admin/banner/' . $this->getCurPage());
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
		$this->hasPermit('banner_manage', 'admin/');

		$cur_page = (int)$this->input->post('cur_page');
		$order_after = (int)$this->input->post('order_after');
		$id = (int)$this->input->post('id');
		$cat_id = (int)$this->input->post('cat_id');
		$this->load->model('banner');
		$this->banner->load_database();
		$this->db = & $this->banner->db;
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();
		$this->banner->update($id, array('ordering' => $order_after));
		$this->db->query("UPDATE {$this->db->dbprefix}" . $this->banner->getTableName() . " SET ordering=ordering+1 WHERE cat_id=$cat_id AND ordering>=$order_after AND id!=$id");
		$this->db->trans_complete();
		header('Content-type: application/json');
		if ($this->db->trans_status() === FALSE) {
			$this->output->append_output(json_encode(array('status' => 0)));
		} else {
			$this->db->trans_start();
			$this->db->query('SET @step=0');
			$this->db->query("UPDATE {$this->db->dbprefix}" . $this->banner->getTableName() . " SET ordering=(@step:=@step+1) WHERE cat_id=$cat_id ORDER BY ordering");
			$this->db->trans_complete();
			$this->output->append_output(json_encode(array('status' => 1, 'html' => $this->index_ajax($cur_page))));
		}
	}

	private function index_ajax($cur_page)
	{
		$this->load->library('form');
		$conditions = array();
		$data['filter_search'] = $this->form->getState('filter_search', '', 'banner');
		if ($data['filter_search'])
			$conditions['name LIKE '] = '%' . $data['filter_search'] . '%';

		$this->load->model('banner');

		$offset = max(0, $cur_page - 1) * 20;
		$data['rows'] = $this->banner->getBanners($conditions, 'ordering DESC', 20, $offset);

		if($data['rows']) {
			$this->load->model('category');
			$data['categories'] = $this->category->db_result($this->category->selectAllCategoriesBySectionId(7));
			$data['features'] = $this->banner->getFeatures();
		}

		$data['sortable'] = !(bool)$data['filter_search'];

		return $this->load->view('admin/banner/list_ajax', $data, true);
	}

	public function publish($status = 1)
	{
		$this->hasPermit('banner_manage', 'admin/');

		$this->load->helper('url');
		$this->load->library('form');
		$cur_page = $this->getCurPage();
		$base_url = $this->config->base_url();
		$task = $status ? 'Hiện' : 'Ẩn';
		$id = $this->input->get('id');
		if (!$id) {
			$id = $this->input->post('cid');
			if ($id && is_array($id) && count($id))
				$id = $this->form->arrayId($id);
			if (!$id || !is_array($id) || count($id) == 0) {
				$this->form->setMessage("Không tìm thấy \"banner\" cần \"$task\".", 'alert');
				redirect($base_url . 'admin/banner/' . $cur_page);
			}
		}
		$this->load->model('banner');
		if ($this->banner->update($id, array('status' => $status))) {
			$this->form->setMessage("\"$task\" các \"banner\" được chọn thành công.", 'success');
		} else {
			$this->form->setMessage("Lỗi không \"$task\" được các \"banner\" được chọn.", 'error');
		}
		redirect($base_url . 'admin/banner/' . $cur_page);
	}

	public function unpublish()
	{
		$this->publish(0);
	}

	public function delete()
	{
		$this->hasPermit('banner_manage', 'admin/');

		$this->load->helper('url');
		$this->load->library('form');
		$cur_page = $this->getCurPage();
		$base_url = $this->config->base_url();
		$id = $this->input->get('id');
		if (!$id) {
			$id = $this->input->post('cid');
			if ($id && is_array($id) && count($id))
				$id = $this->form->arrayId($id);
			if (!$id || !is_array($id) || count($id) == 0) {
				$this->form->setMessage('Không tìm thấy "banner" cần "Xóa".', 'alert');
				redirect($base_url . 'admin/banner/' . $cur_page);
			}
		}
		$this->load->model('banner');
		if ($this->banner->delete($id)) {
			$this->form->setMessage('"Xóa" các "banner" được chọn thành công.', 'success');
		} else {
			$this->form->setMessage('Lỗi không "Xóa" được các "banner" được chọn.', 'error');
		}
		redirect($base_url . 'admin/banner/' . $cur_page);
	}

	public function branch()
	{
		$this->hasPermit('banner_manage', 'admin/');

		$limit = 0;
		$status = 0;
		$message = '';
		$this->load->model('category');
		$categories = $this->category->db_result($this->category->selectAllCategoriesBySectionId(7));
		$this->load->model('banner');
		$total = $this->banner->total();
		$cursor = (int)$this->input->post('cursor');
		$this->load->library('form');
		if ($cursor < $total) {
			$limit = (int)($total / 10);
			if ($limit < 100) $limit = 100;
			elseif ($limit > 1000) $limit = 1000;
			$banners = $this->banner->getBanners(null, null, $limit, $cursor);
			if ($banners) {
				$data = array();
				foreach ($banners as &$banner) {
					if ($banner->cat_id) {
						$branch = $categories[$banner->cat_id]->branch;
						if($branch) $branch .= ',';
						$branch .= $banner->cat_id;
						$data[] = array(
							'id' => $banner->id,
							'branch' => $branch
						);
					}
				}
				if ($this->banner->db->update_batch($this->banner->getTableName(), $data, 'id') !== false) {
					if ($cursor + $limit < $total)
						$status = 1;
					elseif ($this->input->is_ajax_request())
						$this->form->setMessage('Đã thực hiện "Chia nhánh" thành công.', 'success');
					else {
						$base_url = $this->config->base_url();
						$this->form->redirect($base_url . 'admin/banner/' . $this->getCurPage(), array('Đã thực hiện "Chia nhánh" thành công.', 'success'));
					}
				} else $message = 'Lỗi "Chia nhánh". Xin vui lòng thử lại.';
			} else $message = 'Không có "Sản phẩm" nào.';
		} else $this->form->setMessage('Đã thực hiện "Chia nhánh" thành công.', 'success');
		if ($this->input->is_ajax_request()) {
			echo json_encode(array('status' => $status, 'cursor' => $cursor, 'limit' => $limit, 'total' => $total, 'message' => $message));
		} else {
			$this->load->view('admin/banner/branch', array('status' => $status, 'cursor' => $cursor, 'limit' => $limit, 'total' => $total, 'message' => $message, 'browser_title' => 'Chia nhánh các banner', 'page_heading' => 'Chia nhánh các banner'));
		}
	}
}
