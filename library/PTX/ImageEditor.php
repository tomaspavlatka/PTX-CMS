<?php
/*
	The functions:
	
	loadImage($imageURL):
		loads an image to edit, can be GIF, JPG or PNG
		
	setQuality($imgQuality):
		sets the JPG quality
		
	writeJPGToScreen():
		outputs the edited image as an JPG to the browser
		
	writeGIFToScreen():
		outputs the edited image as an GIF to the browser
		
	writePNGToScreen():
		outputs the edited image as an PNG to the browser
	
	writeJPGToFile($destFile):
		outputs the edited image as an JPG to the given file
		
	writeGIFToFile($destFile):
		outputs the edited image as an GIF to the given file
		
	writePNGToFile($destFile):
		outputs the edited image as an PNG to the given file
	
	
	--Edit functions--
	
	blur($dist=1), blurs the image $dist gives the blurfactor, this is a heavy function so be carefull how you use it.
	
	convertDuoTone($hexColor,$min="00", $max="FF") the image is converted to the specified hexColor the min and max options are to mark the converted area by grayscale
	
	crop($width, $height,$startX=false, $startY=false):
		$width = the width of the new image
		$height = the height of the new image
		$startX = the starting X position in the original image, if you don't specify a startX then the cropped area will center in the image
		$startY = the starting Y position in the original image, if you don't specify a startY then the cropped area will center in the image
		
	deinterlace($even=false):
		$even = If even is false the interlaced lines are on the lines e.g. 1,3,5 etc...
		
	interlace($hexColor="000000",$startEven=false):
		$hexColor = the color of the interlaced lines
		$startEven = if true the first interlaced line is 0 else the first = 1
		
	interlaceMerge($sourceURL,$startEven=false):
		$sourceURL = the URL of the images that has to be merged into the edit image
		$startEven = if true the first interlaced line is 0 else the first = 1
		This function merges to images the interlaced way
		The output images is as large as the edit image, the sourceimage is positioned at the 0,0 point of the editimage
		
	noise():
		Adds noise to the image
		
	pixelate($blocksize=4):
		Pixalates the image, default blocksize is 4
		
	rotate($angle):
		rotates the image clockwise with the given angle.
		
	scale($newWidth, $newHeight, $keepAspect=true, $scaleMin=false):
		The image is scaled to the new width and height,
		if keepaspect is set to false the image will stretch otherwise 
		the image will become as large as the highest factor. 
		If scaleMin is set to true (only when keepaspect=true) 
		the image will become as large as the lowest factor.
	
	scalePercentage($percentage):
		The image is resized to the given percentage
	
	scaleX($newWidth):
		The image is scaled to the new width but aspect is kept
		
	scaleY($newHeight):
		The image is scaled to the new height but aspect is kept
	
	scatter($pixelDist=4):
		Scatters the image with the given pixel distance, default distance is 4 pixels
		
	screen($hexColor="000000"):
		Like interlace only both horizontal and vertical lines are interlaced
		
	watermark($sourceURL, $transparency=50, $fillImage=false, $keepAspect=true):
		$sourceURL = The URL of the image that serves as the watermark, can be GIF, JPG or PNG
		$transparency = The transparency in which the watermark is displayed over the original image, default is 50
		$fillImage = If set to true the watermark will be resized to the size of the original image
		$keepAspect = If $fillImage is set to true this will tell the resizer to keep the aspect of the watermark or not.
			
	merge($sourceURL, $mX=0, $mY=0):
		$sourceURL defines the URL of the image to merge on top of the loaded image.
		$mX is the startX coordinate, $mY is the startY coordinate
		
*/
class PTX_ImageEditor{
	
	var $inputImage;
	var $imageQuality;
	var $editImage;
	
	function ImageEditor(){
		$this->imageQuality = 80;
	}
	
	function clear(){
		imagedestroy($this->inputImage);
		imagedestroy($this->editImage);
	}
	
	function getEditImage(){
		if(!isset($this->editImage)){
			return $this->inputImage;
		} else {
			return $this->editImage;
		}
	}
	
	function loadImage($imageURL){
		$info = getimagesize($imageURL);
		unset($this->editImage);
		if($info[2]==1) $this->loadGIF($imageURL);
		if($info[2]==2) $this->loadJPG($imageURL);
		if($info[2]==3) $this->loadPNG($imageURL);
	}
	
