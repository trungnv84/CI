<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Article_Controller extends MY_Controller
{
	public function listing()
	{
		$this->load->helper('url');
		$cat_id = (int)$this->uri->rsegment(3, 0);
		$url = $this->uri->segment(1, '');

		$conditions = array(
			'status' => 1,
			'(start_date = 0 OR start_date <= ' . TIME_NOW . ')' => null,
			'(end_date = 0 OR end_date > ' . TIME_NOW . ')' => null,
			"FIND_IN_SET($cat_id,branches) > " => 0
		);

		$this->load->model('article');
		$config['per_page'] = 12;
		$config['uri_rsegment'] = 4;
		$config['num_links'] = 7;
		$config['use_page_numbers'] = true;
		$config['base_url'] = preg_replace(array('/'. REWRITE_SUFFIX. '/', '/[-\/]trang-\d+/i'), '', $url);
		$config['first_url'] = $config['base_url'] . REWRITE_SUFFIX;
		$config['end_base_url'] = '-';
		$config['prefix'] = 'trang-';
		$config['suffix'] = REWRITE_SUFFIX;
		$config['total_rows'] = $this->article->total($conditions);

		$this->load->library('pager');
		$this->pager->initialize($config);
		$data['pagination'] = $this->pager->create_links();
		$offset = max(0, $this->pager->cur_page - 1) * $config['per_page'];
		$data['articles'] = $this->article->getArticles($conditions, 'show_date desc', $config['per_page'], $offset);

		$list = array(array('text' => 'Trang chủ', 'link' => $this->config->base_url(), 'seperator' => ' » '));
		$this->load->model('category');
		$data['cat'] = $this->category->getCategoryById($cat_id);
		if($data['cat']) {
			if($data['cat']->branch) {
				$data['cats'] = $this->category->selectCategories('id,name,alias,parent_id', "id IN ({$data['cat']->branch})");
				foreach($data['cats'] as $cat) {
					$list[] = array(
						'text' => $cat->name,
						'link' => $cat->alias . '-' . Category::$menuPrefix[1] . $cat->id . REWRITE_SUFFIX,
						'seperator' => ' » '
					);
				}
			}
			$list[] = array(
				'text' => $data['cat']->name,
				'link' => $data['cat']->alias . '-' . Category::$menuPrefix[1] . $data['cat']->id . REWRITE_SUFFIX,
				'seperator' => ' » '
			);
		}

		$this->load->library('theme');
		$this->theme->breadcrumb($list);
		$this->theme->view('article/listing', $data);
	}

	public function detail()
	{
		$this->load->helper('url');
		$id = (int)$this->uri->rsegment(3, 0);
		if($id) {
			$this->load->model('article');
			$data['article'] = $this->article->getArticleById($id);
			if($data['article']) {
				$list = array(array('text' => 'Trang chủ', 'link' => $this->config->base_url(), 'seperator' => ' » '));
				if($data['article']->branches) {
					$this->load->model('category');
					$data['cats'] = $this->category->selectCategories('id,name,alias,parent_id', "id IN({$data['article']->branches})");
					foreach($data['cats'] as $k => $cat) {
						if($k && !$cat->parent_id) break;
						$list[] = array(
							'text' => $cat->name,
							'link' => $cat->alias . '-' . Category::$menuPrefix[2] . $cat->id . REWRITE_SUFFIX,
							'seperator' => ' » '
						);
					}
				}

				$this->load->library('theme');
				$this->theme->breadcrumb($list);
				$this->theme->view('article/detail', $data);
				return;
			}
		}
		show_404();
	}

	public function manager()
	{
		$this->hasPermit('article_manage', 'admin/');

		$data['browser_title'] = 'Quản lý bài viết';
		$data['page_heading'] = 'Quản lý bài viết';

		$this->load->library('form');
		$data['message'] = $this->form->getMessage();

		$conditions = array();
		$data['filter_search'] = trim($this->form->getState('filter_search', '', 'article'));
		if ($data['filter_search'])
			$conditions['title LIKE '] = '%' . $data['filter_search'] . '%';

		$this->load->model('article');
		$config['per_page'] = 20;
		$config['uri_segment'] = 3;
		$config['num_links'] = 7;
		$config['use_page_numbers'] = true;
		$config['base_url'] = 'admin/article';
		$config['total_rows'] = $this->article->total($conditions);

		$this->load->library('pagination');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['cur_page'] = $this->pagination->cur_page;
		$this->form->setState('article_cur_page', $this->pagination->cur_page);

		$offset = max(0, $this->pagination->cur_page - 1) * 20;
		$data['rows'] = $this->article->getArticles($conditions, 'modify_date DESC', $config['per_page'], $offset);

		if ($data['rows']) {
			$this->load->model('category');
			$data['categories'] = $this->category->db_result($this->category->selectAllCategoriesBySectionId(1));
		}

		$this->load->view('admin/article/list', $data);
	}

	private function getCurPage()
	{
		static $cur_page;
		if (!isset($cur_page)) {
			$this->load->library('form');
			$cur_page = $this->form->getStateIntFromSession('cur_page', 1, 'article');
			if ($cur_page < 2) $cur_page = '';
		}
		return $cur_page;
	}

	public function add()
	{
		$this->hasPermit('article_manage', 'admin/');

		$this->load->library('form');
		$data['message'] = $this->form->getMessage();
		$data['cur_page'] = $this->getCurPage();

		$data['browser_title'] = 'Thêm bài viết';
		$data['page_heading'] = 'Thêm bài viết';

		$this->load->model('category');
		$data['categories'] = $this->category->selectAllCategoriesBySectionId(1);

		$this->load->view('admin/article/form', $data);
	}

	public function edit()
	{
		$this->hasPermit('article_manage', 'admin/');

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
				$this->form->setMessage('Không tìm thấy "bài viết" cần sửa.', 'alert');
				redirect($base_url . 'admin/article/' . $data['cur_page']);
			}
		}

		$this->load->model('article');
		$data['article'] = $this->article->getArticleById($id);
		if ($data['article'] === false) {
			$this->load->helper('url');
			$this->form->setMessage('Không tìm thấy "bài viết" cần sửa.', 'alert');
			redirect($base_url . 'admin/article/' . $data['cur_page']);
		}

		$data['message'] = $this->form->getMessage();
		$data['browser_title'] = 'Sửa bài viết';
		$data['page_heading'] = 'Sửa bài viết';

		$this->load->model('category');
		$data['categories'] = $this->category->selectAllCategoriesBySectionId(1);

		$this->load->view('admin/article/form', $data);
	}

	public function save($next_action = 1)
	{
		$this->hasPermit('article_manage', 'admin/');

		$this->load->helper('url');
		$base_url = $this->config->base_url();
		$method = $_SERVER['REQUEST_METHOD'];
		switch ($method) {
			case 'POST':
				$id = (int)$this->input->post('id');
				$data['title'] = $this->input->post('title');
				$data['alias'] = $this->input->post('alias');
				$data['cat_ids'] = $this->input->post('cat_ids');
				$data['branches'] = trim($this->input->post('branches'));
				$data['status'] = (int)$this->input->post('status');
				$data['intro'] = $this->input->post('intro');
				$data['content'] = $this->input->post('content');
				$data['start_date'] = trim($this->input->post('start_date'));
				if ($data['start_date']) $data['start_date'] = strtotime($data['start_date']);
				$data['end_date'] = trim($this->input->post('end_date'));
				if ($data['end_date']) $data['end_date'] = strtotime($data['end_date']);
				$data['show_date'] = trim($this->input->post('show_date'));
				if ($data['show_date']) $data['show_date'] = strtotime($data['show_date']);
				else $data['show_date'] = TIME_NOW;
				$data['keywords'] = $this->input->post('keywords');
				$data['description'] = $this->input->post('description');
				$this->load->model('article');
				if ($this->article->bindData($data)) {
					if ($id) {
						$data['modify_date'] = TIME_NOW;
						if (!$this->article->update($id, $data)) {
							$data['id'] = $id;
							$next_action = 0;
						}
					} else {
						$data['create_date'] = TIME_NOW;
						if (!$this->article->insert($data)) $next_action = 0;
					}
				} else {
					$data['id'] = $id;
					$next_action = 0;
				}

				if ($next_action) {
					$this->load->library('form');
					$this->form->setMessage('Lưu "bài viết" thành công.', 'success');
					switch ($next_action) {
						case 0:
							break;
						case 1:
							redirect($base_url . 'admin/editArticle?id=' . $id);
							break;
						case 2:
							redirect($base_url . 'admin/article/' . $this->getCurPage());
							break;
						case 3:
							redirect($base_url . 'admin/addArticle');
							break;
						default:
							redirect($base_url . 'admin/article');
					}
				}

				$data['images'] = trim($this->input->post('old_images'));
				$data = array('article' => (object)$data);

				$data['browser_title'] = ($id ? 'Sửa bài viết' : 'Thêm bài viết');
				$data['page_heading'] = ($id ? 'Sửa bài viết' : 'Thêm bài viết');

				$this->load->model('category');
				$data['categories'] = $this->category->selectAllCategoriesBySectionId(1);
				$data['cur_page'] = $this->getCurPage();

				$this->load->library('form');
				if ($this->article->message)
					$data['message'] = $this->form->renderMessage($this->article->message, 'Cảnh báo', 'warning');
				else $data['message'] = $this->form->renderMessage('Lỗi không lưu được "bài viết".', 'Báo lỗi', 'error');
				$this->load->view('admin/article/form', $data);
				break;
			default:
				redirect($base_url . 'admin/article/' . $this->getCurPage());
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

	public function publish($status = 1)
	{
		$this->hasPermit('article_manage', 'admin/');

		$this->load->library('form');
		$this->load->helper('url');
		$cur_page = $this->getCurPage();
		$base_url = $this->config->base_url();
		$task = $status ? 'Hiện' : 'Ẩn';
		$id = $this->input->get('id');
		if (!$id) {
			$id = $this->input->post('cid');
			if ($id && is_array($id) && count($id))
				$id = $this->form->arrayId($id);
			if (!$id || !is_array($id) || count($id) == 0) {
				$this->form->setMessage("Không tìm thấy \"bài viết\" cần \"$task\".", 'alert');
				redirect($base_url . 'admin/article/' . $cur_page);
			}
		}
		$this->load->model('article');
		if ($this->article->update($id, array('status' => $status))) {
			$this->form->setMessage("\"$task\" các \"bài viết\" được chọn thành công.", 'success');
		} else {
			$this->form->setMessage("Lỗi không \"$task\" được các \"bài viết\" được chọn.", 'error');
		}
		redirect($base_url . 'admin/article/' . $cur_page);
	}

	public function unpublish()
	{
		$this->publish(0);
	}

	public function delete()
	{
		$this->hasPermit('article_manage', 'admin/');

		$this->load->library('form');
		$this->load->helper('url');
		$cur_page = $this->getCurPage();
		$base_url = $this->config->base_url();
		$id = $this->input->get('id');
		if (!$id) {
			$id = $this->input->post('cid');
			if ($id && is_array($id) && count($id))
				$id = $this->form->arrayId($id);
			if (!$id || !is_array($id) || count($id) == 0) {
				$this->form->setMessage('Không tìm thấy "bài viết" cần "Xóa".', 'alert');
				redirect($base_url . 'admin/article/' . $cur_page);
			}
		}
		$this->load->model('article');
		if ($this->article->delete($id)) {
			$this->form->setMessage('"Xóa" các "bài viết" được chọn thành công.', 'success');
		} else {
			$this->form->setMessage('Lỗi không "Xóa" được các "bài viết" được chọn.', 'error');
		}
		redirect($base_url . 'admin/article/' . $cur_page);
	}

	public function branch()
	{
		$limit = 0;
		$status = 0;
		$message = '';
		$this->load->model('category');
		$categories = $this->category->db_result($this->category->selectAllCategoriesBySectionId(1));
		$this->load->model('article');
		$total = $this->article->total();
		$cursor = (int)$this->input->post('cursor');
		$this->load->library('form');
		if ($cursor < $total) {
			$limit = (int)($total / 10);
			if ($limit < 100) $limit = 100;
			elseif ($limit > 1000) $limit = 1000;
			$articles = $this->article->getArticles(null, null, $limit, $cursor);
			if ($articles) {
				$data = array();
				foreach ($articles as &$article) {
					if ($article->cat_ids = trim($article->cat_ids)) {
						$branches = array();
						$article->cat_ids = explode(',', $article->cat_ids);
						foreach ($article->cat_ids as &$cat_id)
							if (isset($categories[$cat_id]))
								$branches[] = $categories[$cat_id]->branch . ',' . $cat_id;
						$branches = implode(',', $branches);
						$branches = explode(',', $branches);
						$branches = implode(',', $this->form->arrayId($branches));
						$data[] = array(
							'id' => $article->id,
							'branches' => $branches
						);
					}
				}
				if ($this->db->update_batch($this->article->getTableName(), $data, 'id') !== false) {
					if ($cursor + $limit < $total)
						$status = 1;
					elseif ($this->input->is_ajax_request())
						$this->form->setMessage('Đã thực hiện "Chia nhánh" thành công.', 'success'); else {
						$base_url = $this->config->base_url();
						$this->form->redirect($base_url . 'admin/article/' . $this->getCurPage(), array('Đã thực hiện "Chia nhánh" thành công.', 'success'));
					}
				} else $message = 'Lỗi "Chia nhánh". Xin vui lòng thử lại.';
			} else $message = 'Không có "Bài viết" nào.';
		} else $this->form->setMessage('Đã thực hiện "Chia nhánh" thành công.', 'success');
		if ($this->input->is_ajax_request()) {
			echo json_encode(array('status' => $status, 'cursor' => $cursor, 'limit' => $limit, 'total' => $total, 'message' => $message));
		} else {
			$this->load->view('admin/article/branch', array('status' => $status, 'cursor' => $cursor, 'limit' => $limit, 'total' => $total, 'message' => $message, 'browser_title' => 'Chia nhánh các bài viết', 'page_heading' => 'Chia nhánh các bài viết'));
		}
	}
}
