<?php
/**
 * Author: Tomas Pavlatka
 * Email: tomas.pavlatka@gmail.com
 * Created: 19.1.2010 21:33:40
 */
 
class Zend_View_Helper_StatusIcon {
	
	public function statusIcon($status) {
		switch($status) {
			case 0:
				return '<img src="/images/icons/yellow/18/close.png" alt="0" width="18" height="18" />';
			case 1:				
				return '<img src="/images/icons/blue/18/ok.png" alt="1" width="18" height="18" />';
		}
	}
}