<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Sep 22, 2011
 */
  
class Default_Plugin_MenuInfo extends Zend_Controller_Plugin_Abstract {
    
    /*
     * Found (Private).
     * 
     * holds information if we have found something.
     */
    private $_found = false;
    
    /*
     * Locale (Private).
     * 
     * holds information about actual locale
     */
    private $_locale = null;
    
    /**
     * Construct.
     * 
     * constructor of the class
     */
    public function __construct() {
         $this->_locale = Zend_Registry::get('PTX_Locale');
    }
    
    /**
     * Pre Dispatch.
     * 
     * pre dispatch
     * @see Zend/Controller/Plugin/Zend_Controller_Plugin_Abstract::preDispatch()
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        // Variables.
        $requestParams = $request->getParams();
        
        // Default module.
        if($requestParams['module'] == 'default') {
            if($requestParams['controller'] == 'index') {
                if($requestParams['action'] == 'index') {
                    $this->_findData('homepage');
                }
            } else if($requestParams['controller'] == 'static-page') {
                if($requestParams['action'] == 'detail' || $requestParams['action'] == 'preview' || $requestParams['action'] == 'revision') {
                    if(isset($requestParams['url'])) {
                        $pageObj = $this->_getStaticPageObj($requestParams['url']);
                    } else if(isset($requestParams['id'])) {
                        $pageObj = new Admin_Model_StaticPage($requestParams['id']);
                    }
                    
                    if($pageObj instanceof Default_Model_StaticPage) {
                        $pageData = $pageObj->getData(false,true);
                        $this->_findData('staticpage',$pageData['id']);
                    }
                }
            } else if($requestParams['controller'] == 'category') {
                if($requestParams['action'] == 'detail-photo') {
                    if(isset($requestParams['url'])) {
                        $categoryObj = $this->_getCategoryObj($requestParams['url']);
                    } else if(isset($requestParams['id'])) {
                        $categoryObj = new Default_Model_Category($requestParams['id']);
                    }
                    
                    $categoryData = $categoryObj->getData(false,true);
                    $this->_findData('category',$categoryData['id']);
                    
                    if(!$this->_found) {
                        if($categoryData->parent_type == 'photos') {
                            $this->_findData('photogallery',null);
                        }
                    }
                }
            }
        } else if($requestParams['module'] == 'photo') {
            if($requestParams['controller'] == 'index') {
                if($requestParams['action'] == 'index') {
                    $this->_findData('photogallery');
                }
            }
        } else if($requestParams['module'] == 'article') {
            if($requestParams['controller'] == 'article') {
                if($requestParams['action'] == 'detail' || $requestParams['action'] == 'preview' || $requestParams['action'] == 'revision') {
                    if(isset($requestParams['url'])) {
                        $articleObj = $this->_getArticleObj($requestParams['url']);
                    } else if(isset($requestParams['id'])) {
                        $articleObj = new Article_Model_Article($requestParams['id']);
                    }
                    
                    if($articleObj instanceof Article_Model_Article) {
                        $articleData = $articleObj->getData(false,true);
                        $this->_findData('article',$articleData['id']);
                    }
                    
                    if(!$this->_found) {
                        // Categories.
                        $categoriesIds = Admin_Model_DbSelect_CategoryRelations::get4Parent('articles',$articleData['id']," = 1");
                        foreach($categoriesIds as $key => $id) {
                            $this->_findData('category',$id);
                            if($this->_found == true) {
                                break;
                            }
                        }
                    }
                }
            }
        }
        
        if($this->_found == false) {
            $this->_findByUrl($_SERVER['REQUEST_URI']);
        }
    }
    
    /**
     * Get article obj (Private).
     * 
     * return instace of article class according to url
     * @param $url - url 
     * @return article obj
     */
    private function _getArticleObj($url) {
        $hash = md5(trim($url));
        $articlesObj = new Article_Model_Articles();
        $articleData = $articlesObj->findByUrlHash($hash," = 1");

        if(isset($articleData['id'])) {
            return new Article_Model_Article($articleData['id']);
        } else {
            $select = Default_Model_DbSelect_UrlBackups::pureSelect();
            $select->columns(array('DISTINCT(parent_id)'))->where("parent_type = 'articles'")->where('url_hash = ?',$hash)->order('id desc');
            $stmt = $select->query();
            $data = $stmt->fetchAll();
            
            if(is_array($data) && count($data) > 0) {
                foreach($data as $row) {
                    $articleObj = new Article_Model_Article($row['parent_id']);
                    $articleData = $articleObj->getData(false,true);
                }
            }
        }
    }
    
