<?php

require_once APPLICATION_PATH .'/helpers/PtxUrl.php';

class Default_Model_SitemapGenerator {
    
    /*
     * Base Url (Private)
     * 
     * holds object of Zend_View_Helper_BaseUrl
     */
    private $_baseUrl;

    /*
     * Languages (Private).
     * 
     * holds array with active languages.
     */
    private $_languages;
    
    /*
     * Page Link (Private).
     * 
     * holds link for page, e.g. http://cms.ptx.cz
     */
    private $_pageLink;
    
    /*
     * Url (Private)
     * 
     * holds object of Zend_View_Helper_Url
     */
    private $_url;
    
    /*
     * Xml Code (Private).
     * 
     * holds xml code
     */
    private $_xmlCode;
    
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {       
       $this->_pageLink = 'http://'.$_SERVER['HTTP_HOST'];
       
       $this->_url = new Zend_View_Helper_PtxUrl();
       $this->_baseUrl = new Zend_View_Helper_BaseUrl();
       $this->_languages = Admin_Model_DbSelect_Languages::getActive();
    }
    
    /**
     * Finish.
     * 
     * generates end sequence for xml site map
     */
    public function finish() {
        $this->_xmlCode .= '</urlset>'."\n";
    }
    
    /**
     * Generate content.
     * 
     * generates content of sitemap
     */
    public function generateContent() {
        $this->_homepage();
        $this->_staticPages();
    }
    
    /**
     * Start.
     * 
     * generates start sequence for xml site map
     */
    public function start() {
    	$this->_xmlCode  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $this->_xmlCode .= '<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">'."\n";
    }
    
    /**
     * Get sitemap xml.
     * 
     * returns sitemap in xml format
     * @return sitemap in xml
     */
    public function getSitemapXml() {        
        return $this->_xmlCode;
    }
    
    /**
     * Homepage.
     * 
     * generates link for homapage
     */
    private function _homepage() {
        foreach($this->_languages as $key => $values) {
            $this->_xmlCode .= '<url>'."\n";
            $this->_xmlCode .= '<loc>'.$this->_pageLink.$this->_baseUrl->baseUrl().$this->_url->ptxUrl(array('lang'=>$values['code']),'homepage',true).'</loc>'."\n";
            $this->_xmlCode .= '<changefreq>daily</changefreq>'."\n";
            $this->_xmlCode .= '<priority>1</priority>'."\n";
            $this->_xmlCode .= '</url>'."\n";
        }
    }
    
    /**
     * Static pages.
     * 
     * generates links for static pages
     */
    private function _staticPages() {
        foreach($this->_languages as $key => $values) {
            $select = Default_Model_DbSelect_StaticPages::pureSelect();
            $select->columns(array('url_'.$values['code'].' as url','published'))->where('status = 1')->order('id asc')->where('name_'.$values['code'].' != ""');
            $stmt = $select->query();
            $pages = $stmt->fetchAll();
            
            // Foreach.
            foreach($pages as $pageKey => $pageValues) { 
            	$this->_xmlCode .= '<url>'."\n";
            	$this->_xmlCode .= '<loc>'.$this->_pageLink.$this->_baseUrl->baseUrl().$this->_url->ptxUrl(array('lang'=>$values['code'],'url'=>$pageValues['url']),'static-page',true).'</loc>'."\n";
                $this->_xmlCode .= '<changefreq>monthly</changefreq>'."\n";
                $this->_xmlCode .= '<priority>0.7</priority>'."\n";
            	$this->_xmlCode .= '<lastmod>'.$this->_transformDate($pageValues['published']).'</lastmod>'."\n";
            	$this->_xmlCode .= '</url>'."\n";
            }
        }
    }
    
    /**
     * Transform date.
     * 
     * transforms date from mktime into ISO_8601 format
     * @param ISO_8601 date
     */
    private function _transformDate($date) {
        if(!empty($date)) {
            return date('c',$date);
        }
    }
}