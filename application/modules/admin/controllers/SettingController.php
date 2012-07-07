<?php
class Admin_SettingController extends Zend_Controller_Action {

    /*************** PUBLIC FUNCTION ***************/
    /**
     * Init.
     */
    public function init() {
        $response = $this->getResponse();
        $response->insert('menuTop',$this->view->render('_menu/top.phtml'));
        $response->insert('incTop',$this->view->render('_inc/top-row.phtml'));
        $response->insert('incLeft',$this->view->render('_inc/left.phtml'));
        
        $this->view->h1 = $this->view->trans('Settings');
    }

    /**
     * Clear cache.     
     */
    public function clearCacheAction() {
        
        $cache = Zend_Registry::get('ptxcache');
        $cache->clean(Zend_Cache::CLEANING_MODE_ALL);
        
        $cache = Zend_Registry::get('ptxcachelong');
        $cache->clean(Zend_Cache::CLEANING_MODE_ALL);
        
        $cache = Zend_Registry::get('ptxcacheshort');
        $cache->clean(Zend_Cache::CLEANING_MODE_ALL);
        
        PTX_Message::setMessage4User('admin','done',$this->view->trans('Page cache has been deleted.'));
        $this->_redirect(PTX_Anchor::getAnchor('admin'));    
    }
    
    /**
     * Index.     
     */
    public function emailAction() {
        // Form.
        $form = new Admin_Form_SettingEmail();
        $this->view->form = $this->_indexForm($this->getRequest(),$form);
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        $this->view->fast_jumps = $this->_fastJump();
        
        // Page setting.
        $this->view->h2 = $this->view->trans('Email Settings');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
        $this->render('index');
    }
    
    /**
     * Index.     
     */
    public function indexAction() {
        // Form.
        $form = new Admin_Form_Setting();
        $this->view->form = $this->_indexForm($this->getRequest(),$form);
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        $this->view->fast_jumps = $this->_fastJump();
        
        // Page setting.
        $this->view->h2 = $this->view->trans('Global Settings');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Seo.     
     */
    public function seoAction() {
        // Form.
        $form = new Admin_Form_SettingSeo();
        $this->view->form = $this->_indexForm($this->getRequest(),$form);
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        $this->view->fast_jumps = $this->_fastJump();
        
        // Page setting.
        $this->view->h2 = $this->view->trans('Seo Settings');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
        $this->render('index');
    }
    
    /**
     * Index form.
     * 
     * manages form
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Form $form
     * @return Zend_Form
     */
    private function _indexForm(Zend_Controller_Request_Http $request, Zend_Form $form) {    	
        $settingsObj = new Admin_Model_Settings();
        
    	if($request->isPost()) {
    		$formData = $request->getPost();
    		
    		if($form->isValid($formData)) {
                $settingsObj->saveArray($formData);
	            
	            PTX_Message::setMessage4User('admin','done',$this->view->trans('New setting has been set.'));
	            $this->_redirect($_SERVER['REQUEST_URI']);
    		} 
            
    		$form->populate($formData);
    	} else {
    		$form->populate($settingsObj->getSettings('list'));
    	}
    	
    	return $form;
    }
    
    /**
     * Fast Jump (Private).
     * 
     * returns array with operation for settings.
     * @return array operations
     */
    private function _fastJump() {
        $array = array(
            'index' => $this->view->trans('Global Settings'),
            'email' => $this->view->trans('Email Settings'),
            'seo'   => $this->view->trans('Seo Settings'),
        );
        return (array)$array;
    }
}