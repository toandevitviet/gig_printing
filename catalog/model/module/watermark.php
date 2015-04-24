<?php 
class ModelModuleWatermark extends Model {

	public function __construct($register) {
	
		if (!defined('WATERMARK_ROOTPATH')) define('WATERMARK_ROOTPATH', substr(DIR_APPLICATION, 0, strrpos(DIR_APPLICATION, '/', -2)) . '/');
		
		parent::__construct($register);
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `key` = 'watermark_settings'");
		
		if(! $query->num_rows)
			$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `group` = 'watermark', `key` = 'watermark_settings', `value` = '" . $this->db->escape(serialize(array())) . "', serialized = '1'");
			
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `key` = 'watermark_status'");
		
		if(! $query->num_rows)
			$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `group` = 'watermark', `key` = 'watermark_status', `value` = '0', serialized = '0'");
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `key` = 'watermark_apply'");
		
		if(! $query->num_rows)
			$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `group` = 'watermark', `key` = 'watermark_apply', `value` = '" . $this->db->escape(serialize(array())) . "', serialized = '1'");
		
	}
	
	public function getApplySetting()
	{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `key` = 'watermark_apply'");
		
		if($query->num_rows == 1)		
			return unserialize($query->row['value']);
		
		
		return true;
	}
	
	public function getWatermarks()
	{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `key` = 'watermark_settings'");
		if($query->num_rows == 1)
		{
			return unserialize($query->row['value']);			
		}
		return false;
		
	}
	
	public function getManufacturerByImage($url)
	{
		$query = $this->db->query("SELECT manufacturer_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_image pi ON p.product_id = p.product_id WHERE pi.image = '".$this->db->escape($url)."'");
		
		if($query->num_rows == 1)
			return $query->row['manufacturer_id'];
		
		return false;
	}
	
	public function isProduct($url)
	{
		$query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE image = '". $this->db->escape($url) . "'");
		
		if($query->num_rows)
			return true;
			
		$query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product_image WHERE image = '". $this->db->escape($url) . "'");
		
		if($query->num_rows)
			return true;
		
		return false;
	}
	
	public function getWatermarkById($id)
	{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `key` = 'watermark_settings'");
		if($query->num_rows == 1)
		{
			$data = unserialize($query->row['value']);			
			
			foreach ($data as $value)
			{	
				if($value['wid'] == $id)
					return $value;
			}
		}
		return false;
	}
}

?>