<?php
class Image {
	private $file;
	private $image;
	private $info;

	public function __construct($file) {
		if (file_exists($file)) {
			$this->file = $file;

			$info = getimagesize($file);

			$this->info = array(
				'width'  => $info[0],
				'height' => $info[1],
				'bits'   => $info['bits'],
				'mime'   => $info['mime']
			);

			$this->image = $this->create($file);
		} else {
			exit('Error: Could not load image ' . $file . '!');
		}
	}

	private function create($image) {
		$mime = $this->info['mime'];

		if ($mime == 'image/gif') {
			return imagecreatefromgif($image);
		} elseif ($mime == 'image/png') {
			return imagecreatefrompng($image);
		} elseif ($mime == 'image/jpeg') {
			return imagecreatefromjpeg($image);
		}
	}

	public function save($file, $quality = 90) {
		$info = pathinfo($file);

		$extension = strtolower($info['extension']);

		if (is_resource($this->image)) {
			if ($extension == 'jpeg' || $extension == 'jpg') {
				imagejpeg($this->image, $file, $quality);
			} elseif($extension == 'png') {
				imagepng($this->image, $file);
			} elseif($extension == 'gif') {
				imagegif($this->image, $file);
			}

			imagedestroy($this->image);
		}
	}

	public function resize($width = 0, $height = 0, $default = '') {
		if (!$this->info['width'] || !$this->info['height']) {
			return;
		}

		$xpos = 0;
		$ypos = 0;
		$scale = 1;

		$scale_w = $width / $this->info['width'];
		$scale_h = $height / $this->info['height'];

		if ($default == 'w') {
			$scale = $scale_w;
		} elseif ($default == 'h'){
			$scale = $scale_h;
		} else {
			$scale = min($scale_w, $scale_h);
		}

		if ($scale == 1 && $scale_h == $scale_w && $this->info['mime'] != 'image/png') {
			return;
		}

		$new_width = (int)($this->info['width'] * $scale);
		$new_height = (int)($this->info['height'] * $scale);			
		$xpos = (int)(($width - $new_width) / 2);
		$ypos = (int)(($height - $new_height) / 2);

		$image_old = $this->image;
		$this->image = imagecreatetruecolor($width, $height);

		if (isset($this->info['mime']) && $this->info['mime'] == 'image/png') {		
			imagealphablending($this->image, false);
			imagesavealpha($this->image, true);
			$background = imagecolorallocatealpha($this->image, 255, 255, 255, 127);
			imagecolortransparent($this->image, $background);
		} else {
			$background = imagecolorallocate($this->image, 255, 255, 255);
		}

		imagefilledrectangle($this->image, 0, 0, $width, $height, $background);

		imagecopyresampled($this->image, $image_old, $xpos, $ypos, 0, 0, $new_width, $new_height, $this->info['width'], $this->info['height']);
		imagedestroy($image_old);

		$this->info['width']  = $width;
		$this->info['height'] = $height;
	}


			
			public function getImage()
			{
				return $this->image;
			}
			
