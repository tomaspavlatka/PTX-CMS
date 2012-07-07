<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 10.8.2010
**/

class Admin_WidgetController extends Zend_Controller_Action {    
    
    /************** PUBLIC FUNCTION **************/
    private $_user;
    
    /************** PUBLIC FUNCTION **************/
    /**
     * init
     */
    public function init() {
        $this->view->h1 = $this->view->trans('Widgets');
        
        $response = $this->getResponse();
        $response->insert('menuTop',$this->view->render('_menu/top.phtml'));
        $response->insert('incTop',$this->view->render('_inc/top-row.phtml'));
        $response->insert('incLeft',$this->view->render('_inc/left.phtml'));
        
        $this->_user = Zend_Auth::getInstance()->getStorage()->read();
        
        // Anchor 2
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
        
        // First verification.
        if(empty($place)) {
            PTX_Message::setMessage4User('admin', 'warning', $this->view->trans('You must select widget place first.'));
            $this->_redirect('/admin/widget/list');
        } else if(empty($type)) {
            $this->_redirect('/admin/widget/add-select/place/'.(int)$place);
        }
        
        // Widget place.
        $placeObj = new Admin_Model_WidgetPlace($place);
        $placeData = $placeObj->getData(false,true);
        
        // Form.
        $params = array('parent_type' => $type,'place' => $place);
        $form = $this->_getRightForm($type,$params);
        $this->view->form = $this->_addForm($this->getRequest(),$form,$params);
        
        // View variables.
        $this->view->place = $place;
        $this->view->type = $type;
        $this->view->fastJump = $this->_getFastJump($placeData['parent_type'],$place);
        
        // Page setting/
        $this->view->h2 = $this->view->trans('New Widget');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * List of widget types.
     */
    public function addSelectAction() {
        // Data.
        $place = $this->_getParam('place',null);
        
        // First verification.
        if(empty($place)) {
            PTX_Message::setMessage4User('admin', 'warning', $this->view->trans('You must select widget place first.'));
            $this->_redirect('/admin/widget/list');
        } 
        
        // Widget place.
        $placeObj = new Admin_Model_WidgetPlace($place);
        $placeData = $placeObj->getData(false,true);
        
        // Verification.
        if(!isset($placeData['status']) || $placeData['status'] < 0) {
            $this->_redirect('/admin/widget/list');
        }
        
        // Widget options.
        $this->view->widgetOptions = $this->_getWidgetOptions($placeData['parent_type'],$place);
        
        // View variables.
        $this->view->data = $placeData;
        $this->view->place = $this->_getParam('place',null);
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('Select widget type');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Delete.
     */
    public function deleteAction() {
        // Data.
        $idWidget = $this->_getParam('id');
        $widgetObj = new Admin_Model_Widget($idWidget);
        $widgetData = $widgetObj->getData(false,true);
        
        // Verification.
        if(!isset($widgetData['status']) || $widgetData['status'] < 0) {
            $this->_redirect('/admin/index');
        } 
        
        // Widget place.
        $placeObj = new Admin_Model_WidgetPlace($widgetData['widget_place_id']);
        $placeData = $placeObj->getData(false,true);
        
        if(!isset($placeData['status']) || $placeData['status'] < 0) {
            $this->_redirect('/admin/index');
        } else if($placeData['parent_type'] == 'admin' && $this->_user->role != 'admins' && $this->_user->id != $widgetData['user_id']) {
            $this->_redirect('/admin/index');
        }
        
        // Delete.
        $rows = $widgetObj->delete();
        
        // Message + redirect.
        if($rows > 0) {
            PTX_Message::setMessage4User('admin','done',$this->view->transParam('Widget <b>~0~</b> has been deleted.',array('~0~'=>$widgetData['name_'.$this->_locale]),false));
        } else {
            PTX_Message::setMessage4User('admin','warning',$this->view->transParam('Widget <b>~0~</b> could not be deleted.',array('~0~'=>$widgetData['name_'.$this->_locale]),false));
        }
        $this->_redirect(PTX_Anchor::getAnchor('admin'));
    }
    
    /**
     * Edit.
     */
    public function editAction() {
        // Data.
        $idWidget = $this->_getParam('id');
        $widgetObj = new Admin_Model_Widget($idWidget);
        $widgetData = $widgetObj->getData(false,true);
        
        // Verification.
        if(!isset($widgetData['status']) || $widgetData['status'] < 0) {
            $this->_redirect('/admin/index');
        } 
        
        // Widget place.
        $placeObj = new Admin_Model_WidgetPlace($widgetData['widget_place_id']);
        $placeData = $placeObj->getData(false,true);
        
        if(!isset($placeData['status']) || $placeData['status'] < 0) {
            $this->_redirect('/admin/index');
        } else if($placeData['parent_type'] == 'admin' && $this->_user->role != 'admins' && $this->_user->id != $widgetData['user_id']) {
            $this->_redirect('/admin/index');
        }
        
        // Form.
        $params = array('type' => $widgetData['parent_type'],'place' => $widgetData['widget_place_id']);
        $form = $this->_getRightForm($widgetData['parent_type'],$params);
        $this->view->form = $this->_editForm($this->getRequest(),$form,$widgetObj);
        
        // View variables.
        $this->view->data = $widgetData;
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('Edit_2');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Index.
     */
    public function indexAction() {
        $places = $this->_getWidgetPlaces('list');
        $keys = array_keys($places);
        if(isset($keys[0])) {
            $this->_redirect('/admin/widget/list/place/'.$keys[0]);
        }
        
        $this->view->h2 = $this->view->trans('Places are missing');
    }
    
    /**
     * List.     
     */
    public function listAction() {
        // Parameters.
        $params = array(
            'place'   => $this->_getParam('place',null),
            'input'  => $this->_getParam('input',25),
            'search' => $this->_getParam('search',null));
        
        if(empty($params['place'])) {
            $this->_redirect('/admin/widget');
        }
        
        // Zend_Db_Select.
        $select = Admin_Model_DbSelect_Widgets::pureSelect();
        $select->columns(array('id','parent_type','name_'.$this->_locale.' as name','status'))->where('status > -1');
        $select->order('position asc');
        $this->_completeSelectList($select,$params);

        // Admin place = user restrictrion.
        $places4Select = $this->_getWidgetPlaces('list_type');
        if(isset($places4Select[$params['place']]) && $places4Select[$params['place']] == 'admin') {
            $select->where('user_id = ?',(int)$this->_user->id);
        }

        // Paginator.
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setItemCountPerPage($params['input'])->setCurrentPageNumber($this->_getParam('page',1));
        $this->view->paginator = $paginator;
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        $this->view->params = $params;
        $this->view->widgetPlaces = $this->_getWidgetPlaces('list');
        
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
        $widgetObj = new Admin_Model_Widget($id);
        $widgetObj->changePosition($way);
        
        // Redirect.
        $this->_redirect(PTX_Anchor::getAnchor('admin'));
    }
    
    /**
     * Transform. 
     */
    public function transformAction() {
        $link = '/admin/widget/list';
        
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
     * @param array $params - additional params
     * @return Zend_Form
     */
    private function _addForm(Zend_Controller_Request_Http $request, Zend_Form $form, array $params) {
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                $formData = $form->getValues();
                
                $widgetsObj = new Admin_Model_Widgets();
                $params['user_id'] = $this->_user->id;
                $widgetsObj->save($formData,$params);                
                
                PTX_Message::setMessage4User('admin','done',$this->view->transParam('Widget <b>~0~</b> has been stored.',array('~0~'=>$formData['name'.$this->_locale]),false));
                $this->_redirect('/admin/widget/list/place/'.(int)$params['place']);
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
            $select->where('widget_place_id = ?',(int)$params['place']);
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
     * @param Admin_Model_Widget $widgetObj
     * @return Zend_Form
     */
    private function _editForm(Zend_Controller_Request_Http $request, Zend_Form $form, Admin_Model_Widget $widgetObj) {
        $data = $widgetObj->getData(false,true);
        
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                $formData = $form->getValues();
                $widgetObj->update($formData,$data);
                
                PTX_Message::setMessage4User('admin','done',$this->view->transParam('Widget <b>~0~</b> has been updated.',array('~0~'=>$formData['name'.$this->_locale]),false));
                $this->_redirect(PTX_Anchor::getAnchor('admin'));
            }
                
        } else {
            $params = PTX_Parser::parseWidgetParam($data['param']);
            $populate = array(
                'place'     => $data['widget_place_id'],
                'showname'  => $data['show_name'],
                'number'    => (isset($params['number'])) ? (int)$params['number'] : 5,
                'category'  => (isset($params['cats'])) ? explode("#",$params['cats']) : array(),
                'random'    => (isset($params['shuffle'])) ? (int)$params['shuffle'] : 0
            );
            
            $transData = Admin_Model_AppModel::bindTranslations($data, array('name','content'));
            $populate = array_merge($populate,$transData);
            
            foreach($populate as $key => $values) {
                if(strstr($key,'content')) {
                    $populate['w'.$key] = $values;
                    unset($populate[$key]);
                }
            }
            
            $form->populate($populate);
        }
        
        return $form;
    }
    
    /**
     * Get Fast Jump (Private).
     * 
     * return setting for fast jump.
     * @param string $type - type
     * @param integer $place - id of place
     * @return array with data for fast jump
     */
    private function _getFastJump($type,$place) {
        $fastJump = array();
        $options = $this->_getWidgetOptions($type,$place);        
        foreach($options as $key => $values) {
            $fastJump[$key.'_'.$place] = $values['name'];
        }
        
        return (array)$fastJump;
    }
    
    /**
     * Get right form.
     * 
     * return instance of right form class
     * @param $type - type
     * @param $options - options
     * @return Zend_Form
     */
    private function _getRightForm($type,$options) {
        switch($type) {
            case 'articlelast':
            case 'articlerandom':
            case 'articleshown':
                return new Admin_Form_WidgetArticle($options);
            case 'banner':
                return new Admin_Form_WidgetBanner($options);
            case 'separator':
                return new Admin_Form_WidgetSeparator($options);
            case 'shortcut':
                return new Admin_Form_WidgetShortcut($options);
            case 'text':
                return new Admin_Form_WidgetText($options);
            case 'twitter':
                return new Admin_Form_WidgetTwitter($options);
        }   
    }
    
    /**
     * Get widget options.
     * 
     * returns widgets which can be set up for specific widget place type
     * @param $type - widget place type
     * @param $place - ID of widget place
     * @return widgets options
     */
    private function _getWidgetOptions($type,$place) {
        // Widget settings.
        $buttonsSetting = array(
            'articlelast' => array('name'=>$this->view->trans('Article - Last'),'link'=>'/admin/widget/add/type/articlelast/place/'.(int)$place,'description'=>$this->view->trans('Last articles')),
            'articlerandom' => array('name'=>$this->view->trans('Article - Random'),'link'=>'/admin/widget/add/type/articlerandom/place/'.(int)$place,'description'=>$this->view->trans('Random articles')),
            'articleshown' => array('name'=>$this->view->trans('Article - Shown'),'link'=>'/admin/widget/add/type/articleshown/place/'.(int)$place,'description'=>$this->view->trans('Articles according to their popularity')),
            'shortcut' => array('name'=>$this->view->trans('Shortcut'),'link'=>'/admin/widget/add/type/shortcut/place/'.(int)$place,'description'=>$this->view->trans('Create shortcut for easier work')),
            'separator' => array('name'=>$this->view->trans('Separator'),'link'=>'/admin/widget/add/type/separator/place/'.(int)$place,'description'=>$this->view->trans('Separate your links with simple separator')),
            'text' => array('name'=>$this->view->trans('Text'),'link'=>'/admin/widget/add/type/text/place/'.(int)$place,'description'=>$this->view->trans('Add text / code widget')),
            'twitter' => array('name'=>$this->view->trans('Twitter'),'link'=>'/admin/widget/add/type/twitter/place/'.(int)$place,'description'=>$this->view->trans('Add user time line from twitter')),
            'banner' => array('name'=>$this->view->trans('Banners'),'link'=>'/admin/widget/add/type/banner/place/'.(int)$place,'description'=>$this->view->trans('Add banner section')),
        );    
        
        // Select which ones we want.
        switch($type) {
            case 'admin':
                $select = array('shortcut','text','separator');
                break;
            case 'frontend':
                $select = array('shortcut','text','articlelast','articlerandom','articleshown','banner','twitter');
                break;
        }   

        // Make options.
        $options = array();
        if(isset($select)) {
            foreach($select as $ident) {
                if(isset($buttonsSetting[$ident])) {
                    $options[$ident] = $buttonsSetting[$ident];
                }
            }
        }
        
        // Return options.
        return $options;
    }
    
    /**
     * Get places.
     * 
     * return array with widget places
     * @return widget places
     */
    private function _getWidgetPlaces($return = 'array') {
        $select = Admin_Model_DbSelect_WidgetPlaces::pureSelect();
        $select->columns(array('id','name_'.$this->_locale.' as name','parent_type'))->where('status = 1')->order('name asc');
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
            
        } else if($return == 'list_type') {
            $places4View = array();
            if(is_array($data)) {
                foreach($data as $row) {
                    $places4View[$row['id']] = $row['parent_type'];
                }
            }
            return (array)$places4View;
            
        }
    }
}