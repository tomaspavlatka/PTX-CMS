<?php
/**
 * Author: Tomas Pavlatka
 * Email: tomas.pavlatka@gmail.com
 * Created: 23.1.2010 19:11:38
 */
 
class Zend_View_Helper_ViewMsg {
        
    public function viewMsg($string,$type) {
        return '<p class="msg '.$type.'">'.$string.'</p>';
    }
}