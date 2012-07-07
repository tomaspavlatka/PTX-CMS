<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 22.8.2010
**/

class Admin_WidgetPlaceController extends Zend_Controller_Action {    
    
    /************** PUBLIC FUNCTION **************/
    /**
     * Init.
     */
    public function init() {
        $this->view->h1 = $this->view->trans('Widget places');
        
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
        $form = new Admin_Form_WidgetPlace();
        $this->view->form = $this->_addForm($this->getRequest(),$form);
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('New Place');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Delete.
     */
    public function deleteAction() {
        // Data.
        $idPlace = $this->_getParam('id');
        $placeObj = new Admin_Model_WidgetPlace($idPlace);
        $placeData = $placeObj->getData(false,true);
        
        // Verification.
        if(!isset($placeData['status']) || $placeData['status'] < 0) {
            $this->_redirect('/admin/index');
        } 
        
        // Delete.
        $rows = $placeObj->delete();
        
        // Message + redirect.
        if($rows > 0) {
            PTX_Message::setMessage4User('admin','done',$this->view->transParam('Widget place <b>~0~</b> has been deleted.',array('~0~'=>$placeData['name_'.$this->_locale]),false));
        } else {
            PTX_Message::setMessage4User('admin','warning',$this->view->transParam('Widget place <b>~0~</b> could not be deleted.',array('~0~'=>$placeData['name_'.$this->_locale]),false));
        }
        $this->_redirect(PTX_Anchor::getAnchor('admin'));
    }
    
    /**
     * Edit.
     */
    public function editAction() {
        // Data.
        $idPlace = $this->_getParam('id');
        $placeObj = new Admin_Model_WidgetPlace($idPlace);
        $placeData = $placeObj->getData();
        
        // Verification.
        if(!isset($placeData->status) || $placeData->status < 0) {
            $this->_redirect('/admin/index');
        } 
        
        // Form.
        $form = new Admin_Form_WidgetPlace(array('id'=>$idPlace));
        $this->view->form = $this->_editForm($this->getRequest(),$form,$placeObj);
        
        // View variables.
        $this->view->data = $placeData;
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('Edit_2');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Index.
     */
    public function indexAction() {
        $this->_redirect('/admin/widget-place/list');
    }
    
    /**
     * List.     
     */
    public function listAction() {
        // Zend_Db_Select.
        $select = Admin_Model_DbSelect_WidgetPlaces::pureSelect();
        $select->columns(array('name_'.$this->_locale.' as name','status','id','parent_type'));
        $select->where('status > -1')->order('parent_type asc')->order('name_'.$this->_locale.' asc');
        
        // Paginator.
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setItemCountPerPage(25)->setCurrentPageNumber($this->_getParam('page',1));
        $this->view->paginator = $paginator;
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Anchor.
        PTX_Anchor::createAnchor('admin');
        
        // Page setting.
        $this->view->h2 = $this->view->trans('List');
        $this->view->headTitle($this->view->h1.' - '.$this->view->h2, 'PREPEND');
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
                $placesObj = new Admin_Model_WidgetPlaces();
                $idPlace = $placesObj->save($formData);                
                
                PTX_Message::setMessage4User('admin','done',$this->view->transParam('Widget place <b>~0~</b> has been stored.',array('~0~'=>$formData['name'.$this->_locale]),false));
                if($formData['continue'] == 0) {
                    $this->_redirect('/admin/widget-place/list');
                } else {
                    $form->populate($formData);    
                }
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
     * @param Admin_Model_WidgetPlace $placeObj
     * @return Zend_Form
     */
    private function _editForm(Zend_Controller_Request_Http $request, Zend_Form $form, Admin_Model_WidgetPlace $placeObj) {
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                $formData = $form->getValues();
                $placeObj->update($formData);
                
                PTX_Message::setMessage4User('admin','done',$this->view->transParam('Widget place <b>~0~</b> has been updated.',array('~0~'=>$formData['name'.$this->_locale]),false));
                $this->_redirect(PTX_Anchor::getAnchor('admin'));
            }
                
        } else {
        	$data = $placeObj->getData(false,true);
            $populate = array(
                'status' => $data['status'],
                'type' => $data['parent_type']);
            
            $transData = Admin_Model_AppModel::bindTranslations($data, array('name'));
            $populate = array_merge($populate,$transData);
            
            $form->populate($populate);
        }
        
        return $form;
    }
}