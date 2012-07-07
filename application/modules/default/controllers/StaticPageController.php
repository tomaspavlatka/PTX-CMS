<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 22.8.2010
**/

class StaticPageController extends Zend_Controller_Action {

    public function init() {
        $response = $this->getResponse();
        
        // Widgets.
        $response->insert('widgetFooterBanners',$this->view->render('_widget/footer_banners.phtml'));
        $response->insert('widgetSlider',$this->view->render('_widget/slider.phtml'));
        $response->insert('widgetTopNews',$this->view->render('_widget/top_news.phtml'));
        $response->insert('widgetRight',$this->view->render('_widget/right.phtml'));
    
        // Menu
        $response->insert('menuHeader',$this->view->render('_menu/header.phtml'));
        $response->insert('menuTop',$this->view->render('_menu/top.phtml'));
        
        // Include.
        $response->insert('incLang',$this->view->render('_inc/lang-switcher.phtml'));

        // projectconfig
        $this->_config = PTX_Session::getSessionValue('settings', 'project');
        $this->_locale = Zend_Registry::get('PTX_Locale');
        $this->view->locale = Zend_Registry::get('PTX_Locale');
    }

    /**
     * Detail.
     */
    public function detailAction() {
        // Data.
        $url = $this->_getParam('url');
        $pageObj = $this->_getStaticPageObj($url);
        if(!($pageObj instanceof Default_Model_StaticPage)) {
            throw new PTX_Exception(__CLASS__.": This text page is not for visitors [url:{$url}]");
        } 
        $pageData = $pageObj->getData(false,true);
        
        if(empty($pageData['name_'.$this->_locale]) || empty($pageData['content_'.$this->_locale])) {
            throw new PTX_Exception(__CLASS__.": This text page is not for visitors [url:{$url}]");
        }
        
        // Update shown.
        $pageObj->updateShown();
        
        // View variables.
        $this->view->data = $pageData;
        
        $response = $this->getResponse();
        $response->insert('widgetHomeLeft',$this->view->render('_menu/static_page_left.phtml')); 
        
        // Page settings.
        $this->view->headTitle($pageData['name_'.$this->_locale], 'PREPEND');
        $this->view->headMeta()->appendName('description', $pageData['seo_description_'.$this->_locale]);
        $this->view->headMeta()->appendName('keywords', $pageData['seo_keywords_'.$this->_locale]);
        $this->view->headMeta()->appendName('robots','index,follow');
        $this->view->headMeta()->appendName('googlebot','snippet,archive');
    }
    
    /**
     * Preview.
     */
    public function previewAction() {
        // Data.
        $id = $this->_getParam('url');
        $pageObj = $this->_getStaticPageObj($id);
        $pageData = $pageObj->getData(false,true);
        
        if(empty($pageData['name_'.$this->_locale]) || empty($pageData['content_'.$this->_locale])) {
            throw new PTX_Exception(__CLASS__.": This text page is not for visitors [url:{$url}]");
        }
        
        // Update shown.
        $pageObj->updateShown();
        
        // View variables.
        $this->view->data = $pageData;
        
        // Page settings.
        $this->view->headTitle($pageData['name_'.$this->_locale], 'PREPEND');
        $this->view->headMeta()->appendName('description', $pageData['seo_description_'.$this->_locale]);
        $this->view->headMeta()->appendName('keywords', $pageData['seo_keywords_'.$this->_locale]);
        $this->view->headMeta()->appendName('robots','index,follow');
        $this->view->headMeta()->appendName('googlebot','snippet,archive');
    }
    
    /**
     * Get static page obj
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
            $select->columns(array('DISTINCT(parent_id)'))->where("parent_type = 'pages'")->where('url_hash = ?',$hash)->where('locale = ?',$this->_locale)->order('id desc');
            $stmt = $select->query();
            $data = $stmt->fetchAll();
            
            if(is_array($data) && count($data) > 0) {
                foreach($data as $row) {
                    $pageObj = new Default_Model_StaticPage($row['parent_id']);
                    $pageData = $pageObj->getData(false,true);
                    
                    if(isset($pageData['status']) && $pageData['status'] == 1 && !empty($pageData['name_'.$this->_locale])) {
                        $this->_redirect($this->view->baseUrl().$this->view->ptxUrl(array('url'=>$pageData['url_'.$this->_locale]),'static-page',true),array('code'=>301));
                    }
                }
            }
        }
    }
}
