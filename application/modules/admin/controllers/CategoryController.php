<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 20.11.2010
**/

class Admin_CategoryController extends Zend_Controller_Action {
    
    /************** PUBLIC FUNCTION **************/
    /**
     * Init.
     */
    public function init() {
        $this->view->h1 = $this->view->trans('Categories');
        
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
        // Variables.
        $type = $this->_getParam('type',null);
        $treeObj = new Admin_Model_Tree_Category();
        
        // First verification.
        if(empty($type)) {
            PTX_Message::setMessage4User('admin', 'warning', $this->view->trans('You must select category type first.'));
            $this->_redirect('/admin/category/list');
        }
        
        // Form.
        $params = array(
            'parent_type' => $type,
            'categories'  => $treeObj->getCategories($type," > -1 ",array('name'),null,'list'));
        $form = new Admin_Form_Category($params);
        $this->view->form = $this->_addForm($this->getRequest(),$form);
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('New category');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Delete.     
     */
    public function deleteAction() {
        // Data.
        $id = $this->_getParam('id');
        $catObj = new Admin_Model_Category($id);
        $data = $catObj->getData(false,true);
        
        // Verification. 
        if(!isset($data['status']) || $data['status'] < 0) {
            $this->_redirect('/admin/index');
        }
        
        // Delete.
        $rows = $catObj->delete();
        
        // Message + Redirect.
        if($rows > 0) {
            PTX_Message::setMessage4User('admin','done',$this->view->transParam('Category <b>~0~</b> has been deleted.',array('~0~'=>$data['name_'.$this->_locale]),false));
        } else {
            PTX_Message::setMessage4User('admin','warning',$this->view->transParam('Category <b>~0~</b> could not be deleted.',array('~0~'=>$data['name_'.$this->_locale]),false));
        }
        $this->_redirect(PTX_Anchor::getAnchor('admin'));
    }
    
    /**
     * Edit.     
     */
    public function editAction() {
        // Data.
        $id = $this->_getParam('id');
        $catObj = new Admin_Model_Category($id);
        $data = $catObj->getData(false,true);
        $treeObj = new Admin_Model_Tree_Category();
        
        // Verification. 
        if(!isset($data['status']) || $data['status'] < 0) {
            $this->_redirect('/admin/index');
        } 
        
        // Form.
        $params = array(
            'parent_type' => $data['parent_type'],
            'id' => $id,
            'categories' => $treeObj->getCategories($data['parent_type'],' > -1',array('name'),$data['id'],'list'));
        $form = new Admin_Form_Category($params);
        $this->view->form = $this->_editForm($this->getRequest(),$form,$catObj);
        
        // View variables.
        $this->view->data = $data;
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        $this->view->fastJump = $params['categories'];
        
        // Page setting.
        $this->view->h2 = $this->view->trans('Edit_2');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Index.
     */
    public function indexAction() {
        // View variables.
        $this->view->categoryTypes = $this->_getCategoryTypes();
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('Select type');
        $this->view->headTitle($this->view->h1.' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * List.     
     */
    public function listAction() {
        // Parameters.
        $params = array('type' => $this->_getParam('type',null));
        
        // Category types.
        $types = $this->_getCategoryTypes();
        if(empty($params['type'])) {
            $keys = array_keys($types);
        	$this->_redirect('/admin/category/list/type/'.$keys[0]);
        }
       
        // Zend_Db_Select.
        $treeObj = new Admin_Model_Tree_Category();
        $treeData = $treeObj->getTree(0,0,' > -1',array('parent_type'=>$params['type']));
        
        // View variables.
        $this->view->treeData = $treeData;
        $this->view->params = $params;
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
        $catObj = new Admin_Model_Category($id);
        $catObj->changePosition($way);
        $this->_redirect(PTX_Anchor::getAnchor('admin'));
    }
    
    /**
     * Transform. 
     */
    public function transformAction() {
        $link = '/admin/category/list';
        
        if($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            
            if(isset($data['type']) && !empty($data['type'])) {
                $link .= '/type/'.urlencode(trim($data['type']));
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
     * @param $place - place
     * @param $type - typ
     * @return Zend_Form
     */
    private function _addForm(Zend_Controller_Request_Http $request, Zend_Form $form) {
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                
                $formData = $form->getValues();               
                $catsObj = new Admin_Model_Categories();
                $idCategory = $catsObj->save($formData,array('file_name'=>null,'parent_type'=>$request->getParam('type')));
                
                if($formData['continue'] == 0) {
                    $this->_redirect('/admin/category/edit/id/'.(int)$idCategory);
                } else {
                    PTX_Message::setMessage4User('admin','done',$this->view->transParam('Category <b>~0~</b> has been stored.',array('~0~'=>$formData['name'.$this->_locale]),false));
                    $this->_redirect('/admin/category/add/type/'.$request->getParam('type'));
                }
            } else {
                $form->populate($form->getValues());
            }
        } 
        
        return $form;
    }
    
    /**
     * zpracuje fomular
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Form $form
     * @param Admin_Model_Category $catObj
     * @return Zend_Form
     */
    private function _editForm(Zend_Controller_Request_Http $request, Zend_Form $form, Admin_Model_Category $catObj) {
        if($request->isPost()) {
            $formData = $request->getPost();
            
            if($form->isValid($formData)) {
                // Update.
                $catObj->update($formData);
                
                // Message + redirect.
                PTX_Message::setMessage4User('admin','done',$this->view->transParam('Category <b>~0~</b> has been updated.',array('~0~'=>$formData['name'.$this->_locale]),false));
            } else {
                $form->populate($formData);
            }
        } else {
            $data = $catObj->getData(false,true);
            
            $populate = array('parent'=>$data['parent_id'], 'status'=>$data['status']);
            $transData = Admin_Model_AppModel::bindTranslations($data, array('name','seo_description','seo_keywords','perex'));
            $populate = array_merge($populate,$transData);
               
            $form->populate($populate);
        }
        
        return $form;
    }
    
    /**
     * Get category types.
     * 
     * return array with types for categories
     * @return types for categories
     */
    private function _getCategoryTypes() {
        $types = array(
            'articles' => $this->view->trans('Article categories'),
            'photos' => $this->view->trans('Photos categories'),
            'adverts' => $this->view->trans('Advert categories'),
        );
        
        return $types;
    }
}