<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Banner extends MY_Model {

	protected static $_cKey = array('banners', 'banner');

	protected static $_table = 'banners';

	public static $_types = array('Image', 'Flash', 'Nội dung');

	public $message = '';

    private function saveImage($name, $file_name=false) {
        $config['upload_path'] = 'images/';
		if(defined('SITE_NAME') && SITE_NAME) $config['upload_path'] .= SITE_NAME. '/';
		$config['upload_path'] .= 'banner/';
        $config['allowed_types'] = 'gif|jpg|jpeg|png|swf';
        $config['max_size']	= '2048';
        $config['max_width'] = '1024';
        if(!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, true);
        if($file_name) $config['file_name'] = $file_name;
        $this->load->library('upload', $config);
        if($this->upload->do_upload($name)){
			$data = $this->upload->data();
			/*$data['file_path'] = strstr($data['file_path'], $config['upload_path']);
			$data['full_path'] = $data['file_path'] . $data['file_name'];
			return $data;*/
			return strstr($data['full_path'], substr($config['upload_path'], 7));
			/*return $data['file_name'];*/
        } else {
			return false;
        }
    }

    public function bindData(&$data, $id=0) {
        $result = true;
        if(trim($data['name'])=='') {
            $this->message[] = 'Bạn phải nhập tên.';
            $result = false;
        }
        if(trim($data['alias'])=='') {
            $data['alias'] = $data['name'];
            if(trim($data['alias'])!='') {
                $this->load->library('string');
                $data['alias'] = $this->string->stringURLSafe($data['alias']);
            }
        }
        if(!$data['cat_id']) {
            $this->message[] = 'Bạn phải chọn một nhóm banner.';
            $result = false;
        }
        if(isset($_FILES['images']) && isset($_FILES['images']['error']) && $_FILES['images']['error']!=4) {
            $data['images'] = $this->saveImage('images', $data['alias']);
            if($data['images']===false) {
                $this->message[] = $this->upload->display_errors('<div>', '</div>');
                unset($data['images']);
                $result = false;
            }
        }
        if(!$result) {
            $this->message = implode('<br />', $this->message);
        }
        return $result;
    }

	public function totalByCategoryId($category_id)
	{
		return $this->total("FIND_IN_SET($category_id, cat_ids)");
	}

    public function getBannerById($id) {
		return $this->getItemById($id);
    }

	public function getBanner($where = null, $order = null, $offset = null, $type = 'object')
	{
		return $this->getItem($where, $order, $offset, $type);
	}

	public function getBanners($where = null, $order = 'ordering DESC', $limit = null, $offset = null, $type = 'object')
	{
		return $this->getData($where, $order, $limit, $offset, $type);
	}

    public function update_ordering($id, $newOrder, $cat_id) {
        $this->load->database();
        $this->db->trans_start();
        $this->db->query("UPDATE {$this->db->dbprefix}" . self::$_table . " SET ordering=ordering+1 WHERE cat_id=$cat_id AND ordering>=$newOrder AND id!=$id");
        $this->db->query('SET @step=0');
        $this->db->query("UPDATE {$this->db->dbprefix}" . self::$_table . " SET ordering=(@step:=@step+1) WHERE cat_id=$cat_id ORDER BY ordering");
        $this->db->trans_complete();
    }

}