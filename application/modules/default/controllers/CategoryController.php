<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 23.8.2010
**/

class CategoryController extends Zend_Controller_Action {
    
    /************* PUBLIC FUNCTION  ***************/
    /**
     * Init.
     */
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
        
        // pro vyhledavace
        $this->view->headMeta()->appendName('robots','index,follow');
        $this->view->headMeta()->appendName('googlebot','snippet,archive');
    }

    /**
     * Detail.
     */
    public function detailAction() {
        $url = $this->_getParam('url'); 
        $catObj = $this->_getCategoryObj($url);
        if(!($catObj instanceof Default_Model_Category)) {
            throw new PTX_Exception(__CLASS__.": This category is not for visitors [url:{$url}]");
        }
        $categoryData = $catObj->getData(false,true);
        $this->view->categoryData = $categoryData;
        
        if($categoryData['parent_type'] == 'articles') {
            $this->__articles($categoryData);
        } else if($categoryData['parent_type'] == 'photos') {
            $this->__photos($categoryData);
        }
        
        // Page settings.
        $this->view->headTitle($categoryData['name_'.$this->_locale], 'PREPEND');
        $this->view->headMeta()->appendName('description', $categoryData['seo_description_'.$this->_locale]);
        $this->view->headMeta()->appendName('keywords', $categoryData['seo_keywords_'.$this->_locale]);
    }
    
    /**
     * Get category obj.
     * 
     * returns object for category
     * @param $url - url
     * @return Default_Model_Category | null
     */
    private function _getCategoryObj($url) {
        $hash = md5(trim($url));
        $catsObj = new Default_Model_Categories();
        $catData = $catsObj->findByHash($hash," = 1");

        if(isset($catData->id)) {
            return new Default_Model_Category($catData->id);
        } else {
            $select = Default_Model_DbSelect_UrlBackups::pureSelect();
            $select->columns(array('DISTINCT(parent_id)'))->where("parent_type = 'categories'")->where('url_hash = ?',$hash)->where('locale = ?',$this->_locale)->order('id desc');
            $stmt = $select->query();
            $data = $stmt->fetchAll();
            
            if(is_array($data) && count($data) > 0) {
                foreach($data as $row) {
                    $categoryObj = new Default_Model_Category($row['parent_id']);
                    $categoryData = $categoryObj->getData(false,true);
                    
                    if(isset($categoryData['status']) && $categoryData['status'] == 1 && !empty($categoryData['name_'.$this->_locale])) {
                        if($categoryData['parent_type'] == 'articles') {
                            $this->_redirect($this->view->baseUrl().$this->view->ptxUrl(array('url'=>$categoryData['url_'.$this->_locale]),'article-category',true),array('code'=>301));
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Articles (Private).
     * 
     * complete data for articles categories
     * @param array $categoryData - data about category
     */
    private function __articles(array $categoryData) {
        
        // Articles.
        $select = Admin_Model_DbSelect_CategoryRelations::pureSelect();
        $select->columns(array('DISTINCT(parent_id)'))->where('parent_type = ?','articles')->where('status = 1')->where('category_id = ?',(int)$categoryData['id']);
        $stmt = $select->query();
        $articles = $stmt->fetchAll();
        $articlesIds = PTX_Page::getIdsArray($articles,'parent_id');
        
        // Zend_Db_Select + Paginator.
        if(!empty($articlesIds)) {
            $select = Article_Model_DbSelect_Articles::pureSelect();
            $select->columns(array('id','url_'.$this->_locale.' as url','name_'.$this->_locale.' as name','perex_'.$this->_locale.' as perex','published','image_file'))
                ->where('status = 1')->order('published desc')->where('name_'.$this->_locale.' != ""')->where('id IN (?)',$articlesIds);
            $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        } else {
            $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array(array()));
        }
        $paginator->setCurrentPageNumber($this->_getParam('page',0))->setItemCountPerPage(5);

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
            $tags = $tagsObj->get4Parent($row['id'], 'articles', 'list');
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
        
        $this->view->paginator = $paginator;
        $this->view->articles = $articles4View;
        $this->render('article');
    }
    
    /**
     * Photos (Private).
     * 
     * complete data for photos categories
     * @param array $categoryData - data about category
     */
    private function __photos(array $categoryData) {
        // Zend_Db_Select + Paginator.
        $select = Photo_Model_DbSelect_Photos::pureSelect();
        $select->columns(array('id','name_'.$this->_locale.' as name','file_name','image_width','image_height'))
            ->where('status = 1')->order('position asc')->where('parent_type = ?','photos')
            ->where('parent_id = ?',(int)$categoryData['id'])->where('name_'.$this->_locale.' != ""');
        $stmt = $select->query();
        $photos = $stmt->fetchAll();
        
        // Data 4 view.
        $data4View = array();
        $tagsObj = new Default_Model_TagRelations();
        $thumbnailsParams = array(
            'original_path'=>'./userfiles/images/photo/',
            'return_type'=>'href',
            'settings' => array('width'=>205, 'height'=>165, 'save_path'=>'./tmp/phpthumb/50/', 'check_size_if_exists'=>true));
        foreach($photos as $row) {
            $thumbnailsParams['alt'] = $row['name'];
            $thumbnailsParams['orig_width'] = $row['image_width'];
            $thumbnailsParams['orig_height'] = $row['image_height'];
            $row['thumbnail'] = $this->view->ptxPhpThumb($row['file_name'],'thumbnail',$thumbnailsParams);
            
            // Tags.
            $tags = $tagsObj->get4Parent($row['id'], 'articles', 'list');
            if(is_array($tags) && count($tags) > 0) {
                $select = Default_Model_DbSelect_Tags::pureSelect();
                $select->columns(array('name_'.$this->_locale.' as name','url_'.$this->_locale.' as url'))->where('status = 1')->where('id IN (?)',$tags);
                $stmt = $select->query();
                $row['tags'] = $stmt->fetchAll();
            } else {
                $row['tags'] = array();
            }

            // Save row.
            $data4View[] = $row;
        }
        
        // View variables.
        $this->view->photos = $data4View;
        
        $this->render('photo');
    }
}