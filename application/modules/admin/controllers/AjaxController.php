<?php

class Admin_AjaxController extends Zend_Controller_Action {
    /************** PREDISPATCH **************/
    public function preDispatch() {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        
        $this->_locale = Zend_Registry::get('PTX_Locale');
        $this->view->locale = $this->_locale;
    }
    
    /**
     * Add to category
     * 
     * Adds record to category.
     * @return JSON
     */
    public function addToCategoryAction() {
        $params = array(
            'category_id' => $this->_getParam('category'),
            'parent_id'   => $this->_getParam('input'),
            'parent_type' => $this->_getParam('type'));
        
        $categoryRelationsObj = new Admin_Model_CategoryRelations();
        $inputId = $categoryRelationsObj->save($params);
        
        if(!empty($inputId)) {
            $categoryObj = new Admin_Model_Category($params['category_id']);
            $categoryData = $categoryObj->getData(false,true);
            
            $params['id'] = $inputId;
            $params['category'] = $categoryData['name_'.$this->_locale];
            echo Zend_Json::encode($params);
        }
    }
    
    /**
     * Admin menu status.
     * 
     * changes status for menu input
     * @return array('result'=>{new_status},'children'=>array({id_child},{id_child}....) (JSON)
     */
    public function adminMenuStatusAction() {
        $idMenu = $this->_getParam('id');
        
        $menuObj = new Admin_Model_AdminMenu($idMenu);
        $newValue = $menuObj->changeStatus();
        
        if($newValue == 0) {
            $treeObj = new Admin_Model_Tree_AdminMenu();
            $treeData = $treeObj->getTree($idMenu,0," = 1");
            
            foreach($treeData as $row) {
                $catObj = new Admin_Model_AdminMenu($row['id']);
                $catObj->changeStatus();
            }
            
            $array = array('result'=>(int)$newValue,'children'=>PTX_Page::getIdsArray($treeData,'id'));
        } else {
            $array = array('result'=>(int)$newValue,'children'=>array());    
        }
        
        echo Zend_Json::encode($array);
    }
    
    /**
     * Article Picture.
     * 
     * updates data about picture for article.
     * @return (JSON) 
     */
    public function articlePictureAction() {
        $id = $this->_getParam('id',null);
        
        if(!empty($id)) {
            $articleObj = new Admin_Model_Article($id);
            $articleObj->updateFeaturedPicture($_POST);
            
            $return = array('result'=>1);
            echo Zend_Json::encode($return);
        }        
    }
    
    /**
     * Article status.
     * 
     * changes status for article
     * @return new status (JSON) 
     */
    public function articleStatusAction() {
        $id = $this->_getParam('id');
        
        $pageObj = new Admin_Model_Article($id);
        $newValue = $pageObj->changeStatus();
            
        $array = array('result'=>(int)$newValue);    
        echo Zend_Json::encode($array);
    }
    
    /**
     * Banner status.
     * 
     * changes status for banner
     * @return new status (JSON) 
     */
    public function bannerStatusAction() {
        $id = $this->_getParam('id');
        
        $bannerObj = new Admin_Model_Banner($id);
        $newValue = $bannerObj->changeStatus();
            
        $array = array('result'=>(int)$newValue);    
        echo Zend_Json::encode($array);
    }
    
    /**
     * Category Picture.
     * 
     * updates data about picture for category.
     * @return (JSON) 
     */
    public function categoryPictureAction() {
        $id = $this->_getParam('id',null);
        
        if(!empty($id)) {
            $categoryObj = new Admin_Model_Category($id);
            $categoryObj->updateFeaturedPicture($_POST);
            
            $return = array('result'=>1);
            echo Zend_Json::encode($return);
        }        
    }
    