    /** 
     * Get category obj (Private).
     * 
     * return instace of category class according to url
     * @param string $url - url 
     * @return object
     */
    private function _getCategoryObj($url) {
        $hash = md5(trim($url));
        $catsObj = new Default_Model_Categories();
        $categoryData = $catsObj->findByHash($hash," = 1");

        if(isset($categoryData['id'])) {
            return new Default_Model_Category($categoryData['id']);
        } else {
            $select = Default_Model_DbSelect_UrlBackups::pureSelect();
            $select->columns(array('DISTINCT(parent_id)'))->where("parent_type = 'categories'")->where('url_hash_'.$this->_locale.' = ?',$hash)->order('id desc');
            $stmt = $select->query();
            $data = $stmt->fetchAll();
            
            if(is_array($data) && count($data) > 0) {
                foreach($data as $row) {
                    $catObj = new Default_Model_Category($row['parent_id']);
                    $categoryData = $catObj->getData(false,true);
                }
            }
        }
    }
    
    /**
     * Get static page obj (Private).
     * 
     * return instace of static page class according to url
     * @param $url - url 
     * @return static page obj
     */
    private function _getStaticPageObj($url) {
        $hash = md5(trim($url));
        $pagesObj = new Default_Model_StaticPages();
        $pageData = $pagesObj->findByUrlHash($hash," = 1");

        if(isset($pageData['id'])) {
            return new Default_Model_StaticPage($pageData['id']);
        } else {
            $select = Default_Model_DbSelect_UrlBackups::pureSelect();
            $select->columns(array('DISTINCT(parent_id)'))->where("parent_type = 'pages'")->where('url_hash = ?',$hash)->order('id desc');
            $stmt = $select->query();
            $data = $stmt->fetchAll();
            
            if(is_array($data) && count($data) > 0) {
                foreach($data as $row) {
                    $pageObj = new Default_Model_StaticPage($row['parent_id']);
                    $pageData = $pageObj->getData(false,true);
                }
            }
        }
    }
    
    /**
     * Find by Url (Private).
     * 
     * tries to find data by url
     * @param string $url - url
     */
    private function _findByUrl($url) {
        // Find default data.
        $select = Default_Model_DbSelect_MenuInputs::pureSelect();
        $select->columns(array('id','menu_place_id'))->where('parent_type = ?','link')->where('status = 1')->where('link_'.$this->_locale.' = ?',$url);
        $stmt = $select->query();
        $data = $stmt->fetchAll();
        
        // Group menus.
        $menus = array();
        foreach($data as $key => $values) {
            if(!isset($menus[$values['menu_place_id']])) {
                $menus[$values['menu_place_id']] = array();
            } 
            
            // Find path.
            $treeObj = new Default_Model_Tree_Menu();
            $path = $treeObj->getPath($values['id']);
            $cPath = count($path);
            
            $menus[$values['menu_place_id']][$values['id']] = array('level' => $cPath,'path'=>array());
            for($i = $cPath-1; $i >= 0; $i--) {
                $menus[$values['menu_place_id']][$values['id']]['path'][] = $path[$i];
            }
        }
        
        if(!empty($menus)) {
           // Store it into session.
           $session = new Zend_Session_Namespace('menu');
           $session->active = $menus;
           $this->_found = true;
        } 
    }
    
    /**
     * Find data.
     * 
     * finds data according to parent type and id
     * @param string  $parentType - type of the parent
     * @param integer $parentId - id of the parent
     * @return array with data.
     */
    private function _findData($parentType, $parentId = null) {
        // Find default data.
        $select = Default_Model_DbSelect_MenuInputs::pureSelect();
        $select->columns(array('id','menu_place_id'))->where('parent_type = ?',$parentType)->where('status = 1');
        if(!empty($parentId)) {
            $select->where('input_id = ?',(int)$parentId);
        }
        $stmt = $select->query();
        $data = $stmt->fetchAll();
        
        // Group menus.
        $menus = array();
        foreach($data as $key => $values) {
            if(!isset($menus[$values['menu_place_id']])) {
                $menus[$values['menu_place_id']] = array();
            } 
            
            // Find path.
            $treeObj = new Default_Model_Tree_Menu();
            $path = $treeObj->getPath($values['id']);
            $cPath = count($path);
             
            
            $menus[$values['menu_place_id']][$values['id']] = array('level' => $cPath,'path'=>array());
            for($i = $cPath-1; $i >= 0; $i--) {
                $menus[$values['menu_place_id']][$values['id']]['path'][] = $path[$i];
            }
        }
        
        if(!empty($menus)) {
           // Store it into session.
           $session = new Zend_Session_Namespace('menu');
           $session->active = $menus;
           $this->_found = true;
        } 
    }
}