			public function custom_watermark($watermark_details,$position, $maxsize) {
				
				$rootpath = substr(DIR_APPLICATION, 0, strrpos(DIR_APPLICATION, '/', -2)) . '/';
				
				$opacity		=  $watermark_details['watermark_text']['opacity'];
				
				if ($watermark_details['watermark_type'] == 0){ // watermark type = text
					
					$font_size 	= $watermark_details['watermark_text']['font_size'];
					$angle 		= 0;
					$font 		= $rootpath . 'fonts/'.$watermark_details['watermark_text']['font'];
					$text 		= $watermark_details['watermark_text']['text'];
					$color 		= $watermark_details['watermark_text']['color'];
					
					
					$size 		= imagettfbbox($font_size, $angle, $font, $text);
				
					$watermark_width 	= $size[4] - $size[6] + 5;
					$watermark_height 	= $size[1] - $size[7] + 5;
					
					$watermark 	= imagecreatetruecolor($watermark_width, $watermark_height);
					
					$transparent	= imagecolorallocatealpha($watermark , 0x00, 0x00, 0x00, 127);
					
					imagecolortransparent($watermark , $transparent);
					
					imagefill($watermark , 0 , 0 , $transparent);
					
					$image_color = imagecolorallocatealpha($watermark , base_convert(substr($color,0,2) , 16 , 10) , base_convert( substr ( $color , 2 , 2 ) ,16 ,10) , base_convert(substr($color, 4, 2), 16, 10),round(127*(100-$opacity)/100));
				
					imagettftext($watermark, $font_size ,$angle ,0 ,$font_size ,$image_color, $font, $text);
					
				} else if ($watermark_details['watermark_type'] == 1) { // watermark_type = image
				
					$watermark = imagecreatefrompng($rootpath . 'image/'. $watermark_details['watermark_image']);
					
					if (isset($watermark)) {
						$watermark_width 	= imagesx($watermark);
						$watermark_height 	= imagesy($watermark);					
					}
				}
				
				// Watermark Resizing
				/*$imageWidth = $this->info['width'];
				
				if ($imageWidth < $watermark_width) {
					$ratio = $targetWidth/$watermark_width;
					
					$watermark_ratio = $watermark_width/$watermark_height;
					
					$watermark_new_width = ($ratio <= 1) ? $imageWidth*$ratio : $imageWidth/$ratio;
					
					$watermark_new_height = $watermark_new_width/$watermark_ratio;
					
					
					$temp_image= imagecreatetruecolor($watermark_new_width, $watermark_new_height);
					$coloralpha = imagecolorallocatealpha($temp_image,0x00,0x00,0x00,127); 
					imagefill($temp_image, 0, 0, $coloralpha);
					imagecopyresampled($temp_image, $watermark, 0, 0, 0, 0, $watermark_new_width, $watermark_new_height, $watermark_width, $watermark_height);
					$watermark_width = $watermark_new_width;
					$watermark_height = $watermark_new_height;
					$watermark = $temp_image;
				}*/
				
				$imageWidth = $this->info['width'];
				
				if ($imageWidth*( $maxsize / 100 ) < $watermark_width) {
					
					$watermark_ratio = $watermark_width/$watermark_height;
					
					$watermark_new_width = $imageWidth*( $maxsize / 100 );
					
					$watermark_new_height = $watermark_new_width/$watermark_ratio;
					
					
					$temp_image= imagecreatetruecolor($watermark_new_width, $watermark_new_height);
					$coloralpha = imagecolorallocatealpha($temp_image,0x00,0x00,0x00,127); 
					imagefill($temp_image, 0, 0, $coloralpha);
					imagecopyresampled($temp_image, $watermark, 0, 0, 0, 0, $watermark_new_width, $watermark_new_height, $watermark_width, $watermark_height);
					$watermark_width = $watermark_new_width;
					$watermark_height = $watermark_new_height;
					$watermark = $temp_image;
				}
				
				if (isset($watermark)) {
					switch($position) {	//applying position
						case 1:
							$watermark_start_x = 0;
							$watermark_start_y = 0;
							break;
						case 2:
							$watermark_start_x = $this->info['width'] - $watermark_width;
							$watermark_start_y = 0;
							break;
						case 3:
							$watermark_start_x = floor($this->info['width']/2 - $watermark_width/2);
							$watermark_start_y = floor($this->info['height']/2 - $watermark_height/2);;
							break;
						case 4:
							$watermark_start_x = 0;
							$watermark_start_y = $this->info['height'] - $watermark_height;
							break;
						case 5:
							$watermark_start_x = $this->info['width'] - $watermark_width;
							$watermark_start_y = $this->info['height'] - $watermark_height;
							break;	
					}
					
					if($watermark_details['watermark_type'] == 0) {
						imagecopymerge($this->image, $watermark, $watermark_start_x, $watermark_start_y, 0, 0, $watermark_width, $watermark_height, $opacity);
					} else if($watermark_details['watermark_type'] == 1) {
						imagecopy($this->image, $watermark, $watermark_start_x, $watermark_start_y, 0, 0, $watermark_width, $watermark_height);
					}
					
					imagedestroy($watermark);
				}
			}
			
	public function watermark($file, $position = 'bottomright') {
		$watermark = imagecreatefrompng($file);

		$watermark_width = imagesx($watermark);
		$watermark_height = imagesy($watermark);

		switch($position) {
			case 'topleft':
				$watermark_pos_x = 0;
				$watermark_pos_y = 0;
				break;
			case 'topright':
				$watermark_pos_x = $this->info['width'] - $watermark_width;
				$watermark_pos_y = 0;
				break;
			case 'bottomleft':
				$watermark_pos_x = 0;
				$watermark_pos_y = $this->info['height'] - $watermark_height;
				break;

			case 'center':
						$watermark_pos_x = ($this->info['width'])/3;
						$watermark_pos_y = ($this->info['height'])/3;
						break;
			
			case 'bottomright':
				$watermark_pos_x = $this->info['width'] - $watermark_width;
				$watermark_pos_y = $this->info['height'] - $watermark_height;
				break;
		}

		imagecopy($this->image, $watermark, $watermark_pos_x, $watermark_pos_y, 0, 0, $watermark_width, $watermark_height);

		imagedestroy($watermark);
	}

	public function crop($top_x, $top_y, $bottom_x, $bottom_y) {
		$image_old = $this->image;
		$this->image = imagecreatetruecolor($bottom_x - $top_x, $bottom_y - $top_y);

		imagecopy($this->image, $image_old, 0, 0, $top_x, $top_y, $this->info['width'], $this->info['height']);
		imagedestroy($image_old);

		$this->info['width'] = $bottom_x - $top_x;
		$this->info['height'] = $bottom_y - $top_y;
	}

	public function rotate($degree, $color = 'FFFFFF') {
		$rgb = $this->html2rgb($color);

		$this->image = imagerotate($this->image, $degree, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));

		$this->info['width'] = imagesx($this->image);
		$this->info['height'] = imagesy($this->image);
	}

	private function filter($filter) {
		imagefilter($this->image, $filter);
	}

	private function text($text, $x = 0, $y = 0, $size = 5, $color = '000000') {
		$rgb = $this->html2rgb($color);

		imagestring($this->image, $size, $x, $y, $text, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));
	}

	private function merge($file, $x = 0, $y = 0, $opacity = 100) {
		$merge = $this->create($file);

		$merge_width = imagesx($image);
		$merge_height = imagesy($image);

		imagecopymerge($this->image, $merge, $x, $y, 0, 0, $merge_width, $merge_height, $opacity);
	}

	private function html2rgb($color) {
		if ($color[0] == '#') {
			$color = substr($color, 1);
		}

		if (strlen($color) == 6) {
			list($r, $g, $b) = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);   
		} elseif (strlen($color) == 3) {
			list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);    
		} else {
			return false;
		}

		$r = hexdec($r); 
		$g = hexdec($g); 
		$b = hexdec($b);    

		return array($r, $g, $b);
	}	
}
?>