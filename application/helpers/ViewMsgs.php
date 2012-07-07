<?php
/**
 * Author: Tomas Pavlatka
 * Email: tomas.pavlatka@gmail.com
 * Created: 23.1.2010 22:48:41
 */
 
class Zend_View_Helper_ViewMsgs {
        
    public function viewMsgs($msgs) {
        $toString = null;
        
    	if(is_array($msgs)) {
    		foreach($msgs as $msg) {
    			foreach($msg as $result => $text) {
    				$toString .= '<p class="msg '.$result.'">'.$text.'</p>';
    			}
    		}
    	}
    	
    	return $toString;
    }
}