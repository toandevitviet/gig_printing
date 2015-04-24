<?php
class ModelToolImage extends Model {
	/**
	*	
	*	@param filename string
	*	@param width 
	*	@param height
	*	@param type char [default, w, h]
	*				default = scale with white space, 
	*				w = fill according to width, 
	*				h = fill according to height
	*	
	*/
	public function resize($filename, $width, $height, $type = "") {
		if (!file_exists(DIR_IMAGE . $filename) || !is_file(DIR_IMAGE . $filename)) {
			return;
		} 
		
		$info = pathinfo($filename);
		
		$extension = $info['extension'];
		
		$old_image = $filename;
		$new_image = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . $width . 'x' . $height . $type .'_' . $this->config->get('config_store_id') . '.' . $extension;
		
		if (!file_exists(DIR_IMAGE . $new_image) || (filemtime(DIR_IMAGE . $old_image) > filemtime(DIR_IMAGE . $new_image))) {
			$path = '';
			
			$directories = explode('/', dirname(str_replace('../', '', $new_image)));
			
			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;
				
				if (!file_exists(DIR_IMAGE . $path)) {
					@mkdir(DIR_IMAGE . $path, 0777);
				}		
			}

			list($width_orig, $height_orig) = getimagesize(DIR_IMAGE . $old_image);

			if (true) {
				$image = new Image(DIR_IMAGE . $old_image);

	if ($width > 90 || $height > 90) {   
	   $image->watermark(DIR_IMAGE . 'watermark.png', 'center');
	}
	
				$image->resize($width, $height, $type);
$this->load->model('module/watermark');
	
					if($this->config->get('watermark_status'))
					{
						$applySetting = $this->model_module_watermark->getApplySetting(); //get Watermark Apply Settings
						
						if($applySetting && isset($applySetting['store_watermark']) && ($applySetting['store_watermark']['status'])){
						
							$store_watermark = $applySetting['store_watermark'];
							
							$current_store_id = $this->config->get('config_store_id');
							
							if(isset($store_watermark['setting'][$current_store_id])){
								
								$store_watermark_setting = $store_watermark['setting'][$current_store_id];
								
								if(isset($store_watermark_setting['watermark']) && ($store_watermark_setting['watermark'] != '0') && $this->model_module_watermark->isProduct($filename)){
									
									$watermark = array('wid' => $store_watermark_setting['watermark'], 'position' => $store_watermark_setting['position'] , 'maxsize' => $store_watermark_setting['maxsize']);
							
									$watermark_details = $this->model_module_watermark->getWatermarkById($watermark['wid']);
								
									$image->custom_watermark($watermark_details,$watermark['position'],$watermark['maxsize']);	
								}
								
							}
							
						}
					}
			
			
				$image->save(DIR_IMAGE . $new_image);
			} else {
				copy(DIR_IMAGE . $old_image, DIR_IMAGE . $new_image);
			}
		}
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			return $this->config->get('config_ssl') . 'image/' . $new_image;
		} else {
			return $this->config->get('config_url') . 'image/' . $new_image;
		}	
	}
}
?>