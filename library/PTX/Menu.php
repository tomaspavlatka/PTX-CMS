<?php
class PTX_Menu {
	private $_module;
	private $_controller;
	private $_action;
	private $_request;
	
	public function __construct() {
		$this->_request = Zend_Controller_Front::getInstance()->getRequest();
		$this->_module = $this->_request->getModuleName();
		$this->_controller = $this->_request->getControllerName();
		$this->_action = $this->_request->getActionName();		
	}
}