<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Product_Controller extends MY_Controller
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

		$this->load->model('product');
		$config['per_page'] = 35;
		$config['uri_rsegment'] = 4;
		$config['num_links'] = 7;
		$config['use_page_numbers'] = true;
		$config['base_url'] = preg_replace(array('/' . REWRITE_SUFFIX . '/', '/[-\/]trang-\d+/i'), '', $url);
		$config['first_url'] = $config['base_url'] . REWRITE_SUFFIX;
		$config['end_base_url'] = '-';
		$config['prefix'] = 'trang-';
		$config['suffix'] = REWRITE_SUFFIX;
		$config['total_rows'] = $this->product->total($conditions);

		$this->load->library('pager');
		$this->pager->initialize($config);
		$data['pagination'] = $this->pager->create_links();
		$offset = max(0, $this->pager->cur_page - 1) * $config['per_page'];
		$data['products'] = $this->product->getProducts($conditions, 'ordering desc', $config['per_page'], $offset);

		$list = array(array('text' => 'Trang chủ', 'link' => $this->config->base_url(), 'seperator' => ' » '));
		$this->load->model('category');
		$data['cat'] = $this->category->getCategoryById($cat_id);
		if ($data['cat']) {
			if ($data['cat']->branch) {
				$data['cats'] = $this->category->selectCategories('id,name,alias,parent_id', "id IN ({$data['cat']->branch})");
				foreach ($data['cats'] as $cat) {
					$list[] = array(
						'text' => $cat->name,
						'link' => $cat->alias . '-' . Category::$menuPrefix[2] . $cat->id . REWRITE_SUFFIX,
						'seperator' => ' » '
					);
				}
			}
			$list[] = array(
				'text' => $data['cat']->name,
				'link' => $data['cat']->alias . '-' . Category::$menuPrefix[2] . $data['cat']->id . REWRITE_SUFFIX,
				'seperator' => ' » '
			);
		}

		$this->load->library('theme');
		$this->theme->breadcrumb($list);
		$this->theme->view('product/listing', $data);
	}

	public function detail()
	{
		$this->load->helper('url');
		$id = (int)$this->uri->rsegment(3, 0);
		if ($id) {
			$this->load->model('product');
			$data['product'] = $this->product->getProductById($id);
			if ($data['product']) {
				$list = array(array('text' => 'Trang chủ', 'link' => $this->config->base_url(), 'seperator' => ' » '));
				if ($data['product']->branches) {
					$this->load->model('category');
					$data['cats'] = $this->category->selectCategories('id,name,alias,parent_id', "id IN({$data['product']->branches})");
					foreach ($data['cats'] as $k => $cat) {
						if ($k && !$cat->parent_id) break;
						$list[] = array(
							'text' => $cat->name,
							'link' => $cat->alias . '-' . Category::$menuPrefix[2] . $cat->id . REWRITE_SUFFIX,
							'seperator' => ' » '
						);
					}
				}

				$this->load->library('theme');
				$this->theme->breadcrumb($list);
				$this->theme->view('product/detail', $data);
				return;
			}
		}
		show_404();
	}

	public function addCart()
	{
		$id = (int)$this->uri->rsegment(3, 0);
		$product = null;
		if ($id) {
			$this->load->model('product');
			$product = $this->product->getProductById($id);
			if ($product) {
				$this->load->library('Cart');
				$this->cart->insert(array(
					'id' => $product->id,
					'qty' => 1,
					'price' => $this->product->showPrice($product, 'raw'),
					'market_price' => $product->price,
					'name' => $product->name
				));
			}
		}
		if (!$product) {
			$this->load->library('form');
			$this->form->setMessage('Không tìm thấy "sản phẩm" bạn chọn.', 'alert');
		}
		$this->load->helper('url');
		redirect($this->config->base_url('gio-hang' . REWRITE_SUFFIX));
	}

	public function cart()
	{
        $this->load->library('Cart');
		if($_SERVER['REQUEST_METHOD']=='POST'){
			$this->cart->update($this->input->post());
		}
		$data['cart'] = & $this->cart;
		$list = array(
			array('text' => 'Trang chủ', 'link' => $this->config->base_url(), 'seperator' => ' » '),
			array('text' => 'Giỏ hàng', 'link' => 'gio-hang' . REWRITE_SUFFIX)
		);
		$this->load->library('theme');
		$this->theme->breadcrumb($list);
		$this->theme->view('product/cart', $data);
	}

    public function customer()
    {
        $this->load->library('Cart');
        $data['cart'] = & $this->cart;
        $list = array(
            array('text' => 'Trang chủ', 'link' => $this->config->base_url(), 'seperator' => ' » '),
            array('text' => 'Giỏ hàng', 'link' => 'gio-hang' . REWRITE_SUFFIX, 'seperator' => ' » '),
            array('text' => 'Đặt mua', 'link' => 'mua-hang' . REWRITE_SUFFIX)
        );
        $this->load->library('theme');
        $this->theme->breadcrumb($list);
        $this->theme->view('product/customer', $data);
    }

	public function manager()
	{
		$this->hasPermit('product_manage', 'admin/');

		$data['browser_title'] = 'Quản lý sản phẩm';
		$data['page_heading'] = 'Quản lý sản phẩm';

		$this->load->library('form');
		$data['message'] = $this->form->getMessage();

		$conditions = array();
		$data['filter_search'] = trim($this->form->getState('filter_search', '', 'product'));
		if ($data['filter_search'])
			$conditions['name LIKE '] = '%' . $data['filter_search'] . '%';
		$data['sortable'] = !(bool)$data['filter_search'];

		$this->load->model('product');
		$config['per_page'] = 20;
		$config['uri_segment'] = 3;
		$config['num_links'] = 7;
		$config['use_page_numbers'] = true;
		$config['base_url'] = 'admin/product';
		$config['total_rows'] = $this->product->total($conditions);

		$this->load->library('pagination');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['cur_page'] = $this->pagination->cur_page;
		$this->form->setState('product_cur_page', $this->pagination->cur_page);

		$offset = max(0, $this->pagination->cur_page - 1) * $config['per_page'];
		$data['rows'] = $this->product->getProducts($conditions, 'ordering desc', $config['per_page'], $offset);

		if ($data['rows']) {
			$this->load->model('category');
			$data['categories'] = $this->category->db_result($this->category->selectAllCategoriesBySectionId(2));
			$data['features'] = $this->product->getFeatures();
		}

		$this->load->view('admin/product/list', $data);
	}

	private function getCurPage()
	{
		static $cur_page;
		if (!isset($cur_page)) {
			$this->load->library('form');
			$cur_page = $this->form->getStateIntFromSession('cur_page', 1, 'product');
			if ($cur_page < 2) $cur_page = '';
		}
		return $cur_page;
	}

	public function add()
	{
		$this->hasPermit('product_manage', 'admin/');

		$this->load->library('form');
		$data['message'] = $this->form->getMessage();
		$data['cur_page'] = $this->getCurPage();

		$data['browser_title'] = 'Thêm sản phẩm';
		$data['page_heading'] = 'Thêm sản phẩm';

		$this->load->model('category');
		$data['categories'] = $this->category->selectAllCategoriesBySectionId(2);

		$this->load->model('product');
		$data['features'] = $this->product->getFeatures();

		$data['products'] = $this->product->getProducts(null, 'ordering DESC', 50);
		if ($data['products']) {
			$data['orderFirst'] = current($data['products']);
			$data['orderFirst'] = $data['orderFirst']->ordering + 1;
		} else $data['orderFirst'] = 1;

		$this->load->view('admin/product/form', $data);
	}

	public function edit()
	{
		$this->hasPermit('product_manage', 'admin/');

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
				$this->form->setMessage('Không tìm thấy "sản phẩm" cần sửa.', 'alert');
				redirect($base_url . 'admin/product/' . $data['cur_page']);
			}
		}

		$this->load->model('product');
		$data['product'] = $this->product->getProductById($id);
		if ($data['product'] === false) {
			$this->load->helper('url');
			$this->form->setMessage('Không tìm thấy "sản phẩm" cần sửa.', 'alert');
			redirect($base_url . 'admin/product/' . $data['cur_page']);
		}

		$data['message'] = $this->form->getMessage();
		$data['browser_title'] = 'Sửa sản phẩm';
		$data['page_heading'] = 'Sửa sản phẩm';

		$this->load->model('category');
		$data['categories'] = $this->category->selectAllCategoriesBySectionId(2);
		$data['features'] = $this->product->getFeatures();

		$data['products'] = $this->product->getProducts(null, 'ordering DESC', 50);
		if ($data['products']) {
			$data['orderFirst'] = current($data['products']);
			$data['orderFirst'] = $data['orderFirst']->ordering + 1;
		} else $data['orderFirst'] = 1;
		$data['total_products'] = $this->product->total();

		$this->load->view('admin/product/form', $data);
	}

	public function save($next_action = 1)
	{
		$this->hasPermit('product_manage', 'admin/');

		$this->load->helper('url');
		$base_url = $this->config->base_url();
		$method = $_SERVER['REQUEST_METHOD'];
		switch ($method) {
			case 'POST':
				$id = (int)$this->input->post('id');
				$data['name'] = $this->input->post('name');
				$data['alias'] = $this->input->post('alias');
				$data['code'] = trim($this->input->post('code'));
				$data['price'] = (int)trim($this->input->post('price'));
				$data['discount'] = (int)trim($this->input->post('discount'));
				$data['start'] = trim($this->input->post('start'));
				if ($data['start']) $data['start'] = strtotime($data['start']);
				$data['expire'] = trim($this->input->post('expire'));
				if ($data['expire']) $data['expire'] = strtotime($data['expire']);
				$data['cat_ids'] = $this->input->post('cat_ids');
				$data['branches'] = trim($this->input->post('branches'));
				$data['ordering'] = (int)$this->input->post('ordering');
				$old_ordering = (int)$this->input->post('old_ordering');
				$data['feature'] = (int)$this->input->post('feature');
				$data['status'] = (int)$this->input->post('status');
				$data['content'] = $this->input->post('content');
				$data['start_date'] = trim($this->input->post('start_date'));
				if ($data['start_date']) $data['start_date'] = strtotime($data['start_date']);
				$data['end_date'] = trim($this->input->post('end_date'));
				if ($data['end_date']) $data['end_date'] = strtotime($data['end_date']);
				$data['keywords'] = $this->input->post('keywords');
				$data['description'] = $this->input->post('description');
				$this->load->model('product');
				if ($this->product->bindData($data, $id)) {
					if ($id) {
						$data['modify_date'] = TIME_NOW;
						if ($this->product->update($id, $data)) {
							if ($old_ordering != $data['ordering'])
								$this->product->update_ordering($id, $data['ordering']);
						} else {
							$data['id'] = $id;
							$next_action = 0;
						}
					} else {
						$data['create_date'] = TIME_NOW;
						if ($id = $this->product->insert($data)) {
							$this->product->update_ordering($id, $data['ordering']);
						} else $next_action = 0;
					}
				} else {
					$data['id'] = $id;
					$next_action = 0;
				}

				if ($next_action) {
					$this->load->library('form');
					$this->form->setMessage('Lưu "sản phẩm" thành công.', 'success');
					switch ($next_action) {
						case 0:
							break;
						case 1:
							redirect($base_url . 'admin/editProduct?id=' . $id);
							break;
						case 2:
							redirect($base_url . 'admin/product/' . $this->getCurPage());
							break;
						case 3:
							redirect($base_url . 'admin/addProduct');
							break;
						default:
							redirect($base_url . 'admin/product');
					}
				}

				$data['images'] = trim($this->input->post('old_images'));
				$data = array('product' => (object)$data);

				$data['browser_title'] = ($id ? 'Sửa sản phẩm' : 'Thêm sản phẩm');
				$data['page_heading'] = ($id ? 'Sửa sản phẩm' : 'Thêm sản phẩm');

				$this->load->model('category');
				$data['categories'] = $this->category->selectAllCategoriesBySectionId(2);
				$data['features'] = $this->product->getFeatures();

				$data['products'] = $this->product->getProducts(null, 'ordering DESC', 50);
				if ($data['products']) {
					$data['orderFirst'] = current($data['products']);
					$data['orderFirst'] = $data['orderFirst']->ordering + 1;
				} else $data['orderFirst'] = 1;

				$data['old_ordering'] = $old_ordering;
				$data['cur_page'] = $this->getCurPage();

				$this->load->library('form');
				if ($this->product->message)
					$data['message'] = $this->form->renderMessage($this->product->message, 'Cảnh báo', 'warning');
				else $data['message'] = $this->form->renderMessage('Lỗi không lưu được "sản phẩm".', 'Báo lỗi', 'error');
				$this->load->view('admin/product/form', $data);
				break;
			default:
				redirect($base_url . 'admin/product/' . $this->getCurPage());
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
		$cur_page = (int)$this->input->post('cur_page');
		$order_after = (int)$this->input->post('order_after');
		$id = (int)$this->input->post('id');
		$this->load->model('product');
		$this->product->load_database();
		$this->db = & $this->product->db;
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();
		$this->product->update($id, array('ordering' => $order_after));
		$this->db->query("UPDATE {$this->db->dbprefix}" . $this->product->getTableName() . " SET ordering=ordering+1 WHERE ordering>=$order_after AND id!=$id");
		$this->db->trans_complete();
		header('Content-type: application/json');
		if ($this->db->trans_status() === FALSE) {
			$this->output->append_output(json_encode(array('status' => 0)));
		} else {
			$this->db->trans_start();
			$this->db->query('SET @step=0');
			$this->db->query("UPDATE {$this->db->dbprefix}" . $this->product->getTableName() . " SET ordering=(@step:=@step+1) ORDER BY ordering");
			$this->db->trans_complete();
			$this->output->append_output(json_encode(array('status' => 1, 'html' => $this->index_ajax($cur_page))));
		}
	}

	private function index_ajax($cur_page)
	{
		$this->load->library('form');
		$conditions = array();
		$data['filter_search'] = $this->form->getState('filter_search', '', 'product');
		if ($data['filter_search'])
			$conditions['name LIKE '] = '%' . $data['filter_search'] . '%';

		$this->load->model('product');

		$offset = max(0, $cur_page - 1) * 20;
		$data['rows'] = $this->product->getProducts($conditions, 'ordering DESC', 20, $offset);

		if ($data['rows']) {
			$this->load->model('category');
			$data['categories'] = $this->category->db_result($this->category->selectAllCategoriesBySectionId(2));
			$data['features'] = $this->product->getFeatures();
		}

		$data['sortable'] = !(bool)$data['filter_search'];

		return $this->load->view('admin/product/list_ajax', $data, true);
	}

	public function publish($status = 1)
	{
		$this->hasPermit('product_manage', 'admin/');

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
				$this->form->setMessage("Không tìm thấy \"sản phẩm\" cần \"$task\".", 'alert');
				redirect($base_url . 'admin/product/' . $cur_page);
			}
		}
		$this->load->model('product');
		if ($this->product->update($id, array('status' => $status))) {
			$this->form->setMessage("\"$task\" các \"sản phẩm\" được chọn thành công.", 'success');
		} else {
			$this->form->setMessage("Lỗi không \"$task\" được các \"sản phẩm\" được chọn.", 'error');
		}
		redirect($base_url . 'admin/product/' . $cur_page);
	}

	public function unpublish()
	{
		$this->publish(0);
	}

	public function delete()
	{
		$this->hasPermit('product_manage', 'admin/');

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
				$this->form->setMessage('Không tìm thấy "sản phẩm" cần "Xóa".', 'alert');
				redirect($base_url . 'admin/product/' . $cur_page);
			}
		}
		$this->load->model('product');
		if ($this->product->delete($id)) {
			$this->form->setMessage('"Xóa" các "sản phẩm" được chọn thành công.', 'success');
		} else {
			$this->form->setMessage('Lỗi không "Xóa" được các "sản phẩm" được chọn.', 'error');
		}
		redirect($base_url . 'admin/product/' . $cur_page);
	}

	public function branch()
	{
		$this->hasPermit('product_manage', 'admin/');

		$limit = 0;
		$status = 0;
		$message = '';
		$this->load->model('category');
		$categories = $this->category->db_result($this->category->selectAllCategoriesBySectionId(2));
		$this->load->model('product');
		$total = $this->product->total();
		$cursor = (int)$this->input->post('cursor');
		$this->load->library('form');
		if ($cursor < $total) {
			$limit = (int)($total / 10);
			if ($limit < 100) $limit = 100;
			elseif ($limit > 1000) $limit = 1000;
			$products = $this->product->getProducts(null, null, $limit, $cursor);
			if ($products) {
				$data = array();
				foreach ($products as &$product) {
					if ($product->cat_ids = trim($product->cat_ids)) {
						$branches = array();
						$product->cat_ids = explode(',', $product->cat_ids);
						foreach ($product->cat_ids as &$cat_id)
							if (isset($categories[$cat_id]))
								$branches[] = $categories[$cat_id]->branch . ',' . $cat_id;
						$branches = implode(',', $branches);
						$branches = explode(',', $branches);
						$branches = implode(',', array_unique($this->form->arrayId($branches)));
						$data[] = array(
							'id' => $product->id,
							'branches' => $branches
						);
					}
				}
				if ($this->product->db->update_batch($this->product->getTableName(), $data, 'id') !== false) {
					if ($cursor + $limit < $total)
						$status = 1;
					elseif ($this->input->is_ajax_request())
						$this->form->setMessage('Đã thực hiện "Chia nhánh" thành công.', 'success'); else {
						$base_url = $this->config->base_url();
						$this->form->redirect($base_url . 'admin/product/' . $this->getCurPage(), array('Đã thực hiện "Chia nhánh" thành công.', 'success'));
					}
				} else $message = 'Lỗi "Chia nhánh". Xin vui lòng thử lại.';
			} else $message = 'Không có "Sản phẩm" nào.';
		} else $this->form->setMessage('Đã thực hiện "Chia nhánh" thành công.', 'success');
		if ($this->input->is_ajax_request()) {
			echo json_encode(array('status' => $status, 'cursor' => $cursor, 'limit' => $limit, 'total' => $total, 'message' => $message));
		} else {
			$this->load->view('admin/product/branch', array('status' => $status, 'cursor' => $cursor, 'limit' => $limit, 'total' => $total, 'message' => $message, 'browser_title' => 'Chia nhánh các sản phẩm', 'page_heading' => 'Chia nhánh các sản phẩm'));
		}
	}
}
