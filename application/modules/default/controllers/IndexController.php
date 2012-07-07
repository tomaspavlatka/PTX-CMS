<?php

class IndexController extends Zend_Controller_Action{
    
    private $_config;
    
    public function init() {
        $response = $this->getResponse();
        
        // Widgets.
        $response->insert('widgetHomeLeft',$this->view->render('_widget/home_left.phtml'));
        $response->insert('widgetFooterBanners',$this->view->render('_widget/footer_banners.phtml'));
        $response->insert('widgetSlider',$this->view->render('_widget/slider.phtml'));
        $response->insert('widgetTopNews',$this->view->render('_widget/top_news.phtml'));
        $response->insert('widgetRight',$this->view->render('_widget/right.phtml'));
    
        // Menu
        $response->insert('menuHeader',$this->view->render('_menu/header.phtml'));
        $response->insert('menuTop',$this->view->render('_menu/top.phtml'));
        
        // Include.
        $response->insert('incLang',$this->view->render('_inc/lang-switcher.phtml'));
        
        
        // Robots.
        $this->view->headMeta()->appendName('robots','index,follow');
        $this->view->headMeta()->appendName('googlebot','snippet,archive');

        // projectconfig
        $this->_config = PTX_Session::getSessionValue('settings', 'project');
        $this->_locale = Zend_Registry::get('PTX_Locale');
        $this->view->locale = Zend_Registry::get('PTX_Locale');
    }

    /**
     * Index.
     */
    public function indexAction() {
        
        // Zend_Db_Select.
        $select = Article_Model_DbSelect_Articles::pureSelect();
        $select->columns(array('articles.id','articles.name_'.$this->_locale.' as name','articles.content_'.$this->_locale.' as content','articles.url_'.$this->_locale.' as url','articles.published','articles.image_file'))
            ->join(array('relation' => 'category_relations'),'articles.id = relation.parent_id')->where('relation.parent_type = ?','articles')->where('relation.category_id = ?',6)
            ->where('articles.status = 1')->order('articles.published desc')->limit(10)->where('articles.name_'.$this->_locale.' != ""');
        $stmt = $select->query();
        $articles = $stmt->fetchAll();
        
        
        // Data for view.
        $articles4View = array();
        $tagsObj = new Default_Model_TagRelations();
        foreach($articles as $row) {
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
        $this->view->articles = $articles4View;
        
        // Page settings.
        if(isset($this->_config['indextitle'.$this->_locale])) {
            $this->view->headTitle($this->_config['indextitle'.$this->_locale], 'PREPEND');
        }
        if(isset($this->_config['indexdescription'.$this->_locale])) {
            $this->view->headMeta()->appendName('description', $this->_config['indexdescription'.$this->_locale]);
        }
        
        if(isset($this->_config['indexkeywords'.$this->_locale])) {
            $this->view->headMeta()->appendName('keywords', $this->_config['indexkeywords'.$this->_locale]);
        }
    }
}

