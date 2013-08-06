<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Article extends MY_Model
{

	protected static $_cKey = array('articles', 'article');

	protected static $_table = 'articles';

	public $message = '';

	private function saveImage($name, $file_name = false)
	{
		$config['upload_path'] = 'images/';
		if (defined('SITE_NAME') && SITE_NAME) $config['upload_path'] .= SITE_NAME . '/';
		$config['upload_path'] .= 'article/';
		$config['allowed_types'] = 'gif|jpg|jpeg|png';
		$config['max_size'] = '2048';
		$config['max_width'] = '1024';
		if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, true);
		if ($file_name) $config['file_name'] = $file_name;
		$this->load->library('upload', $config);
		if (!$this->upload->do_upload($name)) {
			return false;
		} else {
			$data = $this->upload->data();
			/*$data['file_path'] = strstr($data['file_path'], $config['upload_path']);
			$data['full_path'] = $data['file_path'] . $data['file_name'];
			return $data;*/
			return strstr($data['full_path'], substr($config['upload_path'], 7));
			/*return $data['file_name'];*/
		}
	}

	public function bindData(&$data)
	{
		$result = true;
		if (trim($data['title']) == '') {
			$this->message[] = 'Bạn phải nhập tiêu đề.';
			$result = false;
		}
		if (trim($data['alias']) == '') {
			$data['alias'] = $data['title'];
			if (trim($data['alias']) != '') {
				$this->load->library('string');
				$data['alias'] = $this->string->stringURLSafe($data['alias']);
			}
		}
		if (!isset($data['cat_ids'])) {
			$this->message[] = 'Bạn phải chọn ít nhất một loại tin.';
			$result = false;
		} elseif (is_array($data['cat_ids'])) $data['cat_ids'] = implode(',', $data['cat_ids']);
		if (isset($_FILES['images']) && isset($_FILES['images']['error']) && $_FILES['images']['error'] != 4) {
			$data['images'] = $this->saveImage('images', $data['alias']);
			if ($data['images'] === false) {
				$this->message[] = $this->upload->display_errors('<div>', '</div>');
				unset($data['images']);
				$result = false;
			}
		}
		if (!$result) {
			$this->message = implode('<br />', $this->message);
			$data['branches'] = explode(',', $data['branches']);
			$data['branches'] = array_unique($data['branches']);
			$data['branches'] = implode(',', $data['branches']);
		}
		return $result;
	}

	public function totalByCategoryId($category_id)
	{
		return $this->total("FIND_IN_SET($category_id, cat_ids)");
	}

	public function getArticleById($id)
	{
		return $this->getItemById($id);
	}

	public function getArticle($where = null, $order = null, $offset = null, $type = 'object')
	{
		return $this->getItem($where, $order, $offset, $type);
	}

	public function getArticles($where = null, $order = 'IF(modify_date > 0, modify_date, create_date) DESC', $limit = null, $offset = null, $type = 'object')
	{
		return $this->getData($where, $order, $limit, $offset, $type);
	}

}