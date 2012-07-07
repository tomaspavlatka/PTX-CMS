<?php

require_once APPLICATION_PATH.'/helpers/PtxUrl.php';

class Zend_View_Helper_GetMenu4Print {

	/*
	 * Base Url Helprer (Private).
	 * 
	 * holds instance of Zend_View_Helper_BaseUrl
	 */
	private $_baseUrlHelper;
	
	/*
	 * Cache (Private).
	 * 
	 * holds cache.
	 */
	private $_cache;
	
	/*
	 * Locale (Private).
	 * 
	 * holds information about locale.
	 */
	private $_locale; 
	
	/*
     * Params (Private).
     * 
     * holds default params for menu.
     */
    private $_params = array(
       'block_before'  => null,
       'block_after'   => null,
       'item_before'   => null,
       'item_after'    => null,
       'link_extra'    => null,
       'item_selected_before' => null,
       'item_selected_after'  => null,
       'parent_id'     => null,);
	
	/*
	 * Session Menu Info (Private).
	 * 
	 * holds information about menu in session.
	 */
	private $_sessionMenuInfo;
	
	/*
	 * Url Helper (Private)
	 * 
	 * holds instace of Zend_View_Helper_Url
	 */
	private $_urlHelper;
	
	
	/**
	 * Construct.
	 * 
	 * constructor of the class.
	 */
	public function __construct() {
		$this->_cache = Zend_Registry::get('ptxcachelong');
		$this->_sessionMenuInfo = new Zend_Session_Namespace('menu');
		$this->_urlHelper = new Zend_View_Helper_PtxUrl();
		$this->_baseUrlHelper = new Zend_View_Helper_BaseUrl();
		$this->_locale = Zend_Registry::get('PTX_Locale');
	}
	
	/**
     * Escape
     * 
     * escape string
     * @param $string - string 
     * @return escaped string
     */
	public function escape($string) {
		return htmlspecialchars(trim($string));
	}
	
	/**
     * Get menu 4 print
     * 
     * returns codes to print menu
     * @param integer $placeId - id of the place
     * @param array   $params - additional params
     * @return code to be printed
     */
	public function getMenu4Print($placeId, array $params = array()) {
		// Merge params.
		$this->_params = array_merge($this->_params, $params);
		
		// Build menu.
        $data = $this->_generate($placeId);
         
        return (string)$data;
	}
	
    /**
     * Generate (Private).
     * 
     * generates menu.
     * @param integer $placeId - id of the place
     * @return code for menu
     */
    private function _generate($placeId) {
        // Get data.
    	$data = $this->_getData($placeId,$this->_params['parent_id']);
        
    	
        if(is_array($data) && count($data) > 0) {    
            $codeLink = null;
            $selectedId = $this->_findSelected($placeId);
            
        	foreach($data as $key => $values) {
        	    $link = null;
        	    if($values['parent_type'] == 'link') {
        	        $link = $this->__link($values);
        	    } else if($values['parent_type'] == 'homepage') {
        	        $link = $this->__homepage($values);
        	    } else if($values['parent_type'] == 'article') {
                    $link = $this->__article($values);
                } else if($values['parent_type'] == 'staticpage') {
        	        $link = $this->__staticPage($values);
        	    } else if($values['parent_type'] == 'category') {
        	        $link = $this->__category($values);
        	    } else if($values['parent_type'] == 'photogallery') {
        	        $link = $this->__photogallery($values);
        	    }
        	     
        	    if(isset($link) && !empty($link)) {
        	        if($values['id'] != $selectedId) {
        	            $codeLink .= $this->_params['item_before'].$link.$this->_params['item_after'];
        	        } else {
        	            $codeLink .= $this->_params['item_selected_before'].$link.$this->_params['item_selected_after'];
        	        }
        	    }
            }            
            
            if(!empty($codeLink)) {
            	$code = $this->_params['block_before'].$codeLink.$this->_params['block_after'];
            	return (string)$code;
            }
        }
    }
    
    /**
     * Find selected (Private).
     * 
     * finds selected id of menu for specific place id     
     * @param integer $placeId - id of place
     * @return integer id of selected menu
     */
    private function _findSelected($placeId) {
        if(isset($this->_sessionMenuInfo->active) && isset($this->_sessionMenuInfo->active[$placeId])) {
        	// Variables.
        	$min = 999; 
        	$selected = 0;

        	// Find seleted.
        	foreach($this->_sessionMenuInfo->active[$placeId] as $key => $values) {
        		if($values['level'] < $min) {
        			$min = (int)$values['level'];
        			$selected = $values['path'][0]['id'];
        		}
        	}
        	
        	return (int)$selected;
        }	
    }
    
    /**
     * Get data (Private).
     * 
     * returns data for menu
     * @param integer $placeId - id menu place
     * @param integer $parentId - id of the parent
     * @return data for menu
     */
    private function _getData($placeId, $parentId) {
        $select = Default_Model_DbSelect_MenuInputs::pureSelect();
        $select->columns(array('id','name_'.$this->_locale.' as name','title_'.$this->_locale.' as title','link_'.$this->_locale.' as link','target','parent_type','input_id'))
            ->where('menu_place_id = ?',(int)$placeId)->where('status = 1')->order('position asc')->where('name_'.$this->_locale.' != ""');
            
        if(is_numeric($parentId)) {
        	$select->where('parent_id = ?',$parentId);
        }
        
        $stmt = $select->query();
        return $stmt->fetchAll();                            
    }
	
