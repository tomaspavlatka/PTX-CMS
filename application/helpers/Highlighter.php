<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 31, 2010
**/

require_once '../library/geshi/geshi.php';

class Zend_View_Helper_Highlighter {
    
    /*
     * Buffer (Private).
     */
	private $_buffer;
	
	/*
	 * Locale (Private).
	 */
	private $_locale;
	
	/*
	 * Occurence (Private).
	 */
    private $_occurrence = array();
    
    /*
     * Replacers (Private).     
     */
    private $_replacers = array();
    
    /**
     * Highlighter.
     * 
     * highlight systax in code 
     * @param $string - string
     * @return string with highlight
     */
    public function highlighter($string) {
        $this->_locale = Zend_Registry::get('PTX_Locale');
    	$this->_replacers();
        $this->_prepareOccurrencies($string);
        
        if(count($this->_occurrence) > 0) {
            foreach($this->_occurrence as $row) {
                if(in_array($row['code'],array('php','mysql','javascript','ini','xml','html'))) {
                    $ident = $this->_getLangIdent($row['code']);
                    $string = str_replace($row['replace'],$this->_geshiSyntax($row['text'],$ident),$string);
                } else if(array_key_exists($row['code'],$this->_replacers)) {
                	if(!empty($this->_replacers[$row['code']]['text'])) {
                        $string = str_replace($row['replace'],$this->_replacers[$row['code']]['text'],$string);
                	} else if(!empty($this->_replacers[$row['code']]['script'])) {
                		Zend_Loader::loadClass('Zend_View');
                		$viewObj = new Zend_View();
                		$viewObj->addScriptPath(APPLICATION_PATH.'/modules/default/views/scripts/');
                		$data = $viewObj->partial($this->_replacers[$row['code']]['script']);
                		$string = str_replace($row['replace'],$data,$string);
                	}
                } 
            }
        }        
        
        return (string)$string;
    }
    
    /**
     * pripravi syntax s geshi
     * @param $string
     * @param $lang
     * @return unknown_type
     */
    private function _geshiSyntax($string, $lang = 'php') {
        $string = stripslashes(trim($string));
        $geshi = new Geshi($string,$lang);
        return @$geshi->parse_code();
    }
    
    private function _getLangIdent($code,$default = 'php') {
        if(preg_match('/lang="[a-zA-Z]*"/',$code,$matches)) {
            if(is_array($matches) && count($matches) > 0) {
                foreach($matches as $row) {
                    return substr($row,6,-1);            
                }
            }
        }
        
        return $default;
    }    
    
    /**
     * prepare occurrencies
     * @param $string - string
     */
    private function _prepareOccurrencies($string) {
        $this->_buffer = $string;
        
        if(preg_match_all('/%%[a-z0-9_]+%%/',$this->_buffer,$matches)) {          
            
            if(is_array($matches) && count($matches) > 0) {
                foreach($matches as $row) {
                    if(is_array($row)) {
                        foreach($row as $inRow) {
                            $count = strlen($inRow);
                            $start = strpos($this->_buffer,$inRow);
                            $end = strpos($this->_buffer,"%%~%%",$start); 
                            $this->_occurrence[] = array(
                                'text'    => trim(substr($this->_buffer,$start+$count,$end-$start-$count)),
                                'replace' => trim(substr($this->_buffer,$start,$end-$start+5)),
                                'code'    => substr($inRow,2,-2),
                                );
                            
                            $this->_buffer = substr($this->_buffer,$end);
                        }
                    }
                }
            }
        } 
    }
    
    /**
     * Replacers (Private).
     * 
     * prepares replacers for futher use.
     */
    private function _replacers() {
    	$select = Admin_Model_DbSelect_Replacers::pureSelect();
    	$select->columns(array('code','content_'.$this->_locale.' as content','script'))->where('status = 1')->order('id ASC');
    	$stmt = $select->query();
    	$data = $stmt->fetchAll();
    	
    	if(!empty($data)) {
    		foreach($data as $key => $values) {
    			$this->_replacers[$values['code']] = array(
                    'text' => $values['content'],
    			    'script' => $values['script']
    			);
    		}
    	}
    }
}