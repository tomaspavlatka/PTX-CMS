<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 15.11.2010
**/

class Admin_SitemapController extends Zend_Controller_Action {
    
    /************** VARIABLES **************/
    private $_user;
    
    private $_xmlFolder;
    private $_xmlFileDefault = 'sitemap.xml';
    private $_xmlFileArticle = 'sitemap-article.xml';
        
    /*************** PUBLIC FUNCTION ****************/
    /**
     * Init.
     */
    public function init() {
        $this->view->h1 = $this->view->trans('Sitemaps');
        
        $response = $this->getResponse();
        $response->insert('menuTop',$this->view->render('_menu/top.phtml'));
        $response->insert('incTop',$this->view->render('_inc/top-row.phtml'));
        $response->insert('incLeft',$this->view->render('_inc/left.phtml'));
        
        $this->_user = Zend_Auth::getInstance()->getStorage()->read();
        
        $this->_xmlFolder = $_SERVER['DOCUMENT_ROOT'].'/';
    }
    
    /**
     * Generate.
     */
    public function generateAction() {
        $module = $this->_getParam('gmodule');
        
        switch($module) {
            case 'default':
                $this->_generateDefault();
                PTX_Message::setMessage4User('admin','done',$this->view->transParam('Sitemap for <b>~0~</b> has been (re)generated.',array('~0~'=>$this->view->trans('Default module')),false));
                break;
            case 'article':
                $this->_generateArticle();
                PTX_Message::setMessage4User('admin','done',$this->view->transParam('Sitemap for <b>~0~</b> has been (re)generated.',array('~0~'=>$this->view->trans('Article module')),false));
                break;
        }
        
        $this->_redirect('/admin/sitemap');
    }
    
    /**
     * Index.
     */
    public function indexAction() {
        
        // Data.
        if(file_exists($this->_xmlFolder.$this->_xmlFileDefault)) {
            $this->view->defaultLastUpdate  = filemtime($this->_xmlFolder.$this->_xmlFileDefault);
            $this->view->defaultFileSize = filesize($this->_xmlFolder.$this->_xmlFileDefault);
        }
        
        if(file_exists($this->_xmlFolder.$this->_xmlFileArticle)) {
            $this->view->articleLastUpdate  = filemtime($this->_xmlFolder.$this->_xmlFileArticle);
            $this->view->articleFileSize = filesize($this->_xmlFolder.$this->_xmlFileArticle);
        }
        
        // View variables.
        $this->view->xmlFolder = $this->_xmlFolder;
        $this->view->xmlFileDefault = $this->_xmlFileDefault;
        $this->view->xmlFileArticle = $this->_xmlFileArticle;
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('Review');
        $this->view->headTitle($this->view->h1.' - '.$this->view->h2, 'PREPEND');
        
        // Anchor.
        PTX_Anchor::createAnchor('admin');
    }
    
    /*************** PRIVATE FUNCTION ****************/
    /**
     * Generate default.
     * 
     * generates sitemap for default module
     */
    private function _generateDefault() {
        $sitemapObj = new Default_Model_SitemapGenerator();
        $sitemapObj->start();
        $sitemapObj->generateContent();
        $sitemapObj->finish();
            
        $file = fopen($this->_xmlFolder.$this->_xmlFileDefault,'w+');
        fwrite($file,$sitemapObj->getSitemapXml());
        fclose($file);
    }
    
    /**
     * Generate article.
     * 
     * generates sitemap for article module
     */
    private function _generateArticle() {
        $sitemapObj = new Article_Model_SitemapGenerator();
        $sitemapObj->start();
        $sitemapObj->generateContent();
        $sitemapObj->finish();
            
        $file = fopen($this->_xmlFolder.$this->_xmlFileArticle,'w+');
        fwrite($file,$sitemapObj->getSitemapXml());
        fclose($file);
    }
}