	function loadAlternateFile($imageURL){
		$info = getimagesize($imageURL);
		if($info[2]==1) return imagecreatefromgif($imageURL);
		if($info[2]==2) return imagecreatefromjpeg($imageURL);
		if($info[2]==3) return imagecreatefrompng($imageURL);
	}
	
	function loadJPG($imageURL){
		if(isset($this->inputImage)) imagedestroy($this->inputImage);
		$this->inputImage = imagecreatefromjpeg($imageURL);
	}
	
	function loadGIF($imageURL){
		if(isset($this->inputImage)) imagedestroy($this->inputImage);
		$this->inputImage = imagecreatefromgif($imageURL);
	}
	
	function loadPNG($imageURL){
		if(isset($this->inputImage)) imagedestroy($this->inputImage);
		$this->inputImage = imagecreatefrompng($imageURL);
		imagealphablending($this->inputImage,true);
	}
	
	function setQuality($imgQuality){
		$this->imageQuality = $imgQuality;
	}
	
	function writeToFile($destFile){ // by Jafrus
		if(!isset($this->editImage)){
			imagejpeg($this->inputImage, $destFile, $this->imageQuality);
		}
		else{
			imagejpeg($this->editImage, $destFile, $this->imageQuality);
		}
	}
	
	function writeJPGToFile($destFile){
		if(!isset($this->editImage)){
			imagejpeg($this->inputImage, $destFile, $this->imageQuality);
		}
		else{
			imagejpeg($this->editImage, $destFile, $this->imageQuality);
		}
	}
	
	function writeGIFToFile($destFile){
		if(!isset($this->editImage)){
			imagejpeg($this->inputImage,$destFile);
		}
		else{
			imagejpeg($this->editImage,$destFile);
		}
	}
	
	function writePNGToFile($destFile){
		if(!isset($this->editImage)){
			imagealphablending($this->inputImage,true);
			imagepng($this->inputImage,$destFile);
		}
		else{
			imagealphablending($this->editImage,true);
			imagepng($this->editImage,$destFile);
		}
	}
	
	function writeJPGToScreen(){
		header("Content-Type: image/jpeg");
		if(!isset($this->editImage)){
			imagejpeg($this->inputImage, '', $this->imageQuality);
		}
		else{
			imagejpeg($this->editImage, '', $this->imageQuality);
		}
	}
	
	function writeGIFToScreen(){
		header("Content-Type: image/gif");
		if(!isset($this->editImage)){
			imagejpeg($this->inputImage);
		}
		else{
			imagejpeg($this->editImage);
		}
	}
	
	function writePNGToScreen(){
		header("Content-Type: image/png");
		if(!isset($this->editImage)){
			imagepng($this->inputImage);
		}
		else{
			imagepng($this->editImage);
		}
	}
	
	function createNewEditImage($width, $height){
		if(isset($this->editImage)) imagedestroy($this->editImage);
		$this->editImage = ImageCreateTrueColor($width, $height);
		imagealphablending($this->editImage,false);
		imagesavealpha($this->editImage,true);
	}
	
	function getWidth()
	{
		if(!isset($this->editImage)){
				return imagesx($this->inputImage);
		}
		else{
			return imagesx($this->editImage);
		}
	}
	
	function getHeight()
	{
		if(!isset($this->editImage)){
				return imagesy($this->inputImage);
		}
		else{
			return imagesy($this->editImage);
		}
	}
	
	function getCopy(){
		if(!isset($this->editImage)){
			if(!isset($this->inputImage)){
				$tmpImage = ImageCreateTrueColor(1, 1);
			}
			else{
				$tmpImage = ImageCreateTrueColor(imagesx($this->inputImage), imagesy($this->inputImage));
				imagealphablending($tmpImage,false);
				imagesavealpha($tmpImage,true);
				imagecopyresampled($tmpImage,$this->inputImage,0,0,0,0,imagesx($this->inputImage), imagesy($this->inputImage),imagesx($this->inputImage), imagesy($this->inputImage));
			}
		}
		else{
			$tmpImage = ImageCreateTrueColor(imagesx($this->editImage), imagesy($this->editImage));
			imagealphablending($tmpImage,false);
			imagesavealpha($tmpImage,true);
			imagecopyresampled($tmpImage,$this->editImage,0,0,0,0,imagesx($this->editImage), imagesy($this->editImage),imagesx($this->editImage), imagesy($this->editImage));
		}
		return $tmpImage;
	}
	
