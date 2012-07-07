<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 3.11.2010
**/

require_once '../library/geshi/geshi.php';

class Zend_View_Helper_GeShiSyntax {
    
    /************* VARIABLES *****************/
    private $_buffer;
    private $_occurrence = array();
    
    /************* PUBLIC FUNCTION *****************/
    /**
     * returns string with geshiSyntax
     * @param $string - string
     * @return string with geshiSyntax
     */
    public function geShiSyntax($string) {
        $this->_prepareOccurrencies($string);
        
        if(count($this->_occurrence) > 0) {
            foreach($this->_occurrence as $row) {
                $ident = $this->_getLangIdent($row['code']);
                $string = str_replace($row['replace'],$this->_geshiSyntax($row['text'],$ident),$string);
            }
        }        
        
        return $string;
    }
    
    /************* PRIVATE FUNCTION *****************/
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
        if(preg_match_all('/<pre.*lang="[a-z]*">/',$this->_buffer,$matches)) {
            if(is_array($matches) && count($matches) > 0) {
                foreach($matches as $row) {
                    if(is_array($row)) {
                        foreach($row as $inRow) {
                            $start = strpos($this->_buffer,$inRow);
                            $end = strpos($this->_buffer,"</pre>",$start); 
                            $this->_occurrence[] = array(
                                'text'    => trim(substr($this->_buffer,$start+17,$end-$start-17)),
                                'replace' => trim(substr($this->_buffer,$start,$end-$start+6)),
                                'code'    => $inRow
                                );
                            
//                            echo '<pre>';
//                                echo $end;
//                                print(substr($this->_buffer,$end));
//                            echo '</pre>';
                            //exit;
                            $this->_buffer = substr($this->_buffer,$end);
                        }
                    }
                }
            }
        } 
    }
}