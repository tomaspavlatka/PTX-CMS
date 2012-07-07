<?php
class PTX_Image {
	
	//images width
	public $imageW;
	//images height
	public $imageH;
	//images type
	public $imageT;
	//image path
	public $imageP;
	//thumbnails width
	public $thumbW;
	//thumbnails height
	public $thumbH;
	//thumbnails X centre
	public $thumbCentreX;
	//thumbnails Y centre
	public $thumbCentreY;
	//thumbnails quality
	public $thumbQ;
	//thumbnails path
	public $thumbP;
	//image ratio
	public $ratio;
	
	public function __construct(){
		
	}
	
	/**
	 * sestavi nazev obrazku
	 * @param $fileName - file name
	 * @param $width - sirka 
	 * @param $height - vyska
	 * @param $suffix - suffix
	 * @return file name
	 */
	public function buildImageName($fileName, $width, $height, $suffix) {
		return $fileName.'-'.$width.'x'.$height.'.'.$suffix;
	}
	
	/**
	 * vytvori novy obrazek
	 * @param $source_path - cesta k originalu
	 * @param $resample_path - cesta, kam se ma ulozit vygenerovany obr
	 * @param $width - sirka
	 * @param $height - vyska
	 * @param $watermarks - watermark
	 * @param $keepAspect - keep aspect 
	 * @param $scaleMin - min scale
	 * @return 
	 */
	function resample($sourcePath, $resamplePath, $width, $height, $watermarks=array(), $keepAspect=true, $scaleMin=false) {
		if ($this->isEnoughMemory($sourcePath))
		{
			$imageData = getimagesize($sourcePath);
			if ($imageData[0] >= $width || $imageData[1] >= $height) {
				// compute width and height
				$ratio = $this->getRatio($sourcePath);
				if ($ratio > 1) {
					if (intval($width/$ratio) > $height) {
						$width = $width = intval($height*$ratio);
					} else {
						$height = intval($width/$ratio);
					}
				} else if ($ratio < 1) {
					$width = intval($height*$ratio);
				} else {
					$width = min(array($width, $height));
					$height = min(array($width, $height));
				}
			} else {
				$width = $imageData[0];
				$height = $imageData[1];
			}
			
			$CimgEdit = new PTX_ImageEditor();
            $CimgEdit->loadImage($sourcePath);
            $CimgEdit->setQuality(90);
            
            $CimgEdit->scale($width, $height, $keepAspect, $scaleMin);
            
			if (sizeof($watermarks) > 0) {
            	foreach ($watermarks AS $watermark) {
            		$CimgEdit->watermark($watermark['base_dir'].'/'.$watermark['filename'].'.'.$watermark['extension'], 100, false, true, 5, $watermark['position']);
            	}
            }   
            
			$CimgEdit->writeToFile($resamplePath);
			$CimgEdit->clear();
		}
	}
	
	public function scale($sourcePath, $resamplePath, $width, $height, $watermarks=array(), $keepAspect=true, $scaleMin=false) {
		
		if ($this->isEnoughMemory($source_path)) {
			
			$imageData = getimagesize($sourcePath);
			if ($imageData[0] >= $width && $imageData[1] >= $height) {
				// compute width and height
				$ratio = $this->getRatio($sourcePath);
				if ($ratio > 1) {
					$height = intval($width/$ratio);
				} else if ($ratio < 1) {
					$height = $width;
					$width = intval($height*$ratio);
				} else {
					$width = min(array($width, $height));
					$height = min(array($width, $height));
				}
			} else {
				$width = $imageData[0];
				$height = $imageData[1];
			}
			
			$CimgEdit = new PTX_ImageEditor();
            $CimgEdit->loadImage($sourcePath);
            $CimgEdit->setQuality(90);
            
            $CimgEdit->scale($width, $height, $keepAspect, $scaleMin);
			
            if (sizeof($watermarks) > 0) {
            	foreach ($watermarks AS $watermark) {
            		$CimgEdit->watermark($watermark['base_dir'].'/'.$watermark['filename'].'.'.$watermark['extension'], 100, false, true, 5, $watermark['position']);
            	}
            }   
            
			$CimgEdit->writeToFile($resamplePath);
			$CimgEdit->clear();
		}
	}
	