    /**
     * Category status.
     * 
     * changes status for category
     * @return array('result'=>{new_status},'children'=>array({id_child},{id_child}....) (JSON)
     */
    public function categoryStatusAction() {
        $idCategory = $this->_getParam('id');
        
        $catObj = new Admin_Model_Category($idCategory);
        $newValue = $catObj->changeStatus();
        
        if($newValue == 0) {
            $treeObj = new Admin_Model_Tree_Category();
            $treeData = $treeObj->getTree($idCategory,0," = 1");
            
            foreach($treeData as $row) {
                $catObj = new Admin_Model_Category($row['id']);
                $catObj->changeStatus();
            }
            
            $array = array('result'=>(int)$newValue,'children'=>PTX_Page::getIdsArray($treeData,'id'));
        } else {
            $array = array('result'=>(int)$newValue,'children'=>array());    
        }
        
        echo Zend_Json::encode($array);
    }
    
    /**
     * Comment status.
     * 
     * changes status for comment
     * @return new status (JSON) 
     */
    public function commentStatusAction() {
        $id = $this->_getParam('id');
        
        $pageObj = new Admin_Model_Comment($id);
        $newValue = $pageObj->changeStatus();
            
        $array = array('result'=>(int)$newValue);    
        echo Zend_Json::encode($array);
    }
    
    /**
     * Fast jump. 
     */
    public function fastJumpAction() {
        $link = '/admin';
        
        if($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            
            if(isset($data['controller']) && !empty($data['controller'])) {
                $link .= '/'.urlencode(trim($data['controller']));
            
                if($data['controller'] == 'setting') {
                    $link .= '/'.urlencode(trim($data['id']));
                    $this->_redirect($link);
                } else if($data['controller'] == 'menu-input') {
                    if(isset($data['action'])) {
                        if($data['action'] == 'add') {
                            $parseArray = explode('_',$data['id']);
                            $cParseArray = count($parseArray);
                            
                            if($cParseArray == 2) {
                                $link .= '/add/place/'.(int)$parseArray[1].'/type/'.$parseArray[0];
                                $this->_redirect($link);
                            } else if($cParseArray == 3) {
                                $link .= '/add/place/'.(int)$parseArray[2].'/type/'.$parseArray[0].'/parent/'.$parseArray[1];
                                $this->_redirect($link);
                            }
                        }
                    }
                } else if($data['controller'] == 'widget') {
                    if(isset($data['action'])) {
                        if($data['action'] == 'add') {
                            $parseArray = explode('_',$data['id']);
                            $cParseArray = count($parseArray);
                            
                            if($cParseArray == 2) {
                                $link .= '/add/place/'.(int)$parseArray[1].'/type/'.$parseArray[0];
                                $this->_redirect($link);
                            } else if($cParseArray == 3) {
                                $link .= '/add/place/'.(int)$parseArray[2].'/type/'.$parseArray[0].'/parent/'.$parseArray[1];
                                $this->_redirect($link);
                            }
                        }
                    }
                } else if($data['controller'] == 'photo') {
                    if(isset($data['action'])) {
                        if($data['action'] == 'organize') {
                            $parseArray = explode('_',$data['id']);
                            $cParseArray = count($parseArray);
                            
                            if($cParseArray == 2) {
                                $link .= '/organize/parent/'.(int)$parseArray[1].'/type/'.$parseArray[0];
                                $this->_redirect($link);
                            } 
                        }
                    }
                }
            } 
                
            if(isset($data['action']) && !empty($data['action'])) {
                $link .= '/'.urlencode(trim($data['action']));
            }
                
            if(isset($data['id']) && !empty($data['id'])) {
                $link .= '/id/'.(int)$data['id'];
            }
        }
        
        $this->_redirect($link);
    }
    
    /**
     * Language status.
     * 
     * changes status for language
     * @return new status (JSON) 
     */
    public function languageStatusAction() {
        $id = $this->_getParam('id');
        
        $languageObj = new Admin_Model_Language($id);
        $newValue = $languageObj->changeStatus();
            
        $array = array('result'=>(int)$newValue);    
        echo Zend_Json::encode($array);
    }
    
