<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Sep 24, 2011
 */
  
class Zend_View_Helper_Picture {
    
	/*
	 * Params (Private).
	 * 
	 * holds params for class.
	 */
	private $_params = array(
	   'image_folder' => null,
	   'image_width'  => 0,
	   'image_height' => 0,
	   'image_alt'    => null,
	   
	   'image_ne' => 'text', // possible text / image
	   'image_ne_text' => null,
	   'image_ne_text_class' => 'msg info',
	   'image_ne_image' => null,
	   'image_ne_image_width' => null,
	   'image_ne_image_height' => null,
	);
	
    /**
     * Banner logo.
     * 
     * returns code for banner logo
     * @param string $file - name of file     
     * @param array  $params - additional params
     * @return code
     */
    public function picture($logo, array $params = array()) {        
        $this->_params = array_merge($this->_params,$params);

        $code = null;
        if(!empty($logo) && file_exists($this->_params['image_folder'].$logo)) {
            // Get size information.
        	if($this->_params['image_width'] == 0 && $this->_params['image_height'] == 0) {
            	$imagesize = getimagesize($this->_params['image_folder'].$logo);
            	$whInfo = $imagesize[3];
            } else {
            	$whInfo = null;
            	if($this->_params['image_width'] > 0) {
            		$whInfo .= 'width="'.(int)$this->_params['image_width'].'" ';
            	}
                
            	if($this->_params['image_height'] > 0) {
                    $whInfo .= 'height="'.(int)$this->_params['image_height'].'" ';
                }
            }
            
            $code = '<img src="'.substr($this->_params['image_folder'].$logo,1).'" alt="'.$this->_params['image_alt'].'" title="'.$this->_params['image_alt'].'" '.trim($whInfo).' />';
        } else {
        	if($this->_params['image_ne'] == 'text') {
        		$code = '<p class="'.$this->_params['image_ne_text_class'].'">'.$this->_params['image_ne_text'].'</p>';
        	} else if($this->_params['image_ne'] == 'image') {
        		
        		$whInfo = null;
                if($this->_params['image_width'] > 0) {
                    $whInfo .= 'width="'.(int)$this->_params['image_width'].'" ';
                }
                
                if($this->_params['image_height'] > 0) {
                    $whInfo .= 'height="'.(int)$this->_params['image_height'].'" ';
                }
                
        		$code = '<img src="'.$this->_params['image_ne_image'].$logo.'" alt="'.$this->_params['image_alt'].'" title="'.$this->_params['image_alt'].'" '.trim($whInfo).' />';
        	}
        }
        
        return (string)$code;
    }
}