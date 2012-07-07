<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 7.11.2010
**/

class RssController extends Zend_Controller_Action {
    
    /************** PUBLIC FUNCTION ****************/
    /**
     * PreDispatch.
     */
    public function preDispatch() {
        Zend_Layout::getMvcInstance()->setLayout('rss');                
    }
    
    /**
     * Article.
     */
    public function articleAction() {
        // Default variables.
        $entries = array();
        
        $cache = Zend_Registry::get('ptxcache');
        $ident = 'rss_articles';
        
        if(!($rss = $cache->load($ident))) {
            $articles = $this->_getArticles();
            
            if(is_array($articles) && count($articles) > 0) {            
                $link = 'http://'.$_SERVER['HTTP_HOST'].'/clanky';
                $categories = $this->_prepareCategories();    
            
                // Foreach through array.
                foreach ($articles AS $row) {
                    if(isset($categories[$row['category_id']])) {
                        $entry = array(
                            'title'       => "{$row['name']}",
                            'link'        => $link.'/'.$categories[$row['category_id']].'/'.$row['url'].'-'.$row['id'],
                            'author'      => 'tomas.pavlatka@gmail.com (Tomáš Pavlátka)',
                            'description' => "{$row['perex']}",
                            'lastUpdate'  => "{$row['published']}");
     
                        $entries[] = $entry;
                    }
                }
            }
     
            // RSS array.
            $rss = array(
                    'title'         => 'Tomáš Pavlátka',
                    'webmaster'     => 'tomas.pavlatka@gmail.com',
                    'link'          => 'http://pavlatka.cz',
                    'description'   => 'Blog www programátora žijícího a pracujícího na Kypru',
                    'author'        => 'Tomas Pavlatka',
                    'email'         => 'tomas.pavlatka@gmail.com',
                    'charset'       => 'UTF-8',
                    'entries'       => $entries);
            
            // Store into cache.
            $cache->save($rss,$ident);
        } 
        
        // Import.
        $feed = Zend_Feed::importArray($rss, 'rss');
        
        // Save log.
        $this->_storeLog();
         
        // Show it.
        $feed->send();
    }
    
    /************** PRIVATE FUNCTION **************/
    /**
     * Get articles.
     * 
     * returns articles.
     * @return articles
     */
    private function _getArticles() {
        $select = Article_Model_DbSelect_Articles::pureSelect();
        $select->columns(array('id','name','url','perex','category_id','published'))
            ->where('status = 1')
            ->order('published desc');
        $stmt = $select->query();
        return $stmt->fetchAll();
    }
    
    /**
     * Prapare categories.
     * 
     * prepares categories
     * @return categories array
     */
    private function _prepareCategories() {
        $select = Default_Model_DbSelect_Categories::pureSelect();
        $select->columns(array('id','url'))
            ->where('status = 1')
            ->order('id desc');
        $stmt = $select->query();
        $data = $stmt->fetchAll();
        
        $categoryArray = array();
        if(is_array($data) && count($data) > 0) {
            foreach($data as $row) {
                $categoryArray[$row['id']] = $row['url'];
            }
        }
        
        return $categoryArray;
    }
    
    /**
     * Store log.
     * 
     * store access log
     */
    private function _storeLog() {
        $filePath = './_logs/rss-access';
        
        
        $log  = '['.date('m-d-Y H:i:s').'] ';
        $log .= $_SERVER['REMOTE_ADDR'].' - '.$_SERVER['HTTP_USER_AGENT']."\r\n";
        
        $fp = @fopen($filePath,'a+');
        @fwrite($fp,$log);
        @fclose($fp);
    }
}