    /**
     * Location status.
     * 
     * changes status for location
     * @return array('result'=>{new_status},'children'=>array({id_child},{id_child}....) (JSON)
     */
    public function locationStatusAction() {
        $idLocation = $this->_getParam('id');
        
        $catObj = new Admin_Model_Location($idLocation);
        $newValue = $catObj->changeStatus();
        
        if($newValue == 0) {
            $treeObj = new Admin_Model_Tree_Location();
            $treeData = $treeObj->getTree($idLocation,0," = 1");
            
            foreach($treeData as $row) {
                $catObj = new Admin_Model_Location($row['id']);
                $catObj->changeStatus();
            }
            
            $array = array('result'=>(int)$newValue,'children'=>PTX_Page::getIdsArray($treeData,'id'));
        } else {
            $array = array('result'=>(int)$newValue,'children'=>array());    
        }
        
        echo Zend_Json::encode($array);
    }
    
   /**
     * Menu input status.
     * 
     * changes status for menu input
     * @return new status (JSON)
     */
    public function menuInputStatusAction() {
        $idInput = $this->_getParam('id');
        
        $inputObj = new Admin_Model_MenuInput($idInput);
        $newValue = $inputObj->changeStatus();
            
        $array = array('result'=>(int)$newValue);    
        echo Zend_Json::encode($array);
    }
    
    /**
     * Menu place status.
     * 
     * changes status for menu place
     * @return new status (JSON)
     */
    public function menuPlaceStatusAction() {
        $idPlace = $this->_getParam('id');
        
        $placeObj = new Admin_Model_MenuPlace($idPlace);
        $newValue = $placeObj->changeStatus();
            
        $array = array('result'=>(int)$newValue);    
        echo Zend_Json::encode($array);
    }
    
    /** 
     * Photo status.
     * 
     * changes status for photo
     * @return new status (JSON)
     */
    public function photoStatusAction() {
        $id = $this->_getParam('id');
        
        $photoObj = new Admin_Model_Photo($id);
        $newValue = $photoObj->changeStatus();
            
        $array = array('result'=>(int)$newValue);    
        echo Zend_Json::encode($array);
    }
    
    /**
     * Remove from category
     * 
     * Removes record from category.
     * @return JSON
     */
    public function removeFromCategoryAction() {
        $ident = $this->_getParam('ident');
        $id = (int)substr($ident,6);
        
        $categoryRelationObj = new Admin_Model_CategoryRelation($id);
        $categoryRelationObj->delete();
        
        $data = array('result'=>1);
        echo Zend_Json::encode($data);
    }
    
    /**
     * Replacer status.
     * 
     * changes status for replacer
     * @return new status (JSON)
     */
    public function replacerStatusAction() {
        $id = $this->_getParam('id');
        
        $replacerObj = new Admin_Model_Replacer($id);
        $newValue = $replacerObj->changeStatus();
            
        $array = array('result'=>(int)$newValue);    
        echo Zend_Json::encode($array);
    }
    
    /**
     * Save Order.
     * 
     * saves new order for picture.
     */
    public function saveOrderAction() {
        $params = array(
            'parent_id'   => $this->_getParam('parent',null),
            'parent_type' => $this->_getParam('type',null));
        
        if(empty($params['parent_type']) || empty($params['parent_id'])) {
            $result = $this->view->trans('Missing data');
        } else if(!isset($_GET['photo']) || empty($_GET['photo'])) {
            $result = $this->view->trans('Missing data');
        } else {
            $photosObj = new Admin_Model_Photos();
            $photosObj->updatePositions($params['parent_type'],$params['parent_id'],$_GET['photo']);
            $result = 1;
        }
        
        $array = array('result'=>$result);    
        echo Zend_Json::encode($array);
    }
    
