<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Product extends MY_Model {

	protected static $_cKey = array('products', 'product');

	protected static $_table = 'products';

	public $message = '';

    private function saveImage($name, $file_name=false) {
        $config['upload_path'] = 'images/';
		if(defined('SITE_NAME') && SITE_NAME) $config['upload_path'] .= SITE_NAME. '/';
		$config['upload_path'] .= 'product/';
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
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

    public function getFeatures() {
        return array(
            'Không có',
            'Trang chủ',
            'Hàng mới',
            'Hàng hot',
			'Bán chạy'
        );
    }

    public function bindData(&$data, $id=0) {
        $result = true;
        if($data['code']!='') {
            $this->load->database();
            $query = $this->db->limit(1)->get_where(self::$_table, array('code' => $data['code'], 'id !=' => $id));
            if($query->num_rows()!==0){
                $this->message[] = "Mã sản phẩm \"$data[code]\" đã được sử dụng.";
                $result = false;
            }
        }
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
        if(!isset($data['cat_ids'])) {
            $this->message[] = 'Bạn phải chọn ít nhất một nhóm.';
            $result = false;
        } elseif(is_array($data['cat_ids'])) $data['cat_ids'] = implode(',', $data['cat_ids']);
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

    public function getProductById($id) {
		return $this->getItemById($id);
    }

	public function getProduct($where = null, $order = null, $offset = null, $type = 'object')
	{
		return $this->getItem($where, $order, $offset, $type);
	}

	public function getProducts($where = null, $order = 'ordering DESC', $limit = null, $offset = null, $type = 'object')
	{
		return $this->getData($where, $order, $limit, $offset, $type);
	}

    public function update_ordering($id, $newOrder) {
        $this->load->database();
        $this->db->trans_start();
        $this->db->query("UPDATE {$this->db->dbprefix}" . self::$_table . " SET ordering=ordering+1 WHERE ordering>=$newOrder AND id!=$id");
        $this->db->query('SET @step=0');
        $this->db->query("UPDATE {$this->db->dbprefix}" . self::$_table . " SET ordering=(@step:=@step+1) ORDER BY ordering");
        $this->db->trans_complete();
    }

    public function showPrice(&$product, $return=false){
        if(!$product->discount || ($product->expire && $product->expire<=TIME_NOW))
            $price = $product->price;
        else {
            if($product->discount>99)
                $price = $product->discount;
            elseif($product->discount>0)
                $price = $product->price*(100-$product->discount)/100;
            else
                $price = $product->price+$product->discount;
        }
        if($return==='raw') return $price;
        else {
			$this->load->library('form');
            $price = $this->form->price_format($price);
            if($return) return $price;
        }
        echo $price;
    }

}