	function crop($width, $height, $startX=false, $startY=false){
		$tmpImage = $this->getCopy();
		
		$img_width  = imagesx($tmpImage);
		$img_height = imagesy($tmpImage);
		
		$diffX = $img_width - $width;
		$diffY = $img_height - $height;
		
		if($diffX<0) $diffX=0;
		if($diffY<0) $diffY=0;
				
		if(!$startX) $startX = $diffX/2;
		if(!$startY) $startY = $diffY/2;
		
		$this->createNewEditImage($width,$height);
		
		imagecopyresampled($this->editImage,$tmpImage, 0, 0, $startX, $startY,$width, $height, $img_width - $diffX, $img_height - $diffY);
		imagedestroy($tmpImage);
	}
	
	function scalePercentage($percentage){
		$tmpImage = $this->getCopy();
		
		$img_width  = imagesx($tmpImage);
		$img_height = imagesy($tmpImage);
		
		$newHeight = round($img_height * ($percentage/100));
		$newWidth = round($img_width * ($percentage/100));
		
		$this->createNewEditImage($newWidth,$newHeight);
		
		imagecopyresampled($this->editImage,$tmpImage, 0, 0, 0, 0,$newWidth, $newHeight, $img_width, $img_height);
		imagedestroy($tmpImage);
	}
	
	function scaleX($newWidth){
		$tmpImage = $this->getCopy();
		
		$img_width  = imagesx($tmpImage);
		$img_height = imagesy($tmpImage);
		
		$factor = $newWidth / $img_width;
		
		$newHeight = round($img_height * $factor);
		
		$this->createNewEditImage($newWidth,$newHeight);
		
		imagealphablending($tmpImage,false);
		imagesavealpha($tmpImage,true);
		
		ImageCopyResampled($this->editImage, $tmpImage, 0, 0, 0, 0,$newWidth, $newHeight, $img_width, $img_height);

		
	//	imagecopy($this->editImage,$imgDest, 0, 0, 0, 0,$newWidth, $newHeight);
				
		imagedestroy($tmpImage);
	}
	
	function scaleY($newHeight){
		$tmpImage = $this->getCopy();
		
		$img_width  = imagesx($tmpImage);
		$img_height = imagesy($tmpImage);
		
		$factor = $newHeight / $img_height;
		
		$newWidth = round($img_width * $factor);
		
		$this->createNewEditImage($newWidth,$newHeight);
		
		imagecopyresampled($this->editImage,$tmpImage, 0, 0, 0, 0,$newWidth, $newHeight, $img_width, $img_height);
		imagedestroy($tmpImage);
	}
	
