<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 18, 2010
**/

class TagController extends Zend_Controller_Action {

    /************* PUBLIC FUNCTION **************/
    public function init() {
        $response = $this->getResponse();
        
        // Widgets.
        $response->insert('widgetFooter',$this->view->render('_widget/footer.phtml'));
        $response->insert('widgetSidebar',$this->view->render('_widget/sidebar.phtml'));
        $response->insert('widgetTop',$this->view->render('_widget/top.phtml'));
        $response->insert('widgetUnder',$this->view->render('_widget/under.phtml'));
        
        // Menu
        $response->insert('menuHeader',$this->view->render('_menu/header.phtml'));
        $response->insert('menuMain',$this->view->render('_menu/main.phtml'));
        $response->insert('menuBottom',$this->view->render('_menu/bottom.phtml'));

        // projectconfig
        $this->_config = PTX_Session::getSessionValue('settings', 'project');
        $this->_locale = Zend_Registry::get('PTX_Locale');
        $this->view->locale = Zend_Registry::get('PTX_Locale');
        
        // including
        $response->insert('incStatistics',$this->view->render('_inc/statistics.phtml'));
    }

    /**
     * Detail.
     */
    public function detailAction() {
        // Data.
        $url = $this->_getParam('url');
        $parentType = $this->_getParam('parent_type');
        
        $tagObj = $this->_getTagObj($url,$parentType);
        if(!($tagObj instanceof Default_Model_Tag)) {
            throw new PTX_Exception(__CLASS__.": This tag is not for visitors [url:{$url}]");
        } else if(empty($parentType)) {
            throw new PTX_Exception(__CLASS__.": Parent type cannot be empty");
        }
        $tagData = $tagObj->getData(false,true);
        
        // Get Data.
        $relationObj = new Default_Model_TagRelations();
        $parentArray = $relationObj->get4Tag($tagData['id'],$parentType,'ids_array');
        
        $this->view->tagData = $tagData;
        
        if($parentType == 'articles') {
            $this->__articles($parentType,$parentArray,$tagData);
        }
        
        // Page settings.
        $this->view->headMeta()->appendName('robots','index,follow');
        $this->view->headMeta()->appendName('googlebot','snippet,archive');
    }
    
    /**
     * Get tag obj
     * 
     * return instace of tag class according to url
     * @param string $url - url 
     * @param string $parentType - type of parent 
     * @return tag obj
     */
    private function _getTagObj($url, $parentType) {
        $hash = md5(trim($url));
        $tagsObj = new Default_Model_Tags();
        $tagId = $tagsObj->findByUrlHash($hash," = 1");

        if(!empty($tagId)) {
            return new Default_Model_Tag($tagId);
        } else {
            $select = Default_Model_DbSelect_UrlBackups::pureSelect();
            $select->columns(array('DISTINCT(parent_id)'))->where("parent_type = 'tags'")->where('url_hash = ?',$hash)->where('locale = ?',$this->_locale)->order('id desc');
            $stmt = $select->query();
            $data = $stmt->fetchAll();
            
            if(is_array($data) && count($data) > 0) {
                foreach($data as $row) {
                    $tagObj = new Default_Model_Tag($row['parent_id']);
                    $tagData = $tagObj->getData();
                    
                    if(isset($tagData['status']) && $tagData['status'] == 1 && !empty($tagData['name_'.$this->_locale])) {
                        if($parentType == 'articles') {
                            $this->_redirect($this->view->baseUrl().$this->view->ptxUrl(array('url'=>$tagData['url_'.$this->_locale]),'article-tag',true),array('code'=>301));
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Articles (Private).
     * 
     * set up content for articles
     * @param string $parentType - type of parent
     * @param array $parentArray - ids of articles
     * @param array $tagData - data about tag
     */
    private function __articles($parentType, array $parentArray, array $tagData) {
        
        // Zend_Db_Select + Paginator.
        if(!empty($parentArray)) {
            $select = Article_Model_DbSelect_Articles::pureSelect();
            $select->columns(array('id','url_'.$this->_locale.' as url','name_'.$this->_locale.' as name','perex_'.$this->_locale.' as perex','published'))
                ->where('status = 1')->order('published desc')->where('name_'.$this->_locale.' != ""')->where('id IN (?)',$parentArray[$parentType]);
            $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
            $paginator->setCurrentPageNumber($this->_getParam('page',0))->setItemCountPerPage(1);
        } else {
            $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array(array()));            
        }
        
        // Build articles for view.
        $articles4View = array();
        $tagsObj = new Default_Model_TagRelations();
        foreach($paginator as $row) {
            // Categories.
            $categoriesIds = Admin_Model_DbSelect_CategoryRelations::get4Parent('articles',$row['id']," = 1");
            if(!empty($categoriesIds)) {
                $select = Admin_Model_DbSelect_Categories::pureSelect();
                $select->columns(array('name_'.$this->_locale.' as name','url_'.$this->_locale.' as url'))
                    ->where('status = 1')->where('id IN (?)',$categoriesIds)->where('name_'.$this->_locale.' != ""')->order('name_'.$this->_locale.' ASC');
                $stmt = $select->query();
                $categories = $stmt->fetchAll();
            } else {
                $categories = array();
            }
            $row['categories'] = $categories;
            
            // Tags.
            $tags = $tagsObj->get4Parent($row['id'],$parentType,'list');
            if(is_array($tags) && count($tags) > 0) {
                $select = Default_Model_DbSelect_Tags::pureSelect();
                $select->columns(array('name_'.$this->_locale.' as name','url_'.$this->_locale.' as url'))->where('status = 1')->where('id IN (?)',$tags);
                $stmt = $select->query();
                $row['tags'] = $stmt->fetchAll();
            } else {
                $row['tags'] = array();
            }
            
            $articles4View[] = $row;
        }
        
        // View variables.
        $this->view->articles = $articles4View;
        $this->view->paginator = $paginator;
        
        $this->view->perex = $this->view->transParam('Articles focus on ~0~',array('~0~'=>$tagData['name_'.$this->_locale]));
        $this->view->headTitle($this->view->perex, 'PREPEND');
        $this->view->headMeta()->appendName('description', $this->view->transParam('Articles focus on ~0~ - Today I would like to speak about ~0~',array('~0~'=>$tagData['name_'.$this->_locale])));
        $this->view->headMeta()->appendName('keywords', $this->view->transParam('article ~0~, articles about ~0~, ~0~ discussion, ~0~ review',array('~0~'=>$tagData['name_'.$this->_locale])));
        
        $this->render('article');
    }
}
