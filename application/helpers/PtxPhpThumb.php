<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 23, 2010
**/

// Needed class.
require_once '../library/phpthumb/ThumbLib.inc.php';  

class Zend_View_Helper_PtxPhpThumb{
    
    /*
     * Config (Private).
     *  
     * Config for php thumb
     */
    private $_config = array(
        'resizeUp' => true, 
        'jpegQuality' => 100, 
        'correctPermission' => false,
        'preserveAlpha' => true, 
        'alphaMaskColor' => array(255,255,255), 
        'preserveTransparency' => true,
        'transparencyMaskColor' => array(255,255,255) 
    );
    
    /*
     * External Image (Private).
     * 
     * image is not store in same server.
     */
    private $_externalImage = false;
    
    /*
     * Log (Private).
     * 
     * Variables holding log information
     */
    private $_log = null;
    
    /*
     * mini (Private).
     * 
     * default setting for mini
     * width - width of thumbnail
     * height - height of thumbnail
     * save_path - path where thumbnails are saved
     * check_size_if_exists - if file exists in save folder, check whether file meets width and height conditions
     */
    private $_mini = array(
        'width' => 150, 
        'height' => 112, 
        'save_path' => './tmp/phpthumb/mini/',
        'check_size_if_exists' => true);
    
    /*
     * Original Folder (Private).
     * 
     * Variables holding path where original pictures are stored.
     */
    private $_originalFolder;
    
    /*
     * Original File (Private).
     * 
     * Variables holding name of original picture
     */
    private $_originalFile;
    
    /*
     * Pht Thumb Obj (Private).
     *  
     * Variables for object of PhpThumb
     */
    private $_phpThumbObj;
    
    /*
     * Thumbnail (Private).
     * 
     * default setting for thumbnail
     * width - width of thumbnail
     * height - height of thumbnail
     * save_path - path where thumbnails are saved
     * check_size_if_exists - if file exists in save folder, check whether file meets width and height conditions
     */
    private $_thumbnail = array(
        'width' => 100, 
        'height' => 100, 
        'save_path' => './tmp/phpthumb/thumbnails/',
        'check_size_if_exists' => true);

    /*
     * ThumbPicture (Private).
     * 
     * holds value of picture which will be generated
     */
    private $_thumbPicture = null;
    
    /*
     * Small (Private).
     * 
     * default setting for small
     * width - width of thumbnail
     * height - height of thumbnail
     * save_path - path where thumbnails are saved
     * check_size_if_exists - if file exists in save folder, check whether file meets width and height conditions
     */
    private $_small = array(
        'width' => 320, 
        'height' => 240, 
        'save_path' => './tmp/phpthumb/thumbnails/',
        'check_size_if_exists' => true);
    
    /**
     * PhpThumb
     * 
     * operates with picture
     * @param $fileName - filename of picture
     * @param $action - what action we would like to process
     * @param $options - options
     * @return link to generated picture
     */
    public function ptxPhpThumb($fileName,$action,array $options = array()) {
        // Set up original folder.
        $this->_originalFolder = (!array_key_exists('original_path',$options)) ? './project/'.PROJECT_FOLDER.'/photo/' : $options['original_path'];
        $this->_originalFile = $fileName;
        $this->_externalImage = (preg_match('/^(http|https):\/\//',$this->_originalFile)) ? true : false;
        $this->_thumbPicture = md5($this->_originalFile).substr($this->_originalFile,strrpos($this->_originalFile,'.'));
         
        // File exists.
        if($this->_fileExists()) {
        	if(!$this->_externalImage) {
        		// Rewrite config.
        		if(array_key_exists('config',$options)) {
        			$this->_config = $options['config'];
        		}

        		// Do proper job.
        		switch($action) {
        			case 'mini':
        				$imageArray = $this->_resize($options,$this->_mini,true);
        				break;
        			case 'mini-force':
        				$imageArray = $this->_resize($options,$this->_mini,false);
        				break;
        			case 'small':
        				$imageArray = $this->_resize($options,$this->_small,true);
        				break;
        			case 'small-force':
        				$imageArray = $this->_resize($options,$this->_small,false);
        				break;
        			case 'thumbnail':
        				$imageArray = $this->_resize($options,$this->_thumbnail,true);
        				break;
        			case 'thumbnail-force':
        				$imageArray = $this->_resize($options,$this->_thumbnail,false);
        				break;
        		}
        	} else {
        		$imageArray = $this->_picasa($options,$action);
        	}
        }
        
        
        // We have image array.
        if(isset($imageArray)) {
            return $this->_return($imageArray, $options);
        }
    }
    
