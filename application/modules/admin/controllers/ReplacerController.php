<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Oct 6, 2011
 */
  
class Admin_ReplacerController extends Zend_Controller_Action {    
    
    /************** PUBLIC FUNCTION **************/
    /**
     * Init.
     */
    public function init() {
        $this->view->h1 = $this->view->trans('Replacers');
         
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
        $form = new Admin_Form_Replacer($params);
        $this->view->form = $this->_addForm($this->getRequest(),$form,$params);
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('New Replacer');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Delete.     
     */
    public function deleteAction() {
        // Data.
        $id = $this->_getParam('id');
        $replacerObj = new Admin_Model_Replacer($id);
        $data = $replacerObj->getData(false,true);
        
        // Verification. 
        if(!isset($data['status']) || $data['status'] < 0) {
            $this->_redirect('/admin/');
        } 
        
        // Delete.
        $rows = $replacerObj->delete();
        
        // Message + Redirect.
        if($rows > 0) {
            PTX_Message::setMessage4User('admin','done',$this->view->transParam('Replacer <b>~0~</b> has been deleted.',array('~0~'=>$data['code']),false));
        } else {
            PTX_Message::setMessage4User('admin','warning',$this->view->transParam('Replacer <b>~0~</b> could not be deleted.',array('~0~'=>$data['code']),false));
        }
        $this->_redirect(PTX_Anchor::getAnchor('admin'));
    }

    /**
     * Edit.     
     */
    public function editAction() {
        // Data.
        $id = $this->_getParam('id');
        $replacerObj = new Admin_Model_Replacer($id);
        $data = $replacerObj->getData(false,true);
        
        // Verification. 
        if(!isset($data['status']) || $data['status'] < 0) {
            $this->_redirect('/admin/');
        }   
        
        // Form.
        $params = array('id'=>$id);
        $form = new Admin_Form_Replacer($params);
        $this->view->form = $this->_editForm($this->getRequest(),$form,$replacerObj);
        
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
        $this->_redirect('/admin/replacer/list');
    }
    
    /**
     * List.     
     */
    public function listAction() {
        // Parameters.
        $params = array(
            'sort'   => $this->_getParam('sort','name'),
            'input'  => $this->_getParam('input',25),
            'search' => $this->_getParam('search',null));
        
        // Zend_Db_Select.
        $select = Admin_Model_DbSelect_Replacers::pureSelect();
        $select->columns(array('code','id','status'))->where('status > -1');
        $this->_completeSelectList($select,$params);
        
        // Paginator.
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setItemCountPerPage(25)->setCurrentPageNumber($this->_getParam('page',1));
        $this->view->paginator = $paginator;
        
        // View variables.
        $this->view->params = $params;
        $this->view->sortPossibilities = $this->_getSortPossibilities();
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('List');
        $this->view->headTitle($this->view->h1.' - '.$this->view->h2, 'PREPEND');
        
        // Anchor.
        PTX_Anchor::createAnchor('admin');
    }
    
    /**
     * Transform. 
     */
    public function transformAction() {
        $link = '/admin/replacer/list';
        
        if($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            
            if(isset($data['type']) && !empty($data['type'])) {
                $link .= '/type/'.urlencode(trim($data['type']));
            }
            
            if(isset($data['sort']) && !empty($data['sort'])) {
                $link .= '/sort/'.urlencode(trim($data['sort']));
            }
            
            if(isset($data['input']) && !empty($data['input'])) {
                $link .= '/input/'.urlencode(trim($data['input']));
            }
            
            if(isset($data['search']) && !empty($data['search']) && $data['search'] != $this->view->trans('Search')) {
                $link .= '/search/'.urlencode(trim($data['search']));
            }
        }
        
        $this->_redirect($link);
    }
    
    /**
     * Add form.
     * 
     * manages form
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Form $form
     * @param array $params - additiona params.
     * @return Zend_Form
     */
    private function _addForm(Zend_Controller_Request_Http $request, Zend_Form $form, array $params) {
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                $formData = $form->getValues();
                $replacesObj = new Admin_Model_Replacers();
                
                if($replacesObj->codeExists($formData['code'])) {
                    PTX_Message::setMessage4User('admin','error',$this->view->transParam('Replacer <b>~0~</b> already exists.',array('~0~'=>$formData['code']),false));
                } else {
                	$replacesObj->save($formData,$params);
                    PTX_Message::setMessage4User('admin','done',$this->view->transParam('Replacer <b>~0~</b> has been stored.',array('~0~'=>$formData['code']),false));
                    if($formData['continue'] == 0) {
                        $this->_redirect('/admin/replacer/list');
                    } 
                }
                    
                $form->populate($formData);                    
            }
        } 
        
        return $form;
    }
    
    /**
     * Complete select list.
     * 
     * completes Zend_Db_Select settings
     * @param Zend_Db_Select $select - select
     * @param $params - params
     */
    private function _completeSelectList(Zend_Db_Select $select,$params) {
        // Sort.
        if(!empty($params['sort'])) {
            switch($params['sort']) {
                case 'name':
                    $select->order('code asc');
                    break;
                case 'name2':
                    $select->order('code desc');
                    break;
            }
        }
        
        // Search.
        if(!empty($params['search'])) {
            $string = urldecode($params['search']);
            $select->where('code LIKE ?',"%{$string}%");
        }
    }
    
    /**
     * Edit form.
     * 
     * manages form
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Form $form
     * @param Admin_Model_Replacer $replacerObj
     * @return Zend_Form
     */
    private function _editForm(Zend_Controller_Request_Http $request, Zend_Form $form, Admin_Model_Replacer $replacerObj) {
        $data = $replacerObj->getData(false,true);
        
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                $formData = $form->getValues();
                $replacesObj = new Admin_Model_Replacers();
                
                if($replacesObj->codeExists($formData['code'],$data['id'])) {
                    PTX_Message::setMessage4User('admin','error',$this->view->transParam('Replacer <b>~0~</b> already exists.',array('~0~'=>$formData['code']),false));
                } else {
                    $replacerObj->update($formData);	
                    PTX_Message::setMessage4User('admin','done',$this->view->transParam('Replacer <b>~0~</b> has been updated.',array('~0~'=>$formData['code']),false));
                    $this->_redirect(PTX_Anchor::getAnchor('admin'));
                }
                
                $form->populate($formData);
            }
        } else {
            $populate = array('code'=>$data['code'], 'status'=>$data['status'], 'script'=>$data['script']);
            $transData = Admin_Model_AppModel::bindTranslations($data, array('content'));
            $populate = array_merge($populate,$transData);
            
            foreach($populate as $key => $values) {
                if(strstr($key,'content')) {
                    $populate['p'.$key] = $values;
                    unset($populate[$key]);
                }
            }
            
            $form->populate($populate);
        }
        
        return $form;
    }
    
    /**
     * Get sort possibilities.
     * 
     * returns possibilities for list sorting 
     * @return possibilities for list sorting
     */
    private function _getSortPossibilities() {
        $possible = array(
            'name'   => $this->view->trans('Code A-Z'),
            'name2'  => $this->view->trans('Code Z-A'));
        
        return (array)$possible;
    }
}