<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Sep 23, 2011
 */
  
class Admin_SectionController extends Zend_Controller_Action {    
    
    /************** PUBLIC FUNCTION **************/
    /**
     * Init.
     */
    public function init() {
        $this->view->h1 = $this->view->trans('Sections');
         
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
        $type = $this->_getParam('type',null);
        // First verification.
        if(empty($type)) {
            PTX_Message::setMessage4User('admin', 'warning', $this->view->trans('You must select section type first.'));
            $this->_redirect('/admin/section/list');
        }
        // Form.
        $params = array(
            'parent_type' => $type,
        );
        $form = new Admin_Form_Section($params);
        $this->view->form = $this->_addForm($this->getRequest(),$form,$params);
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        $this->view->type = $type;
        
        // Page setting.
        $this->view->h2 = $this->view->trans('New Section');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Delete.     
     */
    public function deleteAction() {
        // Data.
        $id = $this->_getParam('id');
        $sectionObj = new Admin_Model_Section($id);
        $data = $sectionObj->getData(false,true);
        
        // Verification. 
        if(!isset($data['status']) || $data['status'] < 0) {
            $this->_redirect('/admin/');
        } 
        
        // Delete.
        $rows = $sectionObj->delete();
        
        // Message + Redirect.
        if($rows > 0) {
            PTX_Message::setMessage4User('admin','done',$this->view->transParam('Section <b>~0~</b> has been deleted.',array('~0~'=>$data['name_'.$this->_locale]),false));
        } else {
            PTX_Message::setMessage4User('admin','warning',$this->view->transParam('Section <b>~0~</b> could not be deleted.',array('~0~'=>$data['name_'.$this->_locale]),false));
        }
        $this->_redirect(PTX_Anchor::getAnchor('admin'));
    }

    /**
     * Edit.     
     */
    public function editAction() {
        // Data.
        $id = $this->_getParam('id');
        $sectionObj = new Admin_Model_Section($id);
        $data = $sectionObj->getData(false,true);
        
        // Verification. 
        if(!isset($data['status']) || $data['status'] < 0) {
            $this->_redirect('/admin/');
        }  
        
        // Form.
        $options = array(
            'parent_type'=>$data['parent_type'],
            'id'=>$id
        );
        $form = new Admin_Form_Section($options);
        $this->view->form = $this->_editForm($this->getRequest(),$form,$sectionObj);
        
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
        $this->_redirect('/admin/section/list');
    }
    
    /**
     * List.     
     */
    public function listAction() {
        
        // Parameters.
        $params = array(
            'sort'   => $this->_getParam('sort','name'),
            'input'  => $this->_getParam('input',25),
            'type' => $this->_getParam('type',null),
            'search' => $this->_getParam('search',null));
        
        $sectionTypes = $this->_getSectionTypes();
        if(empty($params['type'])) {
            $keys = array_keys($sectionTypes);
            $this->_redirect('/admin/section/list/type/'.$keys[0]);
        }
        
        // Zend_Db_Select.
        $select = Admin_Model_DbSelect_Sections::pureSelect();
        $select->columns(array('name_'.$this->_locale.' as name','id','status'))->where('status > -1');
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
        $link = '/admin/section/list';
        
        if($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            
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
                $placesObj = new Admin_Model_Sections();
                $idSection = $placesObj->save($formData,$params);                
                
                PTX_Message::setMessage4User('admin','done',$this->view->transParam('Section <b>~0~</b> has been stored.',array('~0~'=>$formData['name'.$this->_locale]),false));
                if($formData['continue'] == 0) {
                    $this->_redirect('/admin/section/list/type/'.$params['parent_type']);
                } else {
                    $form->populate($formData);    
                }
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
                    $select->order('name_'.$this->_locale.' asc');
                    break;
                case 'name2':
                    $select->order('name_'.$this->_locale.' desc');
                    break;
            }
        }
        
        // Search.
        if(!empty($params['search'])) {
            $string = urldecode($params['search']);
            $select->where('name_'.$this->_locale.' LIKE ?',"%{$string}%");
        }
    }
    
    /**
     * Edit form.
     * 
     * manages form
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Form $form
     * @param Admin_Model_Section $sectionObj
     * @return Zend_Form
     */
    private function _editForm(Zend_Controller_Request_Http $request, Zend_Form $form, Admin_Model_Section $sectionObj) {
        $data = $sectionObj->getData(false,true);
        
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                $formData = $form->getValues();
                $sectionObj->update($formData);
                
                PTX_Message::setMessage4User('admin','done',$this->view->transParam('Section <b>~0~</b> has been updated.',array('~0~'=>$formData['name'.$this->_locale]),false));
                $this->_redirect(PTX_Anchor::getAnchor('admin'));
            }
                
        } else {
            $populate = array('status' => $data['status']);
            $transData = Admin_Model_AppModel::bindTranslations($data, array('name'));
            $populate = array_merge($populate,$transData);
            
            $form->populate($populate);
        }
        
        return $form;
    }
    
    /**
     * Get types.
     * 
     * return array with types
     * @return types
     */
    private function _getSectionTypes() {
        $types = array(
            'banners' => $this->view->trans('Banners'),
        );
        
        return (array)$types;
    }
    
    /**
     * Get sort possibilities.
     * 
     * returns possibilities for list sorting 
     * @return possibilities for list sorting
     */
    private function _getSortPossibilities() {
        $possible = array(
            'name'   => $this->view->trans('Name A-Z'),
            'name2'  => $this->view->trans('Name Z-A'));
        
        return (array)$possible;
    }
}