    /**
     * Create object.
     * 
     * create phpthumb object
     */
    private function _createObject() {
        if($this->_externalImage) {
            $this->_phpThumbObj = PhpThumbFactory::create($this->_originalFile);    	    
        } else {
        	$this->_phpThumbObj = PhpThumbFactory::create($this->_originalFolder.$this->_originalFile);
        }
    }
    
    /**
     * File exists.
     * 
     * checks whether file exists or not
     * @return true | false
     */
    private function _fileExists() {
    	
        if($this->_externalImage) {
            return true;            
        } else if(!file_exists($this->_originalFolder.$this->_originalFile)) {
            $this->_log .= 'File '.$this->_originalFolder.$this->_originalFile.' does not exist.';
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Get Image Path (Private).
     * 
     * returns path for image
     * @param string $imagePath - path
     * @return string image path.
     */
    private function _getImagePath($imagePath) {
        if(substr($imagePath,0,1) == '.') {
        	return (string)substr($imagePath,1);
        } else {
        	return (string)$imagePath;
        }
    }
    
    /**
     * Get Original Image Path (Private).
     * 
     * returns original path for image
     * @param string $imagePath - path
     * @return string image path.
     */
    private function _getOriginalImagePath($imagePath) {
        if($this->_externalImage) {
        	return $imagePath;
        }else {
        	$imagePath = $this->_originalFolder.$imagePath;
	        if(substr($imagePath,0,1) == '.') {
	            return (string)substr($imagePath,1);
	        } else {
	            return (string)$imagePath;
	        }
        }
    }
    
    /**
     * Picasa (Private).
     * 
     * 
     * @param $options
     * @param $action
     */
    private function _picasa(array $options, $action) {
    	// Array to be returned.
        if($action == 'mini' || $action == 'mini_force') {
        	$params = $this->_mini;
        } else if($action == 'small' || $action == 'small_force') {
        	$params = $this->_small;
        } else if($action == 'thumbnail' || $action == 'thumbnail_force') {
            $params = $this->_thumbnail;
        }

        // Count width and height
        if($options['image_width'] > $options['image_height']) {
        	$sString = '/s'.$params['width'];
        	$width   = $params['width'];
        	$height  = ($options['image_height']*($params['width']/$options['image_width']));
        } else {
        	$sString = '/s'.$params['height'];
        	$width   = ($options['image_width']*($params['height']/$options['image_height']));
        	$height  = $params['height'];
        }
        
        $cutPoint = strrpos($this->_originalFile,'/');
        $newUrl = substr($this->_originalFile,0,$cutPoint).$sString.substr($this->_originalFile,$cutPoint);
        
        $returnArray = array(
            'image_path' => $newUrl,   
            'width'      => (int)$width,
            'height'     => (int)$height,
        );
                
        return (array)$returnArray;
    }
    
    /**
     * Return.
     * 
     * returns value according to options.
     * @param $imageArray - data about image
     * @param $options - options
     * @return value according to options
     */
    private function _return($imageArray, $options) {
        // We have return type.
        if(array_key_exists('return_type',$options) || !empty($options['return_type'])) {
            
        	$imagePath = $this->_getImagePath($imageArray['image_path']);
            if($options['return_type'] == 'tag') {
                $alt = (array_key_exists('alt',$options)) ? $options['alt'] : null;
                
                return '<img src="'.$imagePath.'" alt="'.$alt.'" width="'.$imageArray['width'].'" height="'.$imageArray['height'].'" />';
            } else if($options['return_type'] == 'tag_link') {
                return $imagePath;
            } else if($options['return_type'] == 'href') {
                // Variables.
                $alt = (array_key_exists('alt',$options)) ? $options['alt'] : null;
                 
                $originalPath = $this->_getOriginalImagePath($this->_originalFile);
                
                // Href options.
                if(array_key_exists('href_options',$options) && is_array($options['href_options'])) {
                    $title = (array_key_exists('title',$options['href_options'])) ? $options['href_options']['title'] : $alt;
                    $rel = (array_key_exists('title',$options['rel'])) ? $options['href_options']['rel'] : 'lightbox';
                    $class = (array_key_exists('class',$options['class'])) ? $options['href_options']['class'] : 'lightbox';
                } else {
                    $title = $alt;
                    $rel = 'lightbox';
                    $class = 'lightbox';
                }
                
                // Code.
                $code = '<a href="'.$originalPath.'" title="'.$title.'" rel="'.$rel.'" class="'.$class.'">';
                $code .= '<img src="'.$imagePath.'" alt="'.$alt.'" width="'.$imageArray['width'].'" height="'.$imageArray['height'].'" />';
                $code .= '</a>';
                
                // Return.
                return $code;
            }
        }    
        
        // Default return - image path.
        return $imageArray['image_path'];
    }
    
    /**
     * Resize.
     *
     * resize picture
     * @param $options - options
     * @param $defaultSetting - default setting
     * @param $adaptive - adaptive resize
     * @return path to thumbnail
     */
    private function _resize(array $options, array $defaultSetting, $adaptive = true) {
        // Count width and height
        if($options['orig_width'] > $options['orig_height']) {
            $options['settings']['height']  = (int)($options['orig_height']*($options['settings']['width']/$options['orig_width']));
        } else {
            $options['settings']['width']   = (int)($options['orig_width']*($options['settings']['height']/$options['orig_height']));
        }
        
        // Rewrite settings.
        if(array_key_exists('settings',$options)) {
            $defaultSetting = $options['settings'];
        }
        
        // File not exists.
        if(!file_exists($defaultSetting['save_path'].$this->_thumbPicture)) {
            // Generates.
            $this->_createObject();
            // Adaptive resize.
            if($adaptive) {
                $this->_phpThumbObj->adaptiveResize($defaultSetting['width'],$defaultSetting['height'])->save($defaultSetting['save_path'].$this->_thumbPicture);
            } else {
                $this->_phpThumbObj->resize($defaultSetting['width'],$defaultSetting['height'])->save($defaultSetting['save_path'].$this->_thumbPicture);
            }
        } else if(array_key_exists('check_size_if_exists',$defaultSetting) && $defaultSetting['check_size_if_exists']) {
            // Get image size info.
            $imageinfo = getimagesize($defaultSetting['save_path'].$this->_thumbPicture);
            
            // If at least one size is different.
            if($imageinfo[0] != $defaultSetting['width'] || $imageinfo[1] != $defaultSetting['height']) {
                // Generates.
                $this->_createObject();
                
                // Adaptive resize.
                if($adaptive) {
                    $this->_phpThumbObj->adaptiveResize($defaultSetting['width'],$defaultSetting['height'])->save($defaultSetting['save_path'].$this->_thumbPicture);
                } else {
                    $this->_phpThumbObj->resize($defaultSetting['width'],$defaultSetting['height'])->save($defaultSetting['save_path'].$this->_thumbPicture);
                }
            }
        }
        
        // Array to be returned.
        $returnArray = array(
            'image_path' => $defaultSetting['save_path'].$this->_thumbPicture,   
            'width' => $defaultSetting['width'],
            'height' => $defaultSetting['height'],
        );
        
        // Return.
        return $returnArray;
    }
}