<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Oct 13, 2011
 */
  
class Admin_LanguageController extends Zend_Controller_Action {    
    
    /************** PUBLIC FUNCTION **************/
    /**
     * Init.
     */
    public function init() {
        $this->view->h1 = $this->view->trans('Languages');
        
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
        $form = new Admin_Form_Language($params);
        $this->view->form = $this->_addForm($this->getRequest(),$form,$params);
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('New Language');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Delete.
     */
    public function deleteAction() {
        // Data.
        $idLanguage = $this->_getParam('id');
        $languageObj = new Admin_Model_Language($idLanguage);
        $data = $languageObj->getData(false,true);
        
        // Verification.
        if(!isset($data['status']) || $data['status'] < 0) {
            $this->_redirect('/admin/index');
        } 
        
        // Delete.
        $rows = $languageObj->delete();
        
        // Message + redirect.
        if($rows > 0) {
            PTX_Message::setMessage4User('admin','done',$this->view->transParam('Language <b>~0~</b> has been deleted.',array('~0~'=>$data['name_'.$this->_locale]),false));
        } else {
            PTX_Message::setMessage4User('admin','warning',$this->view->transParam('Language <b>~0~</b> could not be deleted.',array('~0~'=>$data['name_'.$this->_locale]),false));
        }
        $this->_redirect(PTX_Anchor::getAnchor('admin'));
    }
    
    /**
     * Edit.
     */
    public function editAction() {
        // Data.
        $idLanguage = $this->_getParam('id');
        $languageObj = new Admin_Model_Language($idLanguage);
        $data = $languageObj->getData(false,true);
        
        // Verification.
        if(!isset($data['status']) || $data['status'] < 0) {
            $this->_redirect('/admin/index');
        } 
        
        // Form.
        $params = array('id' => $data['id']);
        $form = new Admin_Form_Language($params);
        $this->view->form = $this->_editForm($this->getRequest(),$form,$languageObj,$params);
        
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
        $this->_redirect('/admin/language/list');
    }
    
    /**
     * List.     
     */
    public function listAction() {
        // Zend_Db_Select.
    	$treeObj = new Admin_Model_Tree_Language();
    	$treeData = $treeObj->getTree(0,0,' > -1');
    	foreach($treeData as &$row) {
    	    $row['name'] = $row['name_'.$this->_locale];
    	}
    	$this->view->treeData = $treeData;
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('List');
        $this->view->headTitle($this->view->h1.' - '.$this->view->h2, 'PREPEND');
        
        // Anchor.
        PTX_Anchor::createAnchor('admin');
    }
    
    /**
     * Position.
     */
    public function positionAction() {
        // nactem parametry
        $id = $this->_getParam('id');
        $way = $this->_getParam('way');
        
        // zmenime pozici
        $languageObj = new Admin_Model_Language($id);
        $languageObj->changePosition($way);
        $this->_redirect(PTX_Anchor::getAnchor('admin'));
    }
    
    /**
     * Transform. 
     */
    public function transformAction() {
        $link = '/admin/language/list';
        
        if($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            
            if(isset($data['type']) && !empty($data['type'])) {
                $link .= '/type/'.urlencode(trim($data['type']));
            }
        }
        
        $this->_redirect($link);
    }
    
    /************** PRIVATE FUNCTION **************/
    /**
     * Add form.
     * 
     * manages form
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Form $form
     * @param array $params - additional params
     * @return Zend_Form
     */
    private function _addForm(Zend_Controller_Request_Http $request, Zend_Form $form, array $params) {
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                // Save language.
                $formData = $form->getValues();
                $languagesObj = new Admin_Model_Languages();
                
                if($languagesObj->codeExists($formData['code'],' > -1')) {
                	PTX_Message::setMessage4User('admin','error',$this->view->transParam('Code <b>~0~</b> already exists in database.',array('~0~'=>$formData['code']),false));
                } else {
                    $idLanguage = $languagesObj->save($formData,$params);                
                
	                PTX_Message::setMessage4User('admin','done',$this->view->transParam('Language <b>~0~</b> has been stored.',array('~0~'=>$formData['name'.$this->_locale]),false));
	                if($formData['continue'] == 0) {
	                    $this->_redirect('/admin/language/list');
	                } else {
	                    $form->populate($formData);    
	                }
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
     * @param Admin_Model_Language $languageObj
     * @param array $params - additional params.
     * @return Zend_Form
     */
    private function _editForm(Zend_Controller_Request_Http $request, Zend_Form $form, Admin_Model_Language $languageObj, array $params) {
        $data = $languageObj->getData(false,true);
        
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                $formData = $form->getValues();
                $languagesObj = new Admin_Model_Languages();
                if($languagesObj->codeExists($formData['code'],' > -1',$data['id'])) {
                    PTX_Message::setMessage4User('admin','error',$this->view->transParam('Code <b>~0~</b> already exists in database.',array('~0~'=>$formData['code']),false));
                } else {
                    $languageObj->update($formData,$params);
                    
                    PTX_Message::setMessage4User('admin','done',$this->view->transParam('Language <b>~0~</b> has been updated.',array('~0~'=>$formData['name'.$this->_locale]),false));
                    $this->_redirect(PTX_Anchor::getAnchor('admin'));
                }
            }
                
        } else {
            $populate = array(
                'code'     => $data['code'],
                'locale'   => $data['locale'],
                'charset'  => $data['charset'],
                'status'   => $data['status']);
            
            $transData = Admin_Model_AppModel::bindTranslations($data, array('name'));
            $populate = array_merge($populate,$transData);
            
            $form->populate($populate);
        }
        
        return $form;
    }
}