	function rotate($angle) {
		$tmpImage = $this->getCopy();
		$angle = $angle + 180;
		$angle = deg2rad($angle);
		
		$src_x = imagesx($tmpImage);
		$src_y = imagesy($tmpImage);
		
		$center_x = floor($src_x/2);
		$center_y = floor($src_y/2);
		
		$cosangle = cos($angle);
		$sinangle = sin($angle);
		
		$corners=array(array(0,0), array($src_x,0), array($src_x,$src_y), array(0,$src_y));
		
		foreach($corners as $key=>$value) {
			$value[0]-=$center_x;        //Translate coords to center for rotation
			$value[1]-=$center_y;
			$temp=array();
			$temp[0]=$value[0]*$cosangle+$value[1]*$sinangle;
			$temp[1]=$value[1]*$cosangle-$value[0]*$sinangle;
			$corners[$key]=$temp;    
		}
		
		$min_x=1000000000000000;
		$max_x=-1000000000000000;
		$min_y=1000000000000000;
		$max_y=-1000000000000000;
		
		foreach($corners as $key => $value) {
			if($value[0]<$min_x)
				$min_x=$value[0];
			if($value[0]>$max_x)
				$max_x=$value[0];
			
			if($value[1]<$min_y)
				$min_y=$value[1];
			if($value[1]>$max_y)
				$max_y=$value[1];
		}
			
		$rotate_width=round($max_x-$min_x);
		$rotate_height=round($max_y-$min_y);
		
		$rotate=imagecreatetruecolor($rotate_width,$rotate_height);
		imagealphablending($rotate, false);
		imagesavealpha($rotate, true);
		
		//Reset center to center of our image
		$newcenter_x = ($rotate_width)/2;
		$newcenter_y = ($rotate_height)/2;
		
		for ($y = 0; $y < ($rotate_height); $y++) {
			for ($x = 0; $x < ($rotate_width); $x++) {
				// rotate...
				$old_x = round((($newcenter_x-$x) * $cosangle + ($newcenter_y-$y) * $sinangle)) + $center_x;
				$old_y = round((($newcenter_y-$y) * $cosangle - ($newcenter_x-$x) * $sinangle)) + $center_y;
				
				if ( $old_x >= 0 && $old_x < $src_x && $old_y >= 0 && $old_y < $src_y ) {
					$color = imagecolorat($tmpImage, $old_x, $old_y);
				} else {
					// this line sets the background colour
					$color = imagecolorallocatealpha($tmpImage, 255, 255, 255, 127);
				}
				imagesetpixel($rotate, $x, $y, $color);
			}
		}
	
		$this->createNewEditImage(imagesx($rotate),imagesy($rotate));
		imagecopy($this->editImage,$rotate,0,0,0,0,imagesx($rotate), imagesy($rotate));
		imagedestroy($tmpImage);
		imagedestroy($rotate);
	}
	
	function watermark($sourceURL, $transparency=50, $fillImage=false, $keepAspect=true, $margin=0, $position='random'){
		
		$tmpImage = $this->getCopy();
		
		$img_width  = imagesx($tmpImage);
		$img_height = imagesy($tmpImage);
		
		$this->createNewEditImage($img_width,$img_height);
		
		$wmImage  = $this->loadAlternateFile($sourceURL);
		
 		if($fillImage){
 			$this->createNewEditImage(imagesx($wmImage),imagesy($wmImage));
 			imagecopy($this->editImage,$wmImage,0,0,0,0,imagesx($wmImage), imagesy($wmImage));
 			$this->scale($img_width,$img_height,$keepAspect,false);
 			$wmImage = $this->getCopy();
 			$this->createNewEditImage($img_width,$img_height);
 		}
 		
 		//$margin = 5;
		// Water mark random position
 		switch ($position)
 		{
 			case 'lt': //left top 
				$wmX = $margin;
				$wmY = $margin;
				break;
			case 'ct': // center top
				$wmX = ((imageSX($tmpImage) - imageSX($wmImage))/2) - $margin;
				$wmY = $margin;
				break;
			case 'rt': // right top
				$wmX = (imageSX($tmpImage) - imageSX($wmImage)) - $margin;
				$wmY = $margin;
				break;
			case 'lc': //left center
				$wmX = $margin;
				$wmY = ((imageSY($tmpImage) - imageSY($wmImage))/2) - $margin;
				break;
			case 'c': //center
				$wmX = ((imageSX($tmpImage) - imageSX($wmImage))/2) - $margin;
				$wmY = ((imageSY($tmpImage) - imageSY($wmImage))/2) - $margin;
				break;
			case 'rc': // right center
				$wmX = (imageSX($tmpImage) - imageSX($wmImage)) - $margin;
				$wmY = ((imageSY($tmpImage) - imageSY($wmImage))/2) - $margin;
				break;
			case 'lb': // left bottom
				$wmX = $margin;
				$wmY = (imageSY($tmpImage) - imageSY($wmImage)) - $margin;
				break;
			case 'cb': // center bottom
				$wmX = ((imageSX($tmpImage) - imageSX($wmImage))/2) - $margin;
				$wmY = (imageSY($tmpImage) - imageSY($wmImage)) - $margin;
				break;
			case 'rb': // right bottom
				$wmX = (imageSX($tmpImage) - imageSX($wmImage)) - $margin;
				$wmY = (imageSY($tmpImage) - imageSY($wmImage)) - $margin;
				break;
			case 'random': // random
				$wmX = (bool)rand(0,1) ? $margin : (imageSX($tmpImage) - imageSX($wmImage)) - $margin;
				$wmY = (bool)rand(0,1) ? $margin : (imageSY($tmpImage) - imageSY($wmImage)) - $margin;
				break;
 		}
 		//$wmX = (bool)rand(0,1) ? $margin : (imageSX($tmpImage) - imageSX($wmImage)) - $margin;
 		//$wmY = (bool)rand(0,1) ? $margin : (imageSY($tmpImage) - imageSY($wmImage)) - $margin;

		imagealphablending($tmpImage,true);
		// Water mark process
 		imagecopy($tmpImage, $wmImage, $wmX, $wmY, 0, 0, imageSX($wmImage), imageSY($wmImage));
 		imagecopy($this->editImage,$tmpImage,0,0,0,0,imagesx($tmpImage), imagesy($tmpImage));
 		imagedestroy($wmImage);
 		imagedestroy($tmpImage);
 	}
 	
