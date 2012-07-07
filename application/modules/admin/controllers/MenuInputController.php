<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 7.9.2010
**/

class Admin_MenuInputController extends Zend_Controller_Action {    
    
    /************** PUBLIC FUNCTION **************/
    /**
     * Init.
     */
    public function init() {
        $this->view->h1 = $this->view->trans('Menu inputs');
        
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
        // Data.
        $place = $this->_getParam('place',null);
        $type = $this->_getParam('type',null);
        $parent = $this->_getParam('parent',null);
        
        // Verification.
        if(empty($place)) {
            PTX_Message::setMessage4User('admin', 'warning', $this->view->trans('You must select menu place first.'));
            $this->_redirect('/admin/menu-input/list');
        } else if(empty($type)) {
            $this->_redirect('/admin/menu-input/add-select/place/'.(int)$place);
        }
        
        // Form.
        $params = array(
            'parents' => $this->_getTree($place),
            'help_type' => $parent,
            'place'   => $place,
            'type'    => $type);
        $form = $this->_getRightForm($type,$params);
        $this->view->form = $this->_addForm($this->getRequest(),$form,$params);

        // View variables.
        $this->view->place = $place;
        $this->view->parent = $parent;
        $this->view->type = $type;
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        $this->view->fastJump = $this->_getFastJump($place);
        
        // Page setting.
        $this->view->h2 = $this->view->trans('New menu input');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Delete.
     */
    public function deleteAction() {
        $idMenu = $this->_getParam('id');
        
        $inputObj = new Admin_Model_MenuInput($idMenu);
        $inputData = $inputObj->getData(false,true);
        
        if(!isset($inputData['status']) || $inputData['status'] < 0) {
            $this->_redirect('/admin/index');
        } 
        
        $rows = $inputObj->delete();
        
        if($rows > 0) {
            PTX_Message::setMessage4User('admin','done',$this->view->transParam('Menu input <b>~0~</b> has been deleted.',array('~0~'=>$inputData['name_'.$this->_locale]),false));
        } else {
            PTX_Message::setMessage4User('admin','warning',$this->view->transParam('Menu input <b>~0~</b> could not be deleted.',array('~0~'=>$inputData['name_'.$this->_locale]),false));
        }
        $this->_redirect(PTX_Anchor::getAnchor('admin'));
    }
    
    /**
     * editace profilu
     */
    public function editAction() {
        // Data.
        $idMenu = $this->_getParam('id');
        $inputObj = new Admin_Model_MenuInput($idMenu);
        $inputData = $inputObj->getData(false,true);
        
        if(!isset($inputData['status']) || $inputData['status'] < 0) {
            $this->_redirect('/admin/index');
        } 
        
        // Form.
        $params = array(
            'help_type'   => $inputData['help_type'],
            'parent_type' => $inputData['parent_type'],
            'parents'     => $this->_getTree($inputData['menu_place_id'])
        );
        $form = $this->_getRightForm($inputData['parent_type'],$params);
        $this->view->form = $this->_editForm($this->getRequest(),$form,$inputObj);
        
        // View variables.
        $this->view->data = $inputData;
        $this->view->place = $inputData['menu_place_id'];
        $this->view->type = $inputData['parent_type'];
        $this->view->parent = $inputData['help_type'];
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('Edit_2');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Index.
     */
    public function indexAction() {
        $places = $this->_getMenuPlaces('list');
        $keys = array_keys($places);
        if(isset($keys[0])) {
            $this->_redirect('/admin/menu-input/list/place/'.$keys[0]);
        } 
        
        $this->view->h2 = $this->view->trans('Places are missing');
    }
    
    /**
     * List.     
     */
    public function listAction() {
        // Parameters.
        $params = array('place'   => $this->_getParam('place',null));
        if(empty($params['place'])) {
            $this->_redirect('/admin/menu-input');
        } 
        
        // Zend_Db_Select.
        $treeObj = new Admin_Model_Tree_Menu();
        $treeData = $treeObj->getTree(0,0,' > -1',array('place_id'=>$params['place']));
        foreach($treeData as &$row) {
            $row['name'] = $row['name_'.$this->_locale];
        }
        $this->view->data = $treeData;
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        $this->view->params = $params;
        $this->view->menuPlaces = $this->_getMenuPlaces('list');
        
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
        $inputObj = new Admin_Model_MenuInput($id);
        $inputObj->changePosition($way);
        
        $inputData = $inputObj->getData();
        $this->_redirect(PTX_Anchor::get('menu-input','list','admin'));
    }
    
    /**
     * Transform. 
     */
    public function transformAction() {
        $link = '/admin/menu-input/list';
        
        if($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            
            if(isset($data['place']) && !empty($data['place'])) {
                $link .= '/place/'.urlencode(trim($data['place']));
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
    /************** PRIVATE FUNCTION **************/
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
    private function _addForm(Zend_Controller_Request_Http $request, Zend_Form $form, $params) {
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                $formData = $form->getValues();
                $inputsObj = new Admin_Model_MenuInputs();
                $idInput = $inputsObj->save($formData,$params);                
                
                PTX_Message::setMessage4User('admin','done',$this->view->transParam('Menu input <b>~0~</b> has been stored.',array('~0~'=>$formData['name'.$this->_locale]),false));
                if($formData['continue'] == 0) {
                    $this->_redirect('/admin/menu-input/list/place/'.(int)$place);
                } else {
                    $form->populate($formData);    
                }
            }
        } else {
            $form->populate(array('place'=>$params['place']));
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
        // Place.
        if(!empty($params['place'])) {
            $select->where('menu_place_id = ?',(int)$params['place']);
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
     * @param Admin_Model_MenuInput inputObj
     * @return Zend_Form
     */
    private function _editForm(Zend_Controller_Request_Http $request, Zend_Form $form, Admin_Model_MenuInput $inputObj) {
        $data = $inputObj->getData(false,true);
        
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                $formData = $form->getValues();
                $formData['place'] = (int)$data['menu_place_id'];
                $inputObj->update($formData);
                
                PTX_Message::setMessage4User('admin','done',$this->view->transParam('Menu input <b>~0~</b> has been updated.',array('~0~'=>$formData['name'.$this->_locale]),false));
                $this->_redirect(PTX_Anchor::getAnchor('admin'));
            }
                
        } else {
            $populate = array(
                'place'     => $data['menu_place_id'],
                'target'    => $data['target'],
                'status'    => $data['status'],
                'input'     => $data['input_id'],
            );
            
            $transData = Admin_Model_AppModel::bindTranslations($data, array('name','title','link'));
            $populate = array_merge($populate,$transData);
            
            $form->populate($populate);
        }
        
        return $form;
    }
    
   /**
     * Get Fast Jump (Private).
     * 
     * return setting for fast jump.
     * @param integer $idPlace
     * @return array with data for fast jump
     */
    private function _getFastJump($idPlace) {
        $fastJump = array();
        $options = $this->_getMenuOptions($idPlace);
        foreach($options as $key => $values) {
            $fastJump[$key.'_'.$idPlace] = $values['name'];
        }
        
        return (array)$fastJump;
    }
    
    /**
     * Get menu options.
     * 
     * returns menu inputs which can be set up
     * @param $idPlace - ID of menu place
     * @return menu inputs options
     */
    private function _getMenuOptions($idPlace) {
        // Menu input settings.
        $buttonsSetting = array(
           'homepage' => array('name'=>$this->view->trans('Homepage Link'),'link'=>'/admin/menu-input/add/type/homepage/place/'.(int)$idPlace,'description'=>$this->view->trans('Create menu input for homepage')),
           'staticpage' => array('name'=>$this->view->trans('Static page'),'link'=>'/admin/menu-input/add/type/staticpage/place/'.(int)$idPlace,'description'=>$this->view->trans('Create menu input for static page')),
           'article' => array('name'=>$this->view->trans('Article'),'link'=>'/admin/menu-input/add/type/article/place/'.(int)$idPlace,'description'=>$this->view->trans('Create menu input for article')),
           'category_articles' => array('name'=>$this->view->trans('Article category'),'link'=>'/admin/menu-input/add/type/category/place/'.(int)$idPlace.'/parent/articles','description'=>$this->view->trans('Create menu input for article category')),
           'photogallery' => array('name'=>$this->view->trans('Photogallery'),'link'=>'/admin/menu-input/add/type/photogallery/place/'.(int)$idPlace.'','description'=>$this->view->trans('Create menu input for homepage of photogallery')),
           'category_photos' => array('name'=>$this->view->trans('Photo category'),'link'=>'/admin/menu-input/add/type/category/place/'.(int)$idPlace.'/parent/photos','description'=>$this->view->trans('Create menu input for photo category')),
           'link' => array('name'=>$this->view->trans('External link'),'link'=>'/admin/menu-input/add/type/link/place/'.(int)$idPlace,'description'=>$this->view->trans('Create menu input for external link')),
        );    
        
        // Return options.
        return $buttonsSetting;
    }
    
    /**
     * Get Tree.
     * 
     * return tree for menu
     * @param integer $placeId - id of place for menu
     * @return tree
     */
    private function _getTree($placeId){
        $treeObj = new Admin_Model_Tree_Menu();
        return $treeObj->getTree(0,0," > -1",array('place_id'=>$placeId));
    }  
    
    /**
     * Get menu places.
     * 
     * return array with menu places
     * @return manu places
     */
    private function _getMenuPlaces($return = 'array') {
        $select = Admin_Model_DbSelect_MenuPlaces::pureSelect();
        $select->columns(array('id','name_'.$this->_locale.' as name'))->where('status = 1')->order('name asc');
        $stmt = $select->query();
        $data = $stmt->fetchAll();
        
        if($return == 'array') {
            return (array)$data;
        } else if($return == 'list') {
            $places4View = array();
            if(is_array($data)) {
                foreach($data as $row) {
                    $places4View[$row['id']] = $row['name'];
                }
            }
            return (array)$places4View;
        }
    }
    
    /**
     * Get right form.
     * 
     * return instace of right form class
     * @param $type - type of menu input to be saved
     * @param $params - options
     * @return Zend_Form
     */
    private function _getRightForm($type,array $params) {
        switch($type) {
            case 'article':
                return new Admin_Form_MenuArticle($params);
            case 'category':
                return new Admin_Form_MenuCategory($params);
            case 'homepage':
                return new Admin_Form_MenuHomepage($params);
            case 'link':
                return new Admin_Form_MenuLink($params);
            case 'photogallery':
                return new Admin_Form_MenuPhotogallery($params);
            case 'staticpage':
                return new Admin_Form_MenuStaticPage($params);
        }   
    }
}