	/**
	 * Target (Private).
	 * 
	 * returns target tag for menu
	 * @param integer $target - target
	 * @return string menu target
	 */
	private function _target($target) {
		switch($target) {
            case 0:    
                return 'target="_blank"';                 
            case 1:
                return null;                        
        }   
	}
	
    /**
     * Article (Private).
     * 
     * generates link for article.
     * @param array $data - data for link
     * @return string
     */
    private function __article(array $data) {
        $articleObj = new Article_Model_Article($data['input_id']);
        $articleData = $articleObj->getData(false,true);

        if(isset($articleData['status']) && $articleData['status'] == 1 && !empty($articleData['name_'.$this->_locale])) {
            $code = '<a href="'.$this->_baseUrlHelper->baseUrl().$this->_urlHelper->ptxUrl(array('url'=>$articleData['url_'.$this->_locale]),'article',true).'" '.$this->_target($data['target']).' 
                title="'.$this->escape($data['title']).'" '.$this->_params['link_extra'].'>'.$this->escape($data['name']).'</a>';
            return (string)$code;
        }
    }
	
    /**
     * Category (Private).
     * 
     * generates link for category.
     * @param array $data - data for link
     * @return string
     */
    private function __category(array $data) {
        $categoryObj = new Default_Model_Category($data['input_id']);
        $categoryData = $categoryObj->getData(false,true);

        // Category is published.
        if(array_key_exists('status',$categoryData) && $categoryData['status'] == 1 && !empty($categoryData['name_'.$this->_locale])) {
            switch($categoryData['parent_type']) {
                case 'photos':
                    $code = '<a href="'.$this->_baseUrlHelper->baseUrl().$this->_urlHelper->ptxUrl(array('url'=>$categoryData['url_'.$this->_locale]),'photo-category',true).'" '.$this->_target($data['target']).' 
                        title="'.$this->escape($data['title']).'" '.$this->_params['link_extra'].'>'.$this->escape($data['name']).'</a>';
                    break;
                case 'articles':
                    $code = '<a href="'.$this->_baseUrlHelper->baseUrl().$this->_urlHelper->ptxUrl(array('url'=>$categoryData['url_'.$this->_locale]),'article-category',true).'" '.$this->_target($data['target']).' 
                        title="'.$this->escape($data['title']).'" '.$this->_params['link_extra'].'>'.$this->escape($data['name']).'</a>';
                    break;
            }

            if(isset($code)) {
                return (string)$code;
            }
    	}
    }
	
    /**
     * Homepage (Private).
     * 
     * generates link for homepage.
     * @param array $data - data for link
     * @return string.
     */
    private function __homepage(array $data) {

    	$code = '<a href="'.$this->_baseUrlHelper->baseUrl().$this->_urlHelper->ptxUrl(array(),'homepage',true).'" '.$this->_target($data['target']).' 
            title="'.$this->escape($data['title']).'" '.$this->_params['link_extra'].'>'.$this->escape($data['name']).'</a>';
        return (string)$code;
    }
    
	/**
	 * Link (Private).
	 * 
	 * generates link for external link.
	 * @param array $data - data for link
	 * @return string
	 */
    private function __link(array $data) {
    	
    	$code = '<a href="'.$data['link'].'" '.$this->_target($data['target']).' 
            title="'.$this->escape($data['title']).'" '.$this->_params['link_extra'].'>'.$this->escape($data['name']).'</a>';
    	return (string)$code;
    }
    
    /**
     * Photogallery (Private).
     * 
     * generates link for photogallery.
     * @param array $data - data for link
     * @return string
     */
    private function __photogallery(array $data) {
    	$code = '<a href="'.$this->_baseUrlHelper->baseUrl().$this->_urlHelper->ptxUrl(array(),'photogallery',true).'" '.$this->_target($data['target']).' 
            title="'.$this->escape($data['title']).'" '.$this->_params['link_extra'].'>'.$this->escape($data['name']).'</a>';
    	return (string)$code;
    }
    
    /**
     * Static Page (Private).
     * 
     * generates link for static page.
     * @param array $data - data for link
     * @return string
     */
    private function __staticPage(array $data) {
    	$pageObj = new Default_Model_StaticPage($data['input_id']);
    	$pageData = $pageObj->getData(false,true);

    	if(isset($pageData['status']) && $pageData['status'] == 1 && !empty($pageData['name_'.$this->_locale])) {
    		$code = '<a href="'.$this->_baseUrlHelper->baseUrl().$this->_urlHelper->ptxUrl(array('url'=>$pageData['url_'.$this->_locale]),'static-page',true).'" '.$this->_target($data['target']).' 
                title="'.$this->escape($data['title']).'" '.$this->_params['link_extra'].'>'.$this->escape($data['name']).'</a>';
    		return (string)$code;
    	}
    }
}