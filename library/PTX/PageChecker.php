<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 16.6.2010
**/

class PTX_PageChecker {
    
    /************ VARIABLES ************/
    private $_sitemap = null; // sitemap in array
    
    private $_sitemapPath = null; // path to sitemp
    
    private $_number = 5; // how many urls check during one cycle
    
    private $_log = null; // log information
    
    private $_checkedUrls = array(); // array with urls which have been already checked.
    
    /************ PUBLIC FUNCTION ************/
    /**
     * konstruktor
     */
    public function __construct() {
    }
    
    /**
     * vrati aktualni obsah logu
     * @return log
     */
    public function getLog() {
        return $this->_log;
    }
    
    /**
     * vrati pocet url k prohlednuti
     * @return pocet
     */
    public function getNumber() {
        return $this->_number;
    }
    
    /**
     * vrati nastavenou cestu k sitemap
     * @return cesta
     */
    public function getSitemapPath() {
        return $this->_sitemap;
    }
    
    /**
     * provede kontrolu
     */
    public function makeCheck() {
        // 1) zkontrolujem, zda mame vsechno  
        $conditions = $this->_checkCondition();

        if(!$conditions) {
            return $this->_log;
        } else {
            
            // prevedem si xml do pole
            $this->_parseXml();    
            
            if(!isset($this->_sitemap['urlset']['url']) || !is_array($this->_sitemap['urlset']['url']) || count($this->_sitemap['urlset']['url']) < 1) {
                $this->_add2Log('Sitemap does not contain any input');
                return $this->_log;    
            } else {               
                $this->_makeCheck();
                 
                return $this->_log;
            }
        }
    }
    
    /**
     * nastavi pocet url ke kontrole
     * @param $number - pocet
     */
    public function setNumber($number) {
        if(Zend_Validate::is($number,'Int')) {
            $this->_number = (int)$number;
        } else {
            $this->_add2Log('Number must be integer');
        }
    }
    
    /**
     * nastavi cestu k sitemap
     * @param $path - cesta
     */
    public function setSitemapPath($path) {
        if(file_exists('.'.$path)) {
            $this->_sitemapPath = $path;
        } else {
            $this->_add2Log('Wrong path to sitemap');
        }
    }
    
    /************ PRIVATE FUNCTION ************/
    /**
     * prida zapis do logu
     * @param $string - retezec     
     */
    private function _add2Log($string) {
        $this->_log .= (!empty($this->_log)) ? "\r\n\r\n [".date('Y/m/d H:i:s')."] \r\n".$string : "[".date('Y/m/d H:i:s')."] \r\n".$string;
    }
    
    /**
     * zjisti, zda mame vsechno potrebne
     * @return true | false
     */
    private function _checkCondition() {
        $result = true; 
        
        // 1) Sitemap
        if(empty($this->_sitemapPath) || !file_exists('.'.$this->_sitemapPath)) {
            $this->_add2Log('Cannot start withnout sitemap.');
            
            $result = false;
        }
        
        return $result;
    }
    
    /**
     * provede samotne overeni
     */
    private function _makeCheck() {
        $max = count($this->_sitemap['urlset']['url'])-1;
        $i = 0;
        
        // pokud jsme zadali vic url k provereni nez mame => prenastavime hodnotu
        if($max < $this->_number) { 
            $this->_number = $max;
        }
        
        while($i < $this->_number) {
            $index = rand(0,$max);
            
            // pokud jsme neco nasli a jeste jsme to nezkontrolovali
            if(isset($this->_sitemap['urlset']['url'][$index]['loc']) && !empty($this->_sitemap['urlset']['url'][$index]['loc']) && !in_array($this->_sitemap['urlset']['url'][$index]['loc'],$this->_checkedUrls)) {
                $headers = @get_headers($this->_sitemap['urlset']['url'][$index]['loc'],1);

                $log = $this->_sitemap['urlset']['url'][$i]['loc']."\r\n";
                if(!is_array($headers))  {
                    $log .= "No reachable!";
                } else {
                    $log .= 'HTTP Status: '. $headers[0].' | DATE: '.$headers['Date'];
                }

                $this->_add2Log($log);
                $this->_checkedUrls[] = $this->_sitemap['urlset']['url'][$index]['loc'];
                $i++;
            }
        }
    }
    
    /**
     * prevede sitemap z xml do pole
     * @return unknown_type
     */
    private function _parseXml() {        
        $this->_sitemap = PTX_Xml::xml2array(file_get_contents('.'.$this->_sitemapPath));
    }
}