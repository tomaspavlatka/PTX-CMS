<?php

require_once APPLICATION_PATH .'/helpers/PtxUrl.php';
class Article_Model_SitemapGenerator {
    
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
     * constructor of class.
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
        $this->_contentCategories();
        $this->_contentArticles();
        $this->_contentTags();
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
        // vratime xml format sitemap
        return $this->_xmlCode;
    }
    
    /**
     * Content articles.
     * 
     * adds links for articles
     */
    private function _contentArticles() {
        foreach($this->_languages as $key => $values) {
            $select = Article_Model_DbSelect_Articles::pureSelect();
            $select->columns(array('id','url_'.$values['code'].' as url','published'))->where('status = 1')->order('id asc')->where('name_'.$values['code'].' != ""');;
            $stmt = $select->query();
            $data = $stmt->fetchAll();
            
            // Foreach.
            foreach($data as $row) {
                // Add code into xml.
            	$this->_xmlCode .= '<url>'."\n";
            	$this->_xmlCode .= '<loc>'.$this->_pageLink.$this->_baseUrl->baseUrl().$this->_url->ptxUrl(array('lang'=>$values['code'],'url'=>$row['url']),'article',true).'</loc>'."\n";
            	$this->_xmlCode .= '<changefreq>never</changefreq>'."\n";
                $this->_xmlCode .= '<priority>0.8</priority>'."\n";
            	$this->_xmlCode .= '<lastmod>'.$this->_transformDate($row['published']).'</lastmod>'."\n";
            	$this->_xmlCode .= '</url>'."\n";
            }
        }
    }
    
    /**
     * Content categories.
     * 
     * adds links for categories
     */
    private function _contentCategories() {
        foreach($this->_languages as $key => $values) {
            // Zend_Db_Select + Data.
            $select = Default_Model_DbSelect_Categories::pureSelect();
            $select->columns(array('id','url_'.$values['code'].' as url','last_input','published'))
                ->where('status = 1')->order('id asc')->where("parent_type = 'articles'")
                ->where('name_'.$values['code'].' != ""');
            $stmt = $select->query();
            $data = $stmt->fetchAll();
            
            // Foreach.
            foreach($data as $catKey => $catData) {
                // Add code into xml.
                $this->_xmlCode .= '<url>'."\n";
                $this->_xmlCode .= '<loc>'.$this->_pageLink.$this->_baseUrl->baseUrl().$this->_url->ptxUrl(array('lang'=>$values['code'],'url'=>$catData['url']),'article-category',true).'</loc>'."\n";
                $this->_xmlCode .= '<changefreq>weekly</changefreq>'."\n";
                $this->_xmlCode .= '<priority>0.8</priority>'."\n";
    
                if(!empty($catData['last_input'])) {
                    $this->_xmlCode .= '<lastmod>'.$this->_transformDate($catData['last_input']).'</lastmod>'."\n";
                } else {
                    $this->_xmlCode .= '<lastmod>'.$this->_transformDate($catData['published']).'</lastmod>'."\n";
                }
                
                $this->_xmlCode .= '</url>'."\n";
            }
        }
    }
    
    /**
     * Content tags.
     * 
     * adds links for tags
     */
    private function _contentTags() {
         $select = Default_Model_DbSelect_TagRelations::pureSelect();
         $select->columns(array('DISTINCT(tag_id)'))->where("parent_type = 'articles'")->where('status = 1');
         $stmt = $select->query();
         $data = $stmt->fetchAll();
         $idsArray = PTX_Page::getIdsArray($data, 'tag_id');

         if(!empty($idsArray)) {
            foreach($this->_languages as $key => $values) {
                $select = Default_Model_DbSelect_Tags::pureSelect();
                $select->columns(array('url_'.$values['code'].' as url'))->where('status = 1')->where('id IN (?)',$idsArray)->order('id asc')->where('name_'.$values['code'].' != ""');
                $stmt = $select->query();
                $data = $stmt->fetchAll();
                
                // Foreach.
                foreach($data as $row) {
                    // Add code into xml.
                    $this->_xmlCode .= '<url>'."\n";
                    $this->_xmlCode .= '<loc>'.$this->_pageLink.$this->_baseUrl->baseUrl().$this->_url->ptxUrl(array('lang'=>$values['code'],'url'=>$row['url']),'article-tag',true).'</loc>'."\n";
                    $this->_xmlCode .= '<changefreq>weekly</changefreq>'."\n";
                    $this->_xmlCode .= '<priority>0.6</priority>'."\n";
                    $this->_xmlCode .= '</url>'."\n";
                }
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