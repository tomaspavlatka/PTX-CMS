<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 22.8.2010
**/

class Admin_StaticPageController extends Zend_Controller_Action {
    
    /************** PUBLIC FUNCTION **************/
    /**
     * Init.
     * 
     * init function of controller
     */
    public function init() {
        $this->view->h1 = $this->view->trans('Static pages');
        
        $response = $this->getResponse();
        $response->insert('menuTop',$this->view->render('_menu/top.phtml'));
        $response->insert('incTop',$this->view->render('_inc/top-row.phtml'));
        $response->insert('incLeft',$this->view->render('_inc/left.phtml'));
        
        PTX_Anchor::set($this->getRequest());
        $this->_locale = Zend_Registry::get('PTX_Locale');
        $this->view->locale = $this->_locale;
    }

    /**
     * Add action.
     */
    public function addAction() {
        // Form.
        $form = new Admin_Form_StaticPage();
        $this->view->form = $this->_addForm($this->getRequest(),$form);
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('New Static page');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Delete.      
     */
    public function deleteAction() {
        // Data.
        $id = $this->_getParam('id');
        $pageObj = new Admin_Model_StaticPage($id);
        $data = $pageObj->getData(false,true);
        
        // Verification.
        if(!isset($data['status']) || $data['status'] < 0) {
            $this->_redirect('/admin/index');
        }

        // Delete.
        $rows = $pageObj->delete();
        
        // Message + Redirect.
        if($rows > 0) {
            PTX_Message::setMessage4User('admin','done',$this->view->transParam('Static page <b>~0~</b> has been deleted.',array('~0~'=>$data['name_'.$this->_locale]),false));
        } else {
            PTX_Message::setMessage4User('admin','warning',$this->view->transParam('Static page <b>~0~</b> could not deleted.',array('~0~'=>$data['name_'.$this->_locale]),false));
        }
        $this->_redirect(PTX_Anchor::getAnchor('admin'));
    }
    
    /**
     * Edit.     
     */
    public function editAction() {
        // Data.
        $id = $this->_getParam('id');
        $pageObj = new Admin_Model_StaticPage($id);
        $data = $pageObj->getData();
        
        // Verification. 
        if(!isset($data->status) || $data->status < 0) {
            $this->_redirect('/admin');
        } 
        
        // Form.
        $form = new Admin_Form_StaticPage();
        $this->view->form = $this->_editForm($this->getRequest(),$form,$pageObj);
        
        // View variables.
        $this->view->data = $data;
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        $this->view->fastJump = $this->_getStaticPages(" > -1");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('Edit_2');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Index.
     */
    public function indexAction() {
        $this->_redirect('/admin/static-page/list');
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
        
        // Zend_Db_Select
        $select = Admin_Model_DbSelect_StaticPages::pureSelect();
        $select->columns(array('id','name_'.$this->_locale.' as name','published','status','shown'))->where('status > -1');
        $this->_completeSelectList($select,$params);
        
        // Paginator.
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setItemCountPerPage($params['input'])->setCurrentPageNumber($this->_getParam('page',1));
        $this->view->paginator = $paginator;
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        $this->view->sortPossibilities = $this->_getSortPossibilities();
        $this->view->params = $params;
        
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
        $link = '/admin/static-page/list';
        
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
     * @return Zend_Form
     */
    private function _addForm(Zend_Controller_Request_Http $request, Zend_Form $form) {
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                $formData = $form->getValues();
                
                $pagesObj = new Admin_Model_StaticPages();
                $idPage = $pagesObj->save($formData);
                
                $pageObj = new Admin_Model_StaticPage($idPage);
                $pageObj->saveRevision($formData);
                
                PTX_Message::setMessage4User('admin','done',$this->view->transParam('Static page <b>~0~</b> has been added.',array('~0~'=>$formData['name'.$this->_locale]),false));
                $this->_redirect('/admin/static-page/edit/id/'.$idPage);
                
            } else {
                $form->populate($form->getValues());
            }
        } else {
            $form->populate(array('published'=>date('d/m/Y H:i:s')));
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
                case 'shown':
                    $select->order('shown asc');
                    break;
                case 'shown2':
                    $select->order('shown desc');
                    break;
                case 'published':
                    $select->order('published asc');
                    break;
                case 'published2':
                    $select->order('published desc');
                    break;                        
            }
        }
        
        // Search.
        if(!empty($params['search'])) {
            $string = urldecode($params['search']);
            $select->where('name_'.$this->_locale.' LIKE ? OR seo_description_'.$this->_locale.' LIKE ? OR seo_keyword_'.$this->_locale.' LIKE ?',"%{$string}%");
        }
    }
    
    /**
     * Edit form.
     * 
     * manage form
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Form $form
     * @param Admin_Model_StaticPage $pageObj
     * @return Zend_Form
     */
    private function _editForm(Zend_Controller_Request_Http $request, Zend_Form $form, Admin_Model_StaticPage $pageObj) {
        $data = $pageObj->getData(false,true);
        
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                $formData = $form->getValues();
                
                $pagesObj = new Admin_Model_StaticPages();
                $correct = true;
                
                // Update.
                if($correct) {
                    $pageObj->update($formData);
                    $pageObj->saveRevision($formData);
                    
                    PTX_Message::setMessage4User('admin','done',$this->view->transParam('Static page <b>~0~</b> has been updated.',array('~0~'=>$formData['name'.$this->_locale]),false));
                }
            }
        } else {
            $populate = array(
                'published'    => date('d/m/Y H:i:s',$data['published']),
                'image'        => $data['image_file'],
                'status'       => $data['status']);
            
            $transData = Admin_Model_AppModel::bindTranslations($data, array('name','seo_description','seo_keywords','content','notice'));
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
            'name'   => $this->view->trans('Name A-Z'),
            'name2'  => $this->view->trans('Name Z-A'),
            'shown'   => $this->view->trans('Shown 0-9'),
            'shown2'  => $this->view->trans('Shown 9-0'),
            'published'   => $this->view->trans('Published 0-9'),
            'published2'  => $this->view->trans('Published 9-0'));
        
        return $possible;
    }
    
    /**
     * Get Static Pages (Private).
     * 
     * returns array with static pages.
     * @param string $status - status of pages we need
     * @return array list of pages
     */
    private function _getStaticPages($status = " > -1") {
        $select = Admin_Model_DbSelect_StaticPages::pureSelect();
        $select->columns(array('id','name_'.$this->_locale.' as name'))->where('status '.$status)->order('name asc');
        $stmt = $select->query();
        $data = $stmt->fetchAll();
        
        $list = array();
        if(!empty($data)) {
            foreach($data as $key => $values) {
                $list[$values['id']] = $values['name'];
            }
        }
        
        return (array)$list;
    }
}