<?php

require_once APPLICATION_PATH .'/helpers/Trans.php';
require_once APPLICATION_PATH .'/helpers/TransParam.php';
class Article_Model_Mailer extends Zend_Mail {
	
	/************* VARIABLES **************/
	private $_server;
	private $_project;
	
	private $_trans;
	private $_transParam;
	
	/************* CONSTANCT **************/
	const NOREPLY_MAIL = 'noreply@pavlatka.cz';
	const DEVEL_MAIL = 'patsx.com@gmail.com';
	
	/************* PUBLIC FUNCTION **************/
	/**
	 * Construct.
	 * 
	 * constructor of class
	 * @param $charset - charset      
	 * @param $smtp - smtp
	 */
	public function __construct($charset = 'utf-8',$smtp = null) {
		parent::__construct($charset);
		
		if(!empty($smtp) && Zend_Validate::is($smtp,'EmailAddress')) {
            $config = array('auth' => 'login',
                'username' => $smtp,
                'password' => 'ruo6m1bi123','ssl'=>'ssl','port' => 465);
			$transport = new Zend_Mail_Transport_Smtp('smtp.googlemail.com', $config);
            $this->setDefaultTransport($transport);
		}
		
		$this->EOL = "\r\n";
		
		$this->_trans = new Zend_View_Helper_Trans();
		$this->_transParam = new Zend_View_Helper_TransParam();
	}
	
	/************* PRIVATE FUNCTION **************/
}