 	function merge($wmImg, $mX=0, $mY=0){
		$tmpImage = $this->getCopy();
		
		$img_width  = imagesx($tmpImage);
		$img_height = imagesy($tmpImage);
		
 		$imgDest	= imageCreateTrueColor($img_width,$img_height);
		
		ImageCopyResampled($imgDest, $tmpImage, 0, 0, 0, 0, $img_width, $img_height, $img_width, $img_height);
		
		//$wmImg = $this->loadAlternateFile($sourceURL);
		
		$wm_width  = imagesx($wmImg);
		$wm_height = imagesy($wmImg);
		
		ImageCopyResampled($imgDest, $wmImg, $mX, $mY, 0, 0, $wm_width, $wm_height, $wm_width, $wm_height);
		
		imagecopy($this->editImage,$imgDest,0,0,0,0,imagesx($imgDest), imagesy($imgDest));
		
		ImageDestroy($imgDest);
		ImageDestroy($tmpImage);
		ImageDestroy($wmImg);

 	}
 	
	function scale($newWidth, $newHeight, $keepAspect=true, $scaleMin=false){
		$tmpImage = $this->getCopy();
		
		$img_width  = imagesx($tmpImage);
		$img_height = imagesy($tmpImage);
		
		if($keepAspect){
			$factorX = $newWidth / $img_width;
			$factorY = $newHeight / $img_height;
			
			if($scaleMin){
				if($factorX>$factorY){
					$newWidth = round($img_width * $factorX);
					$newHeight = round($img_height * $factorX);
				}
				else{
					$newWidth = round($img_width * $factorY);
					$newHeight = round($img_height * $factorY);
				}
			}
			else{
				if($factorX<$factorY){
					$newWidth = round($img_width * $factorX);
					$newHeight = round($img_height * $factorX);
				}
				else{
					$newWidth = round($img_width * $factorY);
					$newHeight = round($img_height * $factorY);
				}
			}
		}		
		$this->createNewEditImage($newWidth,$newHeight);
		
		imagecopyresampled($this->editImage,$tmpImage, 0, 0, 0, 0,$newWidth, $newHeight, $img_width, $img_height);
		imagedestroy($tmpImage);
	}
	
	function _duotone($black,$color,$center,$min=0,$max=255) { 
		if($black <= $center) {
			if($center>0){ 
				return ($black / $center) * $color; 
			}
			else{
				return 0;
			}
		} else { 
			return $max - (($max - $black) / ($max - $center) * ($max - $color)); 
		}
		
	} 
		
	function convertDuoTone($hexColor,$min="00", $max="FF"){
		$tmpImage = $this->getCopy();
		$img_width = imagesx($tmpImage);
		$img_height = imagesy($tmpImage);
		
		$this->createNewEditImage($img_width,$img_height);
		
		$r = hexdec(substr($hexColor,0,2));
		$g = hexdec(substr($hexColor,2,2));
		$b = hexdec(substr($hexColor,4,2));
		if($r>0) $r = round($r/3);
		if($g>0) $g = round($g/3);
		if($b>0) $b = round($b/3);
		
		$min = hexdec($min);
		$max = hexdec($max);
		
		$center = round(.299*$r + .587*$g + .114*$b);
				
		$table = array();
		
		for($i=0;$i<256;$i++) { 
			$table[$i] = imagecolorallocate($this->editImage,$this->_duotone($i,$r,$center,$min,$max),$this->_duotone($i,$g,$center,$min,$max),$this->_duotone($i,$b,$center,$min,$max)); 
		} 
		
		for($x=0;$x < $img_width;$x++) { 
			for($y=0;$y < $img_height;$y++) { 
				$rgb = imagecolorat($tmpImage,$x,$y);
				$r  = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8)  & 0xFF;
				$b  = $rgb & 0xFF;
				$gray = round(.299*$r + .587*$g + .114*$b);
				imagesetpixel($this->editImage,$x,$y,$table[$gray]); 
			} 
		} 
		