	public function crop($source_path, $resample_path, $width, $height, $watermarks=array(), $startX=false, $startY=false) {	
		
		if ($this->isEnoughMemory($source_path)) {
			
			$CimgEdit = new PTX_ImageEditor();
            $CimgEdit->loadImage($source_path);
            $CimgEdit->setQuality(90);
            $CimgEdit->scale(max(array($width, $height)), max(array($width, $height)),true, true);
			
			$CimgEdit->writeToFile($resample_path);
			$CimgEdit->clear();
			
			$CimgEdit->loadImage($resample_path);
            $CimgEdit->setQuality(100);
            
			$CimgEdit->crop($width, $height,false,false);
			if (sizeof($watermarks) > 0) {
            	foreach ($watermarks AS $watermark) {
            		$CimgEdit->watermark($watermark['base_dir'].'/'.$watermark['filename'].'.'.$watermark['extension'], 100, false, true, 5, $watermark['position']);
            	}
            }
            
			$CimgEdit->writeToFile($resample_path);
			$CimgEdit->clear();
		}
	}
	
	public function getRatio($image_path) {
		$image_data = getimagesize($image_path);
		return $image_data[0]/$image_data[1];
	}
	
	function isEnoughMemory($filepath) {
		$image_info = getimagesize($filepath);
		$memory_needed = round(($image_info[0] * $image_info[1] * $image_info['bits'] * $image_info['channels'] / 8 + pow(2, 16)) * 1.65);
		$memory_limit_string = ini_get('memory_limit');
		$memory_limit = str_replace('M', '', $memory_limit_string);
		$limit = $memory_limit * pow(2,20);
		$rozmery_soucin_limit = floor(((($limit / 1.65) - pow(2,16)) * 8) / $image_info['bits'] / $image_info['channels']);
		$rozmery_limit = floor(sqrt($rozmery_soucin_limit));
		
		//echo 'potrebna pamet:'.$memory_needed."\ndostupna pamet:".$limit.'<br />';
		
		// memory limit is enough
		if ($memory_needed < $limit) {
			return true;
		} else {
			return false;				
		}
	}
	
	//make thumbnail
    function makeThumb($imageP,$thumbP,$thumbW,$thumbH,$bgColor, $watermarks=array()) {
        $this->thumbQ = 90;
		$this->imageP = $imageP;
        $this->thumbP = $thumbP;
        // zjisteni rozmeru zdrojoveho obrazku
        list($this->imageW, $this->imageH, $this->imageT) = getimagesize($imageP);
        // nastaveni sirky vysledneho obrazku
        $thumbH = $thumbH;
        
        // zdrojovy obrazek je na vysku
        if ($this->imageH > $this->imageW) {
          $this->ratio = $this->imageW / $this->imageH;
          $this->thumbH = $thumbH;
          $this->thumbW = $this->ratio  * $this->thumbH;
        } elseif($this->imageH < $this->imageW) {
          $this->ratio = $this->imageH / $this->imageW;
          $this->thumbW = ($this->imageW < $thumbW) ? $this->imageW : $thumbW;
          $this->thumbH = $this->ratio * $this->thumbW;
        } else {
          $this->ratio = 1;
          $this->thumbW = ($this->imageW < $thumbW) ? $this->imageW : $thumbW;
          $this->thumbH = $this->ratio * $this->thumbW;  
        }

        //zjistime stred nahledu
        $this->thumbCentreX = $thumbW / 2;
        $this->thumbCentreY = $thumbH / 2;
        
        //type-based operations
        switch ($this->imageT) {
          case 1 : $thumb = ImageCreateFromGIF($this->imageP);
            break;
          case 2 : $thumb = imagecreatefromjpeg($this->imageP);
            break;
          case 3 : $thumb = imagecreatefrompng($this->imageP);
            break;
        }
        
        //vytvorime prazdny nahledovy obrazek
        $image_p = ImageCreateTruecolor($thumbW, $thumbH);
        //nastavime bilou barvu pozadi
        $background = imagecolorallocate($image_p,(int)$bgColor['r'],(int)$bgColor['g'],(int)$bgColor['b']);
        imagefill($image_p, 0, 0, $background);
        
        ImageCopyResampled($image_p, $thumb, ($this->thumbCentreX - ($this->thumbW/2)), ($this->thumbCentreY - ($this->thumbH/2)), 0, 0, $this->thumbW, $this->thumbH, $this->imageW, $this->imageH);
        touch($this->thumbP);
        
        //type-based operations
        switch ($this->imageT) {
          case 1 : ImageGIF($image_p, $this->thumbP, $this->thumbQ);
            break;
          case 2 : imagejpeg($image_p, $this->thumbP, $this->thumbQ);
            break;
          case 3 : imagepng($image_p, $this->thumbP, 2);
            break;
        }
        
        imagedestroy($image_p);
        imagedestroy($thumb);
    }
}