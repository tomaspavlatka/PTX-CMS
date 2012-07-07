<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 24.8.2010
**/

class ManagerController extends Zend_Controller_Action {
    
    /************** VARIABLES ****************/
    private $_xmlFolder;
    private $_xmlFile;
        
    /************** PUBLIC FUNCTION ****************/
    /**
     * Predispatch.
     * 
     * block render of layout
     */
    public function preDispatch() {
        
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
            
        $this->_xmlFolder = $_SERVER['DOCUMENT_ROOT'].'/'; 
        $this->_xmlFile = 'sitemap.xml';
        
    }
    
    /**
     * Sitemap.     
     */
    public function sitemapAction() {
        $code = $this->_getParam('code');
        
        if($code == 'YaTX4x8UZty9') {
            $sitemapObj = new Default_Model_SitemapGenerator();
            
            $sitemapObj->start();
            $sitemapObj->generateContent();
            $sitemapObj->finish();
            
            $file = fopen($this->_xmlFolder.$this->_xmlFile,'w+');
            fwrite($file,$sitemapObj->getSitemapXml());
            fclose($file);
        } 
    }
    
    public function languageAction() {
         // Build database.
        $config = Zend_Registry::get('config');
        $params = array(
            'db_host'       => $config->resources->db->params->host,
            'db_name'       => $config->resources->db->params->dbname,
            'db_user'       => $config->resources->db->params->username,
            'db_password'   => $config->resources->db->params->password,
            'db_charset'    => $config->resources->db->params->charset);
        $dbObj = new PTX_Database($params);
        $dbObj->connect();
        $dbObj->addLanguage('cs');
    }
}