		imagedestroy($tmpImage);
	}
		
	function interlace ($hexColor="000000",$startEven=false) {
	    $tmpImage = $this->getCopy();
		$img_width = imagesx($tmpImage);
		$img_height = imagesy($tmpImage);

		$r = hexdec(substr($hexColor,0,2));
		$g = hexdec(substr($hexColor,2,2));
		$b = hexdec(substr($hexColor,4,2));
		
	    $color = imagecolorallocate($tmpImage, $r, $g, $b);
	
		$startLine=1;
		if($startEven) $startLine=0;
	    for ($y = $startLine; $y < $img_height; $y += 2) {
	        imageline($tmpImage, 0, $y, $img_width, $y, $color);
	    }
	    
	    $this->createNewEditImage($img_width,$img_height);
	    imagecopy($this->editImage,$tmpImage,0,0,0,0,$img_width, $img_height);
	    imagedestroy($tmpImage);
	} 
	
	function interlaceMerge($sourceURL,$startEven=false) {
	    $tmpImage = $this->getCopy();
		$img_width = imagesx($tmpImage);
		$img_height = imagesy($tmpImage);
		
		
		$tmpImage2  = $this->loadAlternateFile($sourceURL);
		$mergeImg = ImageCreateTrueColor(imagesx($tmpImage2), imagesy($tmpImage2));
		imagecopy($mergeImg,$tmpImage2,0,0,0,0,imagesx($tmpImage2), imagesy($tmpImage2));
		imagedestroy($tmpImage2);
		
		$startLine=1;
		if($startEven) $startLine=0;
	    for ($y = $startLine; $y < $img_height; $y += 2) {
	    	for($x = 0; $x<$img_width;++$x){
	        	$color = imagecolorat($mergeImg, $x, $y);
	        	imagesetpixel($tmpImage, $x, $y, $color);
	        }
	    }
	    
	    $this->createNewEditImage($img_width,$img_height);
	    imagecopy($this->editImage,$tmpImage,0,0,0,0,$img_width, $img_height);
	    imagedestroy($mergeImg);
	    imagedestroy($tmpImage);
	} 
	
	function deinterlace($even=false) {
	    $tmpImage = $this->getCopy();
		$img_width = imagesx($tmpImage);
		$img_height = imagesy($tmpImage);

	    $color = imagecolorallocate($tmpImage, $r, $g, $b);
		$startLine=2;
		if($even) $startLine=3;
		
	    for ($y = $startLine; $y <=$img_height; $y += 2) {
	    	for($x = 0; $x<$img_width;++$x){
	        	$colorTop = imagecolorat($tmpImage, $x, $y-2);
	        	$colorBottom = imagecolorat($tmpImage, $x, $y);
	        	$newr = 0;
	            $newg = 0;
	            $newb = 0;
	            $newr += ($colorTop >> 16) & 0xFF;
                $newg += ($colorTop >> 8) & 0xFF;
                $newb += $colorTop & 0xFF;
                $newr += ($colorBottom >> 16) & 0xFF;
                $newg += ($colorBottom >> 8) & 0xFF;
                $newb += $colorBottom & 0xFF;
                $newr /= 2;
	            $newg /= 2;
	            $newb /= 2;
	            $newcol = imagecolorallocate($tmpImage, $newr, $newg, $newb);
				imagesetpixel($tmpImage, $x, $y-1, $newcol);
            }
	        //imageline($tmpImage, 0, $y, $img_width, $y, $color);
	    }
	    
	    $this->createNewEditImage($img_width,$img_height);
	    imagecopy($this->editImage,$tmpImage,0,0,0,0,$img_width, $img_height);
	    imagedestroy($tmpImage);
	} 
	
	function screen($hexColor="000000") {
	    $tmpImage = $this->getCopy();
		$img_width = imagesx($tmpImage);
		$img_height = imagesy($tmpImage);

		$r = hexdec(substr($hexColor,0,2));
		$g = hexdec(substr($hexColor,2,2));
		$b = hexdec(substr($hexColor,4,2));
		
	    $color = imagecolorallocate($tmpImage, $r, $g, $b);
	
	    for($x = 1; $x <= $img_width; $x += 2) {
	        imageline($tmpImage, $x, 0, $x, $img_height, $color);
	    }
	
	    for($y = 1; $y <= $img_height; $y += 2) {
	        imageline($tmpImage, 0, $y, $img_width, $y, $color);
	    }
	    
	    $this->createNewEditImage($img_width,$img_height);
	    imagecopy($this->editImage,$tmpImage,0,0,0,0,$img_width, $img_height);
	    imagedestroy($tmpImage);
	} 

	function noise () {
	    $tmpImage = $this->getCopy();
		$img_width = imagesx($tmpImage);
		$img_height = imagesy($tmpImage);
	
	    for ($x = 0; $x < $img_width; ++$x) {
	        for ($y = 0; $y < $img_height; ++$y) {
	            if (rand(0,1)) {
	                $rgb = imagecolorat($tmpImage, $x, $y);
	                $red = ($rgb >> 16) & 0xFF;
	                $green = ($rgb >> 8) & 0xFF;
	                $blue = $rgb & 0xFF;
	                $modifier = rand(-20,20);
	                $red += $modifier;
	                $green += $modifier;
	                $blue += $modifier;
	
	                if ($red > 255) $red = 255;
	                if ($green > 255) $green = 255;
	                if ($blue > 255) $blue = 255;
	                if ($red < 0) $red = 0;
	                if ($green < 0) $green = 0;
	                if ($blue < 0) $blue = 0;
	
	                $newcol = imagecolorallocate($tmpImage, $red, $green, $blue);
	                imagesetpixel($tmpImage, $x, $y, $newcol);
	            }
	        }
	    }
	    $this->createNewEditImage($img_width,$img_height);
	    imagecopy($this->editImage,$tmpImage,0,0,0,0,$img_width, $img_height);
	    imagedestroy($tmpImage);
	} 
	
	function scatter($pixelDist=4) {
	    $tmpImage = $this->getCopy();
		$img_width = imagesx($tmpImage);
		$img_height = imagesy($tmpImage);
	
	    for ($x = 0; $x < $img_width; ++$x) {
	        for ($y = 0; $y < $img_height; ++$y) {
	            $distx = rand(-$pixelDist, $pixelDist);
	            $disty = rand(-$pixelDist, $pixelDist);
	
	            if ($x + $distx >= $img_width) continue;
	            if ($x + $distx < 0) continue;
	            if ($y + $disty >= $img_height) continue;
	            if ($y + $disty < 0) continue;
	
	            $oldcol = imagecolorat($tmpImage, $x, $y);
	            $newcol = imagecolorat($tmpImage, $x + $distx, $y + $disty);
	            imagesetpixel($tmpImage, $x, $y, $newcol);
	            imagesetpixel($tmpImage, $x + $distx, $y + $disty, $oldcol);
	        }
	    }
	    $this->createNewEditImage($img_width,$img_height);
	    imagecopy($this->editImage,$tmpImage,0,0,0,0,$img_width, $img_height);
	    imagedestroy($tmpImage);
	} 

	function pixelate($blocksize=4) {
	    $tmpImage = $this->getCopy();
		$img_width = imagesx($tmpImage);
		$img_height = imagesy($tmpImage);
	  	
	    for ($x = 0; $x < $img_width; $x += $blocksize) {
	        for ($y = 0; $y < $img_height; $y += $blocksize) {
	            $thiscol = imagecolorat($tmpImage, $x, $y);

	            // set the new red, green, and blue values to 0
	            $newr = 0;
	            $newg = 0;
	            $newb = 0;
	
	            // create an empty array for the colours
	            $colours = array();
	
	            // cycle through each pixel in the block
	            for ($k = $x; $k < $x + $blocksize; ++$k) {
	                for ($l = $y; $l < $y + $blocksize; ++$l) {
	                    // if we are outside the valid bounds of the image, use a safe colour
	                    if ($k < 0) { $colours[] = $thiscol; continue; }
	                    if ($k >= $img_width) { $colours[] = $thiscol; continue; }
	                    if ($l < 0) { $colours[] = $thiscol; continue; }
	                    if ($l >= $img_height) { $colours[] = $thiscol; continue; }
	
	                    // if not outside the image bounds, get the colour at this pixel
	                    $colours[] = imagecolorat($tmpImage, $k, $l);
	                }
	            }
	
	            // cycle through all the colours we can use for sampling
	            foreach($colours as $colour) {
	                // add their red, green, and blue values to our master numbers
	                $newr += ($colour >> 16) & 0xFF;
	                $newg += ($colour >> 8) & 0xFF;
	                $newb += $colour & 0xFF;
	            }
	
	            // now divide the master numbers by the number of valid samples to get an average
	            $numelements = count($colours);
	            $newr /= $numelements;
	            $newg /= $numelements;
	            $newb /= $numelements;
	
	            // and use the new numbers as our colour
	            $newcol = imagecolorallocate($tmpImage, $newr, $newg, $newb);
	            imagefilledrectangle($tmpImage, $x, $y, $x + $blocksize - 1, $y + $blocksize - 1, $newcol);

	        }
	    }
	    
	    $this->createNewEditImage($img_width,$img_height);
	    imagecopy($this->editImage,$tmpImage,0,0,0,0,$img_width, $img_height);
	    imagedestroy($tmpImage);
	} 

	function blur ($dist=1) {
	    $tmpImage = $this->getCopy();
		$img_width = imagesx($tmpImage);
		$img_height = imagesy($tmpImage);
	    	
	    for ($x = 0; $x < $img_width; ++$x) {
	    	set_time_limit(30);
	        for ($y = 0; $y < $img_height; ++$y) {
	            $newr = 0;
	            $newg = 0;
	            $newb = 0;
	
	            $colours = array();
	            $thiscol = imagecolorat($tmpImage, $x, $y);
	
	            for ($k = $x - $dist; $k <= $x + $dist; ++$k) {
	                for ($l = $y - $dist; $l <= $y + $dist; ++$l) {
	                    if ($k < 0) { $colours[] = $thiscol; continue; }
	                    if ($k >= $img_width) { $colours[] = $thiscol; continue; }
	                    if ($l < 0) { $colours[] = $thiscol; continue; }
	                    if ($l >= $img_height) { $colours[] = $thiscol; continue; }
	                    $colours[] = imagecolorat($tmpImage, $k, $l);
	                }
	            }
	
	            foreach($colours as $colour) {
	                $newr += ($colour >> 16) & 0xFF;
	                $newg += ($colour >> 8) & 0xFF;
	                $newb += $colour & 0xFF;
	            }
	
	            $numelements = count($colours);
	            $newr /= $numelements;
	            $newg /= $numelements;
	            $newb /= $numelements;
	
	            $newcol = imagecolorallocate($tmpImage, $newr, $newg, $newb);
	            imagesetpixel($tmpImage, $x, $y, $newcol);
	        }
	    }
	    $this->createNewEditImage($img_width,$img_height);
	    imagecopy($this->editImage,$tmpImage,0,0,0,0,$img_width, $img_height);
	    imagedestroy($tmpImage);
	} 
	
	function setText($xpos, $ypos, $text, $fontsize, $font, $hexcolor, $rotation=0){
		$tmpImage = $this->getCopy();
		$img_width = imagesx($tmpImage);
		$img_height = imagesy($tmpImage);
		//$transCol = imagecolorallocate($tmpImage, 0, 0, 0);
		//$transCol = imagecolortransparent($tmpImage, $transCol);
		imagealphablending($tmpImage,true);
		imagettftext($tmpImage, $fontsize, $rotation, $xpos, $ypos, $hexcolor, $font, $text);

		//$this->createNewEditImage($img_width,$img_height);
	    imagecopy($this->editImage,$tmpImage,0,0,0,0,$img_width, $img_height);
	    imagedestroy($tmpImage);

	}
}