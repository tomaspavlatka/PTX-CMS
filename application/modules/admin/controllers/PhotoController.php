<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 20, 2010
**/

class Admin_PhotoController extends Zend_Controller_Action {
    
    /************** PUBLIC FUNCTION **************/
    /**
     * Init.
     */
    public function init() {
        $this->view->h1 = $this->view->trans('Photos');
        
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
        // Params.
        $params = array(
            'parent_type' => $this->_getParam('type',null),
            'parent' => $this->_getParam('parent',null)
        );
        // First verification.
        if(empty($params['parent_type'])) {
            PTX_Message::setMessage4User('admin', 'warning', $this->view->trans('You must select parent type and id first.'));
            $this->_redirect('/admin/photo/');
        }
        
        $treeObj = new Admin_Model_Tree_Category();
        // Form.
        $params['categories'] = $treeObj->getCategories('photos','> -1',array('name'),null,'list');
        $form = new Admin_Form_Photo($params);
        $this->view->form = $this->_addForm($this->getRequest(),$form,$params);
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        $this->view->params = $params;
        
        // Page setting.
        $this->view->h2 = $this->view->trans('New photo');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Delete.     
     */
    public function deleteAction() {
        // Data.
        $id = $this->_getParam('id');
        $photoObj = new Admin_Model_Photo($id);
        $data = $photoObj->getData(false,true);
        
        // Verification. 
        if(!isset($data['status']) || $data['status'] < 0) {
            $this->_redirect('/admin/');
        }  
        
        // Delete.
        $rows = $photoObj->delete();
        
        // Message + Redirect.
        if($rows > 0) {
            PTX_Message::setMessage4User('admin','done',$this->view->transParam('Photo <b>~0~</b> has been deleted.',array('~0~'=>$data['name_'.$this->_locale]),false));
        } else {
            PTX_Message::setMessage4User('admin','warning',$this->view->transParam('Photo <b>~0~</b> could not be deleted.',array('~0~'=>$data['name_'.$this->_locale]),false));
        }
        $this->_redirect(PTX_Anchor::getAnchor('admin'));
    }
    
    /**
     * Edit.     
     */
    public function editAction() {
        // Data.
        $id = $this->_getParam('id');
        $photoObj = new Admin_Model_Photo($id);
        $data = $photoObj->getData(false,true);
        
        // Verification. 
        if(!isset($data['status']) || $data['status'] < 0) {
            $this->_redirect('/admin/');
        }  
        
        // Form.
        $treeObj = new Admin_Model_Tree_Category();
        $params = array(
            'parent_type' => $data['parent_type'],
            'parent'      => $data['parent_id'],
            'categories'  => $treeObj->getCategories('photos','> -1',array('name'),null,'list'),
            'id'  =>$id);
        $form = new Admin_Form_Photo($params);
        $this->view->form = $this->_editForm($this->getRequest(),$form,$photoObj, $params);
        
        // View variables.
        $this->view->data = $data;
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Thumbnails.
        $thumbnailsParams = array(
            'original_path' => './userfiles/images/photo/',
            'return_type'   => 'href',
            'settings'      => array('width'=>200, 'height'=>160, 'save_path'=>'./tmp/phpthumb/50/', 'check_size_if_exists'=>true),
            'alt'           => $data['name_'.$this->_locale],
            'orig_width'    => $data['image_width'],
            'orig_height'   => $data['image_height']);
        $this->view->thumbnail = $this->view->ptxPhpThumb($data['file_name'],'thumbnail',$thumbnailsParams);
        
        // Page setting.
        $this->view->h2 = $this->view->trans('Edit_2');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Index.
     */
    public function indexAction() {
        // Parent types.
        $types = $this->_getParentTypes();
        
        // Links.
        $links = array();
        foreach($types as $ident => $name) {
            $links[] = array(
                'link' => '/admin/photo/list/type/'.$ident,
                'name' => $name,
                'description' => $name);
        }
        
        // View variables.
        $this->view->links = $links;
        
        // Page setting.
        $this->view->h2 = $this->view->trans('Select type');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * List.     
     */
    public function listAction() {
        // Parameters.
        $params = array(
            'type'   => $this->_getParam('type',null),
            'parent' => $this->_getParam('parent',null),
            'input'  => $this->_getParam('input',25),
            'search' => $this->_getParam('search',null),
            'page' =>   $this->_getParam('page',1));
        $parents = $this->_getParents($params['type']);
        
        if(empty($params['type'])) {
            $this->_redirect('/admin/photo');
        } else if(empty($params['parent'])) {
            $keys = array_keys($parents);
            if(isset($keys[0])) {
                $this->_redirect('/admin/photo/list/type/'.$params['type'].'/parent/'.$keys[0]);
            }
        }
        
        // Zend_Db_Select      
        $select = Admin_Model_DbSelect_Photos::pureSelect();
        $select->columns(array('id','name_'.$this->_locale.' as name','file_name','status','image_width','image_height','created'))
            ->where('status > -1')->where('parent_type = ?',$params['type'])
            ->order('position asc');
        $this->_completeSelectList($select, $params);
        
        // Paginator.
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setCurrentPageNumber($params['page'])->setItemCountPerPage($params['input']);
        
        // Thumbnails.
        $thumbnailsParams = array(
            'original_path' => './userfiles/images/photo/',
            'return_type' => 'href',
            'settings' => array('width'=>200, 'height'=>160, 'save_path'=>'./tmp/phpthumb/50/', 'check_size_if_exists'=>true),
        );
        $data4View = array();
        foreach($paginator as $row) {
            $thumbnailsParams['alt'] = $row['name'];
            $thumbnailsParams['orig_width'] = $row['image_width'];
            $thumbnailsParams['orig_height'] = $row['image_height'];
            $row['thumbnail'] = $this->view->ptxPhpThumb($row['file_name'],'thumbnail',$thumbnailsParams);
            $data4View[] = $row; 
        }
        
        // View variables.
        $this->view->paginator = $paginator;
        $this->view->data4View = $data4View;
        $this->view->params = $params;
        $this->view->parents = $parents;
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('List');
        $this->view->headTitle($this->view->h1.' - '.$this->view->h2, 'PREPEND');
        
        // Anchor.
        PTX_Anchor::createAnchor('admin');
    }
    
    /**
     * Organize.
     */
    public function organizeAction() {
        $params = array(
            'parent_id'   => $this->_getParam('parent',null),
            'parent_type' => $this->_getParam('type',null),
        );
        
        if(empty($params['parent_id'])) {
            if(!empty($params['parent_type'])) {
                $this->_redirect('/admin/photo/list/type/'.$params['parent_type']);
            } else {
                $this->_redirect('/admin/photo');
            }
        } else if(empty($params['parent_type'])) {
            $this->_redirect('/admin/photo');
        }
        
        // Zend_Db_Select      
        $select = Admin_Model_DbSelect_Photos::pureSelect();
        $select->columns(array('id','name_'.$this->_locale.' as name','file_name','image_width','image_height'))
            ->where('status > -1')->where('parent_type = ?',$params['parent_type'])->where('parent_id = ?',(int)$params['parent_id'])
            ->order('position asc');
        $stmt = $select->query();
        $photos = $stmt->fetchAll();
        
        // Thumbnails.
        $thumbnailsParams = array(
            'original_path' => './userfiles/images/photo/',
            'return_type' => 'tag',
            'settings' => array('width'=>200, 'height'=>160, 'save_path'=>'./tmp/phpthumb/50/', 'check_size_if_exists'=>true));
        $data4View = array();
        foreach($photos as $row) {
            $thumbnailsParams['alt'] = $row['name'];
            $thumbnailsParams['orig_width'] = $row['image_width'];
            $thumbnailsParams['orig_height'] = $row['image_height'];
            $row['thumbnail'] = $this->view->ptxPhpThumb($row['file_name'],'thumbnail',$thumbnailsParams);
            $data4View[] = $row; 
        }
        
        // View variables.
        $this->view->data4View = $data4View;
        $this->view->params = $params;
        $this->view->parents = $this->_getParents($params['parent_type'],$params['parent_type'].'_');
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        $this->view->parent_data = $this->_getParentData($params['parent_type'],$params['parent_id']);
        
        // Page setting.
        $this->view->h2 = $this->view->trans('Organize photos');
        $this->view->headTitle($this->view->h1.' - '.$this->view->h2, 'PREPEND');
        
        // Anchor.
        PTX_Anchor::createAnchor('admin');
    }
    
    /**
     * Transform. 
     */
    public function transformAction() {
        $link = '/admin/photo/list';
        
        if($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            
            // Parent type.
            if(isset($data['type']) && !empty($data['type'])) {
                $link .= '/type/'.urlencode(trim($data['type']));
            }
            
            // Parent.
            if(isset($data['parent']) && !empty($data['parent'])) {
                $link .= '/parent/'.(int)$data['parent'];
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
     * @param array $params - additional params
     * @return Zend_Form
     */
    private function _addForm(Zend_Controller_Request_Http $request, Zend_Form $form, array $params) {
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                // Store picture.
                if(isset($_FILES['logo']['name']) && !empty($_FILES['logo']['name'])) {
                    $extension = PTX_Image2::getImageExtension($_FILES['logo']['type']);
                    $secureName = PTX_Uri::getUri($_POST['name'.$this->_locale]).'-'.time().'.'.$extension;
                    $secureFilePath = './userfiles/images/photo/'.$secureName;
                    $form->logo->addFilter('Rename',array('target' => $secureFilePath,'overwrite'=>true));
                    
                    if(!$form->logo->receive()) {
                        PTX_Message::setMessage4User('admin','warning',$this->view->trans('Problem during uploading photo.'));
                        $secureName = null;
                    } else {
                        $imageSize = getimagesize($secureFilePath);
                        $params['image_width'] = $imageSize[0];
                        $params['image_height'] = $imageSize[1];
                    }
                }

                // Data.
                $params['image_file'] = (isset($secureName)) ? $secureName : null;
                $formData = $form->getValues();
                
                // Save.
                $photosObj = new Admin_Model_Photos();
                $idPhoto = $photosObj->save($formData,$params);
                
                // Save tags relations.
                if(!empty($formData['tags'])) {
                    $tagsObj = new Admin_Model_TagRelations();
                    $tagsObj->saveFromArray($formData['tags'], $idPhoto, 'photos');
                }
                
                PTX_Message::setMessage4User('admin','done',$this->view->transParam('Photo <b>~0~</b> has been stored.',array('~0~'=>$formData['name'.$this->_locale]),false));
                
                // Message + Redirect.
                if($formData['continue'] == 0) {
                    $this->_redirect('/admin/photo/list/type/'.$params['parent_type'].'/parent/'.(int)$params['parent']);
                } else {
                    $this->_redirect('/admin/photo/add/type/'.$params['parent_type'].'/parent/'.(int)$params['parent']);
                }
            } else {
                $form->populate($form->getValues());
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
        // Parent.
        if(!empty($params['parent'])) {
            $select->where('parent_id = ?',(int)$params['parent']);
        } else {
        	$select->where('parent_id = ?',-10);
        }
        
        // Search.
        if(!empty($params['search'])) {
            $string = urldecode($params['search']);
            $select->where('name_'.$this->_locale.' LIKE ? OR seo_description_'.$this->_locale.' LIKE ? OR seo_keywords_'.$this->_locale.' LIKE ? OR perex_'.$this->_locale.' LIKE ?',"%{$string}%");
        }
    }
    
    /**
     * zpracuje fomular
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Form $form
     * @param Admin_Model_Photo $photoObj
     * @return Zend_Form
     */
    private function _editForm(Zend_Controller_Request_Http $request, Zend_Form $form, Admin_Model_Photo $photoObj, $params) {
        $data = $photoObj->getData(false,true);
        
        if($request->isPost()) {
            $formData = $request->getPost();
            
            if($form->isValid($formData)) {
                // Update.
                $photoObj->update($formData,$params);
                
                // Save tags relations.
                if(!empty($formData['tags'])) {
                    $tagsObj = new Admin_Model_TagRelations();
                    $tagsObj->saveFromArray($formData['tags'], $data['id'], 'photos');
                }
                
                // Message + redirect.
                PTX_Message::setMessage4User('admin','done',$this->view->transParam('Photo <b>~0~</b> has been updated.',array('~0~'=>$formData['name'.$this->_locale]),false));
                $this->_redirect(PTX_Anchor::getAnchor('admin'));
            } else {
                $form->populate($formData);
            }
        } else {
            $populate = array(
               'parent'    => $data['parent_id'],
               'status'    => $data['status']);
            $transData = Admin_Model_AppModel::bindTranslations($data, array('name','perex','seo_description','seo_keywords'));
            $populate = array_merge($populate,$transData);
            
            // Tags.
            $tagsObj = new Admin_Model_TagRelations();
            $populate['tags'] = $tagsObj->get4Parent($data['id'], 'photos', 'list');
               
            $form->populate($populate);
        }
        
        return $form;
    }
    
    /**
     * Get Parent Data (Private).
     * 
     * returns data about parent.
     * @param string $parentType
     * @param integer $parentId
     * @return array data about parent.
     */
    private function _getParentData($parentType,$parentId) {
        if($parentType == 'photos') {
            $select = Admin_Model_DbSelect_Categories::pureSelect();
            $select->columns(array('id','name_'.$this->_locale.' as name'))
                ->where('parent_type = ?',$parentType)->where('status > -1')->where('id = ?',(int)$parentId);
            $stmt = $select->query();
            $data = $stmt->fetchAll();
            
            if(!empty($data)) {
                return (array)$data[0];
            } 
        }
    }
    
    /**
     * Get parents.
     * 
     * return array with parents
     * @param string $parentType - type of parents
     * @param string $keyPrefix - prefix of key
     * @return array parents
     */
    private function _getParents($parentType, $keyPrefix = null) {
        if($parentType == 'photos') {
            $select = Admin_Model_DbSelect_Categories::pureSelect();
            $select->columns(array('id','name_'.$this->_locale.' as name'))
                ->where('parent_type = ?',$parentType)->where('status > -1');
        }
        
        if(isset($select) && $select instanceof Zend_Db_Select) {
            $stmt = $select->query();
            $data = $stmt->fetchAll();
            
            if(is_array($data) && count($data) > 0) {
                $parents = array();
                foreach($data as $parent) {
                    $parents[$keyPrefix.$parent['id']] = $parent['name'];
                }
                
                return $parents;
            }
        }
        
        return array();
    }
    
    /**
     * Get parent types.
     * 
     * return array with types for parents
     * @return types for parents
     */
    private function _getParentTypes() {
        $types = array(
            'photos' => $this->view->trans('Photos categories'),
        );
        
        return $types;
    }
}