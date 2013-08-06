<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Config_Controller extends MY_Controller
{

	public function manager()
	{
		$this->hasPermit('system_config', 'admin/');

		$data['browser_title'] = 'Cấu hình hệ thống';
		$data['page_heading'] = 'Cấu hình hệ thống';

		$this->load->library('form');
		$data['message'] = $this->form->getMessage();

		$conditions = array();
		$data['filter_search'] = trim($this->form->getState('filter_search', '', 'sys_config'));
		if ($data['filter_search'])
			$conditions['name'] = $data['filter_search'];

		$this->load->model('cf');
		$data['rows'] = $this->cf->getConfigs($conditions);

		$this->load->view('admin/config/list', $data);
	}

	public function edit()
	{
		$this->hasPermit('system_config', 'admin/');

		$this->load->library('form');
		$base_url = $this->config->base_url();
		$id = $this->input->get('id');
		if (!$id) {
			$id = $this->input->post('cid');
			if ($id && is_array($id) && count($id))
				$id = $id[0];
			if (!$id) {
				$this->load->helper('url');
				$this->form->setMessage('Không tìm thấy "cấu hình" cần sửa.', 'alert');
				redirect($base_url . 'admin/config');
			}
		}

		$data['id'] = $id;
		$this->load->model('cf');
		$data['config'] = $this->cf->getConfigById($id);
		if ($data['config'] === false) {
			$this->load->helper('url');
			$this->form->setMessage('Không tìm thấy "cấu hình" cần sửa.', 'alert');
			redirect($base_url . 'admin/config');
		}

		$data['message'] = $this->form->getMessage();
		$data['browser_title'] = 'Sửa "cấu hình"';
		$data['page_heading'] = 'Sửa "cấu hình"';

		$this->load->view('admin/config/form', $data);
	}

	public function save($next_action = 1)
	{
		$this->hasPermit('system_config', 'admin/');

		$this->load->helper('url');
		$base_url = $this->config->base_url();
		$method = $_SERVER['REQUEST_METHOD'];
		switch ($method) {
			case 'POST':
				$id = $this->input->post('id');
				$value = $this->input->post($id);
				$this->load->model('cf');
				if (!$this->cf->update($id, $value)) {
					$data['id'] = $id;
					$data['value'] = $value;
					$data['config'] = $this->cf->getConfigById($id);
					$next_action = 0;
				}

				if ($next_action) {
					$this->load->library('form');
					$this->form->setMessage('Lưu "cấu hình" thành công.', 'success');
					switch ($next_action) {
						case 0:
							break;
						case 1:
							redirect($base_url . 'admin/editConfig?id=' . $id);
							break;
						case 2:
							redirect($base_url . 'admin/config');
							break;
						default:
							redirect($base_url . 'admin/config');
					}
				}

				$data['browser_title'] = 'Sửa "cấu hình"';
				$data['page_heading'] = 'Sửa "cấu hình"';

				$this->load->library('form');
				if ($this->cf->message)
					$data['message'] = $this->form->renderMessage($this->cf->message, 'Cảnh báo', 'warning');
				else $data['message'] = $this->form->renderMessage('Lỗi không lưu được "cấu hình".', 'Báo lỗi', 'error');
				$this->load->view('admin/config/form', $data);
				break;
			default:
				redirect($base_url . 'admin/config');
		}
	}

	public function saveAndClose()
	{
		$this->save(2);
	}
}
