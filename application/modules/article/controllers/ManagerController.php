<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 24.8.2010
**/

class Article_ManagerController extends Zend_Controller_Action {
    
    /************** VARIABLES ****************/
    private $_xmlFolder;
    private $_xmlFile;
    
    /************** PUBLIC FUNCTION ****************/
    /**
     * PreDispatch.
     */
    public function preDispatch() {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
            
        $this->_xmlFolder = $_SERVER['DOCUMENT_ROOT'].'/'; 
        $this->_xmlFile = 'sitemap-article.xml';
        
    }
    
    /**
     * Sitemap.     
     */
    public function sitemapAction() {
        $code = $this->_getParam('code');
        
        if($code == 'YaTX4x8UZty9') {
            $sitemapObj = new Article_Model_SitemapGenerator();
            
            $sitemapObj->start();
            $sitemapObj->generateContent();
            $sitemapObj->finish();
            
            $file = fopen($this->_xmlFolder.$this->_xmlFile,'w+');
            fwrite($file,$sitemapObj->getSitemapXml());
            fclose($file);
        } 
    }
}