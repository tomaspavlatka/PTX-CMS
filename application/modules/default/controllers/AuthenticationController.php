<?php
class AuthenticationController extends Zend_Controller_Action {

    /************* PUBLIC FUNCTION *************/
    public function init() {
        $response = $this->getResponse();
        $this->view->h1 = $this->view->trans('Authorization');

        // change default layout
        Zend_Layout::getMvcInstance()->setLayout('layout-admin');
        
        $response = $this->getResponse();
        $response->insert('incTop',$this->view->render('_inc/top-row.phtml'));
        
        // pro vyhledavace
        $this->view->headMeta()->appendName('robots','noindex,nofollow');
        $this->view->headMeta()->appendName('googlebot','nosnippet,noarchive');
    }
    
    /***
     * zapomenute heslo
     */
    public function forgotAction() {
    	// page setting
    	$this->view->h1 = $this->view->trans('Forgot login data');
        $this->view->headTitle($this->view->h1, 'PREPEND');
    	
        // form
        $form = new Default_Form_UserForgotLogin();
        $this->view->form = $this->_forgotForm($this->getRequest(),$form);
        
        // final view
        $this->view->msgs = PTX_Message::getMessage4User("default");
    }

    /***
     * prihlaseni
     */
    public function loginAction() {
        if(Zend_Auth::getInstance()->hasIdentity()){
            $this->_redirect('admin/index');
        }
        
        $this->view->h2 = $this->view->trans('Login');
        $this->view->headTitle($this->view->h1.' - '.$this->view->h2, 'PREPEND');
        $this->view->headMeta()->appendName('robots','noindex,nofollow');
        $this->view->headMeta()->appendName('googlebot','nosnippet,noarchive');
        
        // form
        $form = new Default_Form_UserLogin();
        $this->view->form = $this->_loginForm($this->getRequest(),$form);
        
        // final view
        $this->view->msgs = PTX_Message::getMessage4User("user");
    }

    /***
     * odhlaseni
     */
    public function logoutAction() {
    	Zend_Session::namespaceUnset('admindata');
    	Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/');
    }

    /************* PRIVATE FUNCTION *************/
    /**
     * zpracuje formular
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Form $form
     * @return Zend_Form
     */
    private function _forgotForm(Zend_Controller_Request_Http $request, Zend_Form $form) {
        if($request->isPost()) {
            $formData = $request->getPost();

            if($form->isValid($formData)) {
            	$email = $form->getValue('email');
            	
            	$usersObj = new Default_Model_Users();
            	$user = $usersObj->findUserByEmail($email);

            	if(isset($user->status) && $user->status == 1) {
            	    // nove heslo
            	    $newPassword = PTX_Password::generatePassword(7,3);
            	    $userObj = new Default_Model_User($user->id);
            	    $userObj->updatePassword($newPassword);

            	    // poslem mail
            	    $mailer = new Default_Model_Mailer('utf-8');
            	    $mailer->forgotLogin($user,$newPassword);
            	    
            	    PTX_Message::setMessage4User('default','done',$this->view->trans('Your password has been reseted and sent to your inbox.'));
            	} else {
            	    PTX_Message::setMessage4User('default','error',$this->view->trans('No user found.'));
            	    $form->populate($formData);
            	}
            } else {
            	$form->populate($formData);
            }
        } 
        
        return $form;    
    }
    
    /**
     * pripravi adapter na prihlaseni
     */
    private function _getAuthAdapter() {
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        $authAdapter->setTableName('users')
                    ->setIdentityColumn('email')
                    ->setCredentialColumn('password')
                    ->setCredentialTreatment('MD5(?) AND status = 1');
                    
        return $authAdapter;
    }
    
    /**
     * zpracuje formular
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Form $form
     * @return Zend_Form
     */
    private function _loginForm(Zend_Controller_Request_Http $request, Zend_Form $form) {
        if($request->isPost()){
            if($form->isValid($this->_request->getPost())){
                $authAdapter = $this->_getAuthAdapter();
        
                $email = $form->getValue('email');
                $password = $form->getValue('password').SECURITY_SALT;
                
                $authAdapter->setIdentity($email)->setCredential($password);
                                                    
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);
                
                if($result->isValid()){
                    $identity = $authAdapter->getResultRowObject();
                    
                    $authStorage = $auth->getStorage();
                    $authStorage->write($identity);
                    
                    
                    // updatujem info o poslednim prihlaseni
//                    $userObj = new Default_Model_User($identity->id);
//                    $userObj->updateLastLogin();      
//
//                    $session = new Zend_Session_Namespace('userdata');
//                    
//                    $userData = $userObj->getData();
//                    if(isset($userData->user_locale) && !empty($userData->user_locale)) {
//                    	$locale = new Zend_Locale($userData->user_locale);
//                    } else {
//                    	$locale = new Zend_Locale('en_GB');
//                    }
                    Zend_Registry::set('locale',$locale);
                    
                    $this->_redirect('admin/index');
                } else {
                	PTX_Message::setMessage4User('user','error',$this->view->trans("E-mail address or password is wrong."));
                }
            }
        }
        
        return $form;
    }
    
}





