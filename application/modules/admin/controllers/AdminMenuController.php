<?php

class Admin_AdminMenuController extends Zend_Controller_Action {
    
	/**
	 * Init.
	 */
    public function init() {
        $this->view->h1 = $this->view->trans('Admin menus');
        
        $response = $this->getResponse();
        $response->insert('menuTop',$this->view->render('_menu/top.phtml'));
        $response->insert('incTop',$this->view->render('_inc/top-row.phtml'));
        $response->insert('incLeft',$this->view->render('_inc/left.phtml'));
        
        PTX_Anchor::set($this->getRequest());
        $this->_locale = Zend_Registry::get('PTX_Locale');
        $this->view->locale = $this->_locale;
    }

    /**
     * Add.
     */
    public function addAction() {
        // Form.
        $params = array();
        $form = new Admin_Form_AdminMenu();
        $this->view->form = $this->_addForm($this->getRequest(),$form);
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('New input');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Delete.     
     */
    public function deleteAction() {
        // Data.
        $id = $this->_getParam('id');
        $menuObj = new Admin_Model_AdminMenu($id);
        $data = $menuObj->getData();
        
        // Verification.
        if(!isset($data->status) || $data->status < 0) {
            $this->_redirect('/admin/');
        } 
        
        // Delete. 
        $rows = $menuObj->delete();
        
        // Redirect.
        if($rows > 0) {
            PTX_Message::setMessage4User('admin','done',$this->view->transParam('Menu input <b>~0~</b> has been deleted.',array('~0~'=>$data['name_'.$this->_locale]),false));
        } else {
            PTX_Message::setMessage4User('admin','warning',$this->view->transParam('Menu input <b>~0~</b> could not be deleted.',array('~0~'=>$data['name_'.$this->_locale]),false));
        }
        $this->_redirect(PTX_Anchor::getAnchor('admin'));
    }
    
    /**
     * Edit.     
     */
    public function editAction() {
        // Data.
        $id = $this->_getParam('id');
        $menuObj = new Admin_Model_AdminMenu($id);
        $data = $menuObj->getData();
        
        // Verification. 
        if(!isset($data->status) || $data->status < 0) {
            $this->_redirect('/admin/');
        } 
        
        // Form.
        $form = new Admin_Form_AdminMenu(array('id'=>$id));
        $this->view->form = $this->_editForm($this->getRequest(),$form,$menuObj);
        
        // View variables.
        $this->view->data = $data;
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('Edit_2');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Index.
     */
    public function indexAction() {
        $this->_redirect('/admin/admin-menu/list');
    }
    
    /**
     * List.     
     */
    public function listAction() {
    	// Tree.
        $treeObj = new Admin_Model_Tree_AdminMenu();
        $this->view->tree = $treeObj->getTree(0,0," > -1");
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Anchor.
        PTX_Anchor::createAnchor('admin');
        
        // Page setting.
        $this->view->h2 = $this->view->trans('List');
        $this->view->headTitle($this->view->h1.' - '.$this->view->h2, 'PREPEND');
    }

    /**
     * Position.
     */
    public function positionAction() {
    	// Parameters.
    	$id = $this->_getParam('id');
    	$way = $this->_getParam('way');
    	
    	// Change + redirect.
    	$listObj = new Admin_Model_AdminMenu($id);
    	$listObj->changePosition($way);
    	$this->_redirect('/admin/admin-menu/list');
    }
    
    /************** PRIVATE FUNCTION **************/
    /**
     * Add form.
     * 
     * manages form
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Form $form
     * @return Zend_Form
     */
    private function _addForm(Zend_Controller_Request_Http $request, Zend_Form $form) {
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                $formData = $form->getValues();
                $menusObj = new Admin_Model_AdminMenus();
            	$idMenu = $menusObj->save($formData);
            	
                if($formData['continue'] == 0) {
                    $this->_redirect('/admin/admin-menu/detail/menu/'.(int)$idMenu);
                } else {
                    PTX_Message::setMessage4User('admin','done',$this->view->transParam('Menu input <b>~0~</b> has been stored.',array('~0~'=>$formData['name'.$this->_locale]),false));
                    $this->_redirect('/admin/admin-menu/add');
                }
            } else {
                $form->populate($form->getValues());
            }
        } 
        
        return $form;
    }
    
    /**
     * Edit form.
     * 
     * manages form
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Form $form
     * @param Admin_Model_AdminMenu $menuObj
     * @return Zend_Form
     */
    private function _editForm(Zend_Controller_Request_Http $request, Zend_Form $form, Admin_Model_AdminMenu $menuObj) {
        if($request->isPost()) {
        	$formData = $request->getPost();
        	
        	if($form->isValid($formData)) {
        		$menuObj->update($formData);
        		PTX_Message::setMessage4User('admin','done',$this->view->transParam('Menu input <b>~0~</b> has been updated.',array('~0~'=>$formData['name'.$this->_locale]),false));
        		$this->_redirect(PTX_Anchor::getAnchor('admin'));
        	} else {
        		$form->populate($formData);
        	}
        } else {
        	$data = $menuObj->getData(false,true);
        	
        	$populateArray = array(
        	   'parent'    => $data['parent_id'],
        	   'module'    => $data['module'],
        	   'controller'=> $data['controller'],
        	   'action'    => $data['action'],
        	   'params'    => $data['parameters'],
        	   'status'    => $data['status']);
        	
        	$transData = Admin_Model_AppModel::bindTranslations($data, array('name','description'));
        	$populateArray = array_merge($populateArray,$transData);
        	   
            $form->populate($populateArray);
        }
        
        return $form;
    }
}