    /**
     * Section status.
     * 
     * changes status for section
     * @return new status (JSON)
     */
    public function sectionStatusAction() {
        $id = $this->_getParam('id');
        
        $sectionObj = new Admin_Model_Section($id);
        $newValue = $sectionObj->changeStatus();
            
        $array = array('result'=>(int)$newValue);    
        echo Zend_Json::encode($array);
    }
    
    /**
     * Static Page Picture.
     * 
     * updates data about picture for static page.
     * @return (JSON) 
     */
    public function staticPagePictureAction() {
        $id = $this->_getParam('id',null);
        
        if(!empty($id)) {
            $staticPageObj = new Admin_Model_StaticPage($id);
            $staticPageObj->updateFeaturedPicture($_POST);
            
            $return = array('result'=>1);
            echo Zend_Json::encode($return);
        }        
    }
    
    /**
     * Static page status.
     * 
     * changes status for static place
     * @return new status (JSON)
     */
    public function staticPageStatusAction() {
        $id = $this->_getParam('id');
        
        $pageObj = new Admin_Model_StaticPage($id);
        $newValue = $pageObj->changeStatus();
            
        $array = array('result'=>(int)$newValue);    
        echo Zend_Json::encode($array);
    }
    
    /**
     * Tag Relation Add.
     */
    public function tagRelationAddAction() {
        $formData = array(
            'tagid'      => $this->_getParam('tag'),
            'parentid'   => $this->_getParam('input'),
            'parenttype' => $this->_getParam('type'));

        $tagRelationsObj = new Admin_Model_TagRelations();
        $inputId = $tagRelationsObj->save($formData);

        if(!empty($inputId)) {
            $tagObj = new Admin_Model_Tag($formData['tagid']);
            $tagData = $tagObj->getData(false,true);

            $formData['id'] = $inputId;
            $formData['tag'] = $tagData['name_'.$this->_locale];
            echo Zend_Json::encode($formData);
        }
    }
    
    /**
     * Tag Relation Delete.
     */
    public function tagRelationDeleteAction() {
        $ident = $this->_getParam('ident');
        $id = (int)substr($ident,6);
        
        $tagRelationObj = new Admin_Model_TagRelation($id);
        $tagRelationObj->delete();
        
        $data = array('result'=>1);
        echo Zend_Json::encode($data);
    }
    
    /**
     * Tag status.
     * 
     * changes status for tag
     * @return new status (JSON)
     */
    public function tagStatusAction() {
        $id = $this->_getParam('id');
        
        $tagObj = new Admin_Model_Tag($id);
        $newValue = $tagObj->changeStatus();
        
        $array = array('result'=>(int)$newValue);
        echo Zend_Json::encode($array);        
    }
    
    /**
     * User status.
     * 
     * changes status for user
     * @return new status (JSON)
     */
    public function userStatusAction() {
        $idUser = $this->_getParam('id');
        
        $userObj = new Admin_Model_User($idUser);
        $newValue = $userObj->changeStatus();
        
        $array = array('result'=>(int)$newValue);
        echo Zend_Json::encode($array);        
    }
    
    /**
     * Widget place status.
     * 
     * changes status for widget place
     * @return new status (JSON)
     */
    public function widgetPlaceStatusAction() {
        $idPlace = $this->_getParam('id');
        
        $placeObj = new Admin_Model_WidgetPlace($idPlace);
        $newValue = $placeObj->changeStatus();
            
        $array = array('result'=>(int)$newValue);    
        echo Zend_Json::encode($array);
    }
    
    /**
     * Widget status.
     * 
     * changes status for widget
     * @return new status (JSON)
     */
    public function widgetStatusAction() {
        $id = $this->_getParam('id');
        
        $userObj = new Admin_Model_Widget($id);
        $newValue = $userObj->changeStatus();
        
        $array = array('result'=>(int)$newValue);
        echo Zend_Json::encode($array);        
    }
}