<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 23.8.2010
**/

class Admin_ArticleController extends Zend_Controller_Action {
    
    public function init() {
        $this->view->h1 = $this->view->trans('Articles');
        
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
        $treeObj = new Admin_Model_Tree_Category();
        
        // Form.
        $params = array(
            'categories' => $treeObj->getCategories('articles','> -1',array('name'),null,'list'));
        $form = new Admin_Form_Article($params);
        $this->view->form = $this->_addForm($this->getRequest(),$form);
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        $this->view->params = $params;
        
        // Page settings.
        $this->view->h2 = $this->view->trans('New Article');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Delete.      
     */
    public function deleteAction() {
        // Data.
        $id = $this->_getParam('id');
        $articleObj = new Admin_Model_Article($id);
        $data = $articleObj->getData(false,true);
        
        // Verification.
        if(!isset($data['status']) || $data['status'] < 0) {
            $this->_redirect('/admin/index');
        }
        
        // Delete. 
        $rows = $articleObj->delete();
        
        // Message + redirect.
        if($rows > 0) {
            PTX_Message::setMessage4User('admin','done',$this->view->transParam('Article <b>~0~</b> has been deleted.',array('~0~'=>$data['name_'.$this->_locale]),false));
        } else {
            PTX_Message::setMessage4User('admin','warning',$this->view->transParam('Article <b>~0~</b> could not be deleted.',array('~0~'=>$data['name_'.$this->_locale]),false));
        }
        $this->_redirect(PTX_Anchor::getAnchor('admin'));
    }
    
    
    /**
     * Edit.     
     */
    public function editAction() {
        // Data.
        $id = $this->_getParam('id');
        $articleObj = new Admin_Model_Article($id);
        $data = $articleObj->getData(false,true);
        $treeObj = new Admin_Model_Tree_Category();
        
        // Verification.
        if(!isset($data['status']) || $data['status'] < 0) {
            $this->_redirect('/admin/index');
        } 
        
        // Form.
        $params = array(
            'id' => $data['id'],
            'categories' => $treeObj->getCategories('articles','> -1',array('name'),null,'list'));
        $form = new Admin_Form_Article($params);
        $this->view->form = $this->_editForm($this->getRequest(),$form,$articleObj);
        
        // Categories.
        $select = Admin_Model_DbSelect_CategoryRelations::pureSelect();
        $select->columns(array('id','category_id'))->where('parent_type = ?','articles')->where('parent_id = ?',(int)$data['id'])->where('status = ?',1)
            ->order('id asc');
        $stmt = $select->query();
        $categories = $stmt->fetchAll();
        
        foreach($categories as $key => $values) {
            $categoryObj = new Admin_Model_Category($values['category_id']);
            $categoryData = $categoryObj->getData(false,true);
            
            if(isset($categoryData['status']) && $categoryData['status'] > -1) {
                $categories[$key]['category_name'] = $categoryData['name_'.$this->_locale];
            } else {
                unset($categories[$key]);
            }
        }
        $this->view->categories = $categories;
        
        // Tags.
        $select = Admin_Model_DbSelect_TagRelations::pureSelect();
        $select->columns(array('id','tag_id'))->where('parent_type = ?','articles')->where('parent_id = ?',(int)$data['id'])->where('status = ?',1)
            ->order('id asc');
        $stmt = $select->query();
        $tags = $stmt->fetchAll();
        
        foreach($tags as $key => $values) {
            $tagObj = new Admin_Model_Tag($values['tag_id']);
            $tagData = $tagObj->getData(false,true);
            
            if(isset($tagData['status']) && $tagData['status'] > -1) {
                $tags[$key]['tag_name'] = $tagData['name_'.$this->_locale];
            } else {
                unset($tags[$key]);
            }
        }
        $this->view->tags = $tags;
        
         // View variables.
        $this->view->data = $data;
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        $this->view->params = $params;
        $this->view->tags4Select = Admin_Model_DbSelect_Tags::getList($this->_locale,array('name'));
        $this->view->fastJump = $this->_getArticles(" > -1");
        
        // Page settings.
        $this->view->h2 = $this->view->trans('Edit_2');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Index.
     */
    public function indexAction() {
        $this->_redirect('/admin/article/list');
    }
    
    /**
     * List.     
     */
    public function listAction() {
        
        // Parameters.
        $params = array(
            'sort'   => $this->_getParam('sort','published2'),
            'input'  => $this->_getParam('input',25),
            'cat'    => $this->_getParam('cat',0),    
            'search' => $this->_getParam('search',null));
        
        // Zend_Db_Select
        $select = Admin_Model_DbSelect_Articles::pureSelect();
        $select->columns(array('id','name_'.$this->_locale.' as name','published','status','shown'))->where('status > -1');
        $this->_completeSelectList($select,$params);
        
        // Paginator.
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setItemCountPerPage($params['input'])->setCurrentPageNumber($this->_getParam('page',1));
        $fullData = array();
        foreach($paginator as &$row) {}
        $this->view->paginator = $paginator;
            
        // View variables.
        $treeObj = new Admin_Model_Tree_Category();
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        $this->view->sortPossibilities = $this->_getSortPossibilities();
        $this->view->categoryTree = $treeObj->getCategories('articles','> -1',array('name'),null,'list');
        $this->view->params = $params;
        
        // Page settings.
        $this->view->h2 = $this->view->trans('List');
        $this->view->headTitle($this->view->h1.' - '.$this->view->h2, 'PREPEND');
        
        // Anchor.
        PTX_Anchor::createAnchor('admin');
    }
    
    /**
     * Transform. 
     */
    public function transformAction() {
        $link = '/admin/article/list';
        
        if($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            
            if(isset($data['sort']) && !empty($data['sort'])) {
                $link .= '/sort/'.urlencode(trim($data['sort']));
            }
            
            if(isset($data['input']) && !empty($data['input'])) {
                $link .= '/input/'.urlencode(trim($data['input']));
            }
            
            if(isset($data['cat']) && !empty($data['cat'])) {
                $link .= '/cat/'.urlencode(trim($data['cat']));
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
                
                // Save article.
                $artilesObj = new Admin_Model_Articles();
                $articleId = $artilesObj->save($formData);
                
                PTX_Message::setMessage4User('admin','done',$this->view->transParam('Article <b>~0~</b> has been stored.',array('~0~'=>$formData['name'.$this->_locale]),false));
                $this->_redirect('/admin/article/edit/id/'.$articleId);
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
     * completes Zend_Db_Select instance settings
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
            $select->where('name_'.$this->_locale.' LIKE ? OR short_name_'.$this->_locale.' LIKE ? OR seo_description_'.$this->_locale.' LIKE ? OR seo_keywords_'.$this->_locale.' LIKE ?',"%{$string}%");
        }
        
        // Category.
        if(!empty($params['cat'])) {            
            $select->where('category_id = ?',(int)$params['cat']);
        }
    }
    
    /**
     * Edit form.
     * 
     * manages form
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Form $form
     * @param Admin_Model_Article $articleObj
     * @return Zend_Form
     */
    private function _editForm(Zend_Controller_Request_Http $request, Zend_Form $form, Admin_Model_Article $articleObj) {
        $data = $articleObj->getData(false,true);
        
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                $formData = $form->getValues();
                $articleObj->update($formData);
                
                PTX_Message::setMessage4User('admin','done',$this->view->transParam('Article <b>~0~</b> has been updated.',array('~0~'=>$formData['name'.$this->_locale]),false));
            }
        } else {
            $populate = array(
                'published'    => date('d/m/Y H:i:s',$data['published']),
                'image'        => $data['image_file'],
                'status'       => $data['status']);
            
            $transData = Admin_Model_AppModel::bindTranslations($data, array('name','perex','content','seo_description','seo_keywords','short_name'));
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
     * Get Articles (Private).
     * 
     * returns array with articles.
     * @param string $status - status of articles we need
     * @return array list of articles
     */
    private function _getArticles($status = " > -1") {
        $select = Admin_Model_DbSelect_Articles::pureSelect();
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
    
    /**
     * Get sort possibilities.
     * 
     * returns all possible values for sort
     * @return possible values for sort
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
}