<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 18.8.2010
**/

class Photo_IndexController extends Zend_Controller_Action{
    
    /************* PUBLIC FUNCTION  ***************/
    public function init() {
    	$response = $this->getResponse();
        
        // Widgets.
        $response->insert('widgetFooter',$this->view->render('_widget/footer.phtml'));
        $response->insert('widgetSidebar',$this->view->render('_widget/sidebar.phtml'));
        $response->insert('widgetTop',$this->view->render('_widget/top.phtml'));
        $response->insert('widgetUnder',$this->view->render('_widget/under.phtml'));
        
        // Menu
        $response->insert('menuHeader',$this->view->render('_menu/header.phtml'));
        $response->insert('menuMain',$this->view->render('_menu/main.phtml'));
        $response->insert('menuBottom',$this->view->render('_menu/bottom.phtml'));

        // projectconfig
        $this->_config = PTX_Session::getSessionValue('settings', 'project');
        $this->_locale = Zend_Registry::get('PTX_Locale');
        $this->view->locale = Zend_Registry::get('PTX_Locale');
    }

    /**
     * index
     */
    public function indexAction() {
        // Categories.
        $select = Default_Model_DbSelect_Categories::pureSelect();
        $select->columns(array('id','url_'.$this->_locale.' as url','name_'.$this->_locale.' as name','seo_description_'.$this->_locale.' as seo_description','seo_keywords_'.$this->_locale.' as seo_keywords'))
            ->where('status = ?',1)->order('position ASC')->where('parent_type = ?','photos')->where('name_'.$this->_locale.' != ""');
        $stmt = $select->query();
        $data = $stmt->fetchAll();
        
        $thumbnailsParams = array(
            'original_path' => './project/'.PROJECT_FOLDER.'/images/photo/',
            'return_type' => 'tag',
            'settings' => array('width'=>320, 'height'=>218, 'save_path'=>'./tmp/phpthumb/320/', 'check_size_if_exists'=>true),
        );
        $data4View = array();
        foreach($data as $key => $values) {
        	
        	$select = Photo_Model_DbSelect_Photos::pureSelect();
        	$select->columns(array('id','file_name','image_width','image_height'))->where('name_'.$this->_locale.' != ""')
        	   ->where('parent_type = ?','photos')->where('parent_id = ?',(int)$values['id'])->where('status = ?',1)->order('position ASC');
        	$stmt = $select->query();
        	$data = $stmt->fetchAll();
        	
        	$values['count_photos'] = count($data);
        	$thumbnailsParams['alt'] = $values['name'];
        	$thumbnailsParams['image_width'] = $data[0]['image_width'];
        	$thumbnailsParams['image_height'] = $data[0]['image_height'];
        	$values['thumbnail'] = $this->view->ptxPhpThumb($values['logo'],'small',$thumbnailsParams);
        	$data4View[] = $values;
        }
        
        $this->view->categories = $data;
        $this->view->data4View = $data4View;
        
        // Page settings.
        $this->view->headTitle($this->_config['photogallerytitle'.$this->_locale], 'PREPEND');
        $this->view->headMeta()->appendName('description', $this->_config['photogallerydescription'.$this->_locale]);
        $this->view->headMeta()->appendName('keywords', $this->_config['photogallerykeywords'.$this->_locale]);
    }
    
}