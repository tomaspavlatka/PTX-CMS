<?php

require_once APPLICATION_PATH .'/helpers/Trans.php';
require_once APPLICATION_PATH .'/helpers/TransParam.php';
require_once APPLICATION_PATH .'/helpers/PtxUrl.php';
class Default_Model_Mailer extends Zend_Mail {
	
    /*
     * Locale (Private).
     * 
     * holds actual locale.
     */
    private $_locale;
    
	/*
     * Project Config (Private).
     * 
     * holds config for project
     */
    private $_projectConfig;
    
    /*
     * Url (Private).
     * 
     * holds instance of PtxUrl helper
     */
    private $_url;
	
	const DEVEL_MAIL = 'patsx.com@gmail.com';
	
	/**
     * Construct.
     * 
     * constructor of class
     * @param string  $charset - charset      
     */
    public function __construct($charset = 'utf-8') {
        parent::__construct($charset);
        
        // Variables.
        $this->_trans = new Zend_View_Helper_Trans();
        $this->_transParam = new Zend_View_Helper_TransParam();
        $this->_url = new Zend_View_Helper_PtxUrl();
        $this->_projectConfig = PTX_Session::getSessionValue('settings', 'project');
        $this->_locale = Zend_Registry::get('PTX_Locale');
        
        if(isset($this->_projectConfig['smtpuse']) && $this->_projectConfig['smtpuse'] == 1) {
            $config = array(
                'auth'     => $this->_projectConfig['smtpauth'],
                'username' => $this->_projectConfig['emailaddress'],
                'password' => $this->_projectConfig['smtppasswd'],
                'ssl'      => $this->_projectConfig['smtpssl'],
                'port'     => $this->_projectConfig['smtpport']);
            $transport = new Zend_Mail_Transport_Smtp($this->_projectConfig['smtphost'], $config);
            $this->setDefaultTransport($transport);
        }
        
        $this->EOL = "\r\n";
    }
    
    /**
     * Comment
     * 
     * sends an email about comment
     * @param array $formData - data from form   
     * @param array $params - additional params   
     */
    public function comment(array $formData, array $params = array()) {
        // Headers.
        $this->__headers();
        
        $params['article_link'] = $this->_url->ptxUrl(array('url'=>$params['article_data']['url_'.$params['locale']]),'article',true);
        
        // Variables.
        $subject = trim($this->_projectConfig['emailsubject'.$this->_locale].' '.$this->_trans->trans('New comment'));
        $html = $this->_commentHtml($formData,$params);
        $text = $this->_commentText($formData,$params);
        
        // Send an email.
        $this->setSubject($subject);
        $this->setFrom($formData['email'],PTX_String::escape($formData['name']));
        $this->setBodyHtml($html,$this->_charset);
        $this->setBodyText($text,$this->_charset);
        $this->addTo($this->_projectConfig['emailaddress']);
        $this->addBcc(self::DEVEL_MAIL);
        $this->send();
    }
    
    /**
     * Comment Notify.
     * 
     * sends an email notification about comment
     * @param array $commentData - data about comment   
     * @param array $params - additional params   
     */
    public function commentNotify(array $commentData, array $params = array()) {
        // Headers.
        $this->__headers();
        
        $params['article_link'] = $this->_url->ptxUrl(array('url'=>$params['article_data']['url_'.$this->_locale]),'article',true);
        
        // Variables.
        $subject = trim($this->_projectConfig['emailsubject'.$this->_locale].' '.$this->_trans->trans('Your comment has been approved'));
        $html = $this->_commentNotifyHtml($commentData,$params);
        $text = $this->_commentNotifyText($commentData,$params);
        
        // Send an email.
        $this->setSubject($subject);
        $this->setFrom($this->_projectConfig['emailaddress'],PTX_String::escape($this->_projectConfig['name'.$this->_locale]));
        $this->setBodyHtml($html,$this->_charset);
        $this->setBodyText($text,$this->_charset);
        $this->addTo($commentData['email'],PTX_String::escape($commentData['name']));
        $this->addBcc(self::DEVEL_MAIL);
        $this->send();
    }
	
	/**
	 * Contact form.
	 * 
	 * sends an email from contact form
	 * @param array $data - data from contact form	 
	 */
	public function contactForm(array $data) {
		// Headers.
        $this->__headers();
		
	    // Variables.
	    $subject = trim($this->_projectConfig['emailsubject'.$this->_locale].' '.$this->_trans->trans('Contact form'));
	    $html = $this->_contactFormHtml($data);
	    $text = $this->_contactFormText($data);
	    
	    // Send an email.
        $this->setSubject($subject);
        $this->setFrom($data['email'],PTX_String::escape($data['name']));
        $this->setBodyHtml($html,$this->_charset);
        $this->setBodyText($text,$this->_charset);
        $this->addTo($this->_projectConfig['emailaddress']);
        $this->addBcc(self::DEVEL_MAIL);
        $this->send();
	}
	
	/**
	 * Forgot login.
	 * 
	 * sends an email with new login credentials
	 * @param object $userObj - user obj
	 * @param string $passwd - password
	 */
	public function forgotLogin($userObj,$passwd) {
		// Loads user data.
		$userData = $userObj->toArray();
		
		// Headers.
        $this->__headers();
		
	    // Variables.
	    $subject = trim($this->_projectConfig['emailsubject'.$this->_locale].' '.$this->_transParam->transParam('New password for ~0~',array('~0~'=>PTX_String::escape($this->_projectConfig['name']))));
	    $html = $this->_forgotLoginHtml($userData,$passwd);
	    $text = $this->_forgotLoginText($userData,$passwd);
	    
	    // Send an email.
        $this->setSubject($subject);
        $this->setFrom($this->_projectConfig['emailaddress']);
        $this->setBodyHtml($html,$this->_charset);
        $this->setBodyText($text,$this->_charset);
        $this->addTo($userData['email']);
        $this->addBcc(self::DEVEL_MAIL);
        $this->send();
	}
	
    /**
     * Contact form Html (Private).
     * 
     * prepares body in html format
     * @param array $formData - data from form   
     * @param array $params - additional params 
     * @return string body of email
     */
    public function _commentHtml(array $formData,array $params) {
        // Html.
        $html = $this->__htmlHeader();
        
        // Info box.
        $html .= '<p style="text-align:justify;">'.$this->_trans->trans('New comment has been added.').'</p>';
        
        $html .= '<p><u>'.$this->_trans->trans('Comment').'</u></p>';
        $html .= '<div>'.nl2br($formData['message']).'</div>';
        
        $html .= '
            <table style="font-family: Georgia, \'New York CE\', utopia, serif; font-size: 13px; width: 820px; border: 1px solid #f0f0f0; margin-top: 15px;">
                <tbody>
                    <tr style="background: #f0f0f0;">
                        <td style="width: 150px;padding: 3px;">'.$this->_trans->trans('Name and Surname').'</td>
                        <td style="padding: 3px;">'.PTX_String::escape($formData['name']).'</td>
                    </tr>
                    <tr>
                        <td style="width: 150px;padding: 3px;">'.$this->_trans->trans('E-mail').'</td>
                        <td style="padding: 3px;">'.PTX_String::escape($formData['email']).'</td>
                    </tr>
                    <tr style="background: #f0f0f0;">
                        <td style="width: 150px;padding: 3px;">'.$this->_trans->trans('Website').'</td>
                        <td style="padding: 3px;">'.PTX_String::escape($formData['website']).'</td>
                    </tr>
                    <tr>
                        <td style="width: 150px;padding: 3px;">'.$this->_trans->trans('Akismet').'</td>
                        <td style="padding: 3px;">'.$params['akismet_text'].'</td>
                    </tr>
                    <tr style="background: #f0f0f0;">
                        <td style="width: 150px;padding: 3px;">'.$this->_trans->trans('IP').'</td>
                        <td style="padding: 3px;">'.$_SERVER['REMOTE_ADDR'].'</td>
                    </tr>
                </tbody>
            </table>';
        
        $html .= '
            <table style="font-family: Georgia, \'New York CE\', utopia, serif; font-size: 13px; width: 820px; border: 1px solid #f0f0f0; margin-top: 15px;">
                <tbody>
                    <tr style="background: #f0f0f0;">
                        <td style="width: 150px;padding: 3px;">'.$this->_trans->trans('Article Name').'</td>
                        <td style="padding: 3px;"><a href="http://'.$_SERVER['HTTP_HOST'].$params['article_link'].'">'.PTX_String::escape($params['article_data']['name_'.$this->_locale]).'</a></td>
                    </tr>
                    <tr>
                        <td style="width: 150px;padding: 3px;">'.$this->_trans->trans('Perex').'</td>
                        <td style="padding: 3px;">'.PTX_String::escape(strip_tags($params['article_data']['perex_'.$this->_locale])).'</td>
                    </tr>
                    <tr style="background: #f0f0f0;">
                        <td style="width: 150px;padding: 3px;">'.$this->_trans->trans('Approve').'</td>
                        <td style="padding: 3px;"><a href="http://'.$_SERVER['HTTP_HOST'].'/comment/approve/id/'.$params['comment_id'].'/code/'.$params['approval_code'].'">'.$this->_trans->trans('Approve comment').'</a></td>
                    </tr>
                    <tr>
                        <td style="width: 150px;padding: 3px;">'.$this->_trans->trans('Delete').'</td>
                        <td style="padding: 3px;"><a href="http://'.$_SERVER['HTTP_HOST'].'/comment/delete/id/'.$params['comment_id'].'/code/'.$params['approval_code'].'">'.$this->_trans->trans('Delete comment').'</a></td>
                    </tr>
                </tbody>
            </table>';
        // Footer.
        $html .= $this->__htmlFooter(true);
        
        return (string)$html;     
    }
    
    /**
     * Comment Text (Private).
     * 
     * prepares body in text format
     * @param array $formData - data from form   
     * @param array $params - additional params 
     * @return string body of email
     */
    public function _commentText(array $formData,array $params) {
        // Html.
        $html = $this->_trans->trans('New comment has been added.')."\r\n";
        $html .= "-------------------------------\r\n\r\n";
        
        $html .= $this->_trans->trans('Message')."\r\n-----------------------\r\n";
        $html .= $formData['message']."\r\n\r\n";

        $html .= $this->_trans->trans('Data about comment')."\r\n-----------------------\r\n";
        $html .= $this->_trans->trans('Name and Surname').': '.PTX_String::escape($formData['name'])."\r\n";
        $html .= $this->_trans->trans('E-mail').': '.PTX_String::escape($formData['email'])."\r\n";
        $html .= $this->_trans->trans('Website').': '.PTX_String::escape($formData['website'])."\r\n";
        $html .= $this->_trans->trans('Akismet').': '.$params['akismet_text']."\r\n";
        $html .= $this->_trans->trans('IP').': '.$_SERVER['REMOTE_ADDR']."\r\n\r\n";
        
        $html .= PTX_String::escape($params['article_data']['name_'.$this->_locale])."\r\n-----------------------\r\n";
        $html .= $this->_trans->trans('Article Url'). ' - http://'.$_SERVER['HTTP_HOST'].$params['article_link']."\r\n";
        $html .= $this->_trans->trans('Approve comment'). ' - http://'.$_SERVER['HTTP_HOST'].'/comment/approve/id/'.$params['comment_id'].'/code/'.$params['approval_code']."\r\n";
        $html .= $this->_trans->trans('Delete comment'). ' - http://'.$_SERVER['HTTP_HOST'].'/comment/delete/id/'.$params['comment_id'].'/code/'.$params['approval_code']."\r\n";

        return (string)$html;         
    }
    
/**
     * Contact form Html (Private).
     * 
     * prepares body in html format
     * @param array $commentData - data about comment  
     * @param array $params - additional params 
     * @return string body of email
     */
    public function _commentNotifyHtml(array $commentData,array $params) {
        // Html.
        $html = $this->__htmlHeader();
        
        // Info box.
        $html .= '<p style="text-align:justify;">'.$this->_trans->trans('Your comment has been approved').'</p>';
        
        $html .= '<p><u>'.$this->_trans->trans('Comment').'</u></p>';
        $html .= '<div>'.nl2br($commentData['message']).'</div>';
        
        $html .= '
            <table style="font-family: Georgia, \'New York CE\', utopia, serif; font-size: 13px; width: 820px; border: 1px solid #f0f0f0; margin-top: 15px;">
                <tbody>
                    <tr style="background: #f0f0f0;">
                        <td style="width: 150px;padding: 3px;">'.$this->_trans->trans('Name and Surname').'</td>
                        <td style="padding: 3px;">'.PTX_String::escape($commentData['name']).'</td>
                    </tr>
                    <tr>
                        <td style="width: 150px;padding: 3px;">'.$this->_trans->trans('E-mail').'</td>
                        <td style="padding: 3px;">'.PTX_String::escape($commentData['email']).'</td>
                    </tr>
                    <tr style="background: #f0f0f0;">
                        <td style="width: 150px;padding: 3px;">'.$this->_trans->trans('Website').'</td>
                        <td style="padding: 3px;">'.PTX_String::escape($commentData['website']).'</td>
                    </tr>
                </tbody>
            </table>';
        
        $html .= '
            <table style="font-family: Georgia, \'New York CE\', utopia, serif; font-size: 13px; width: 820px; border: 1px solid #f0f0f0; margin-top: 15px;">
                <tbody>
                    <tr style="background: #f0f0f0;">
                        <td style="width: 150px;padding: 3px;">'.$this->_trans->trans('Article Name').'</td>
                        <td style="padding: 3px;"><a href="http://'.$_SERVER['HTTP_HOST'].$params['article_link'].'">'.PTX_String::escape($params['article_data']['name_'.$this->_locale]).'</a></td>
                    </tr>
                    <tr>
                        <td style="width: 150px;padding: 3px;">'.$this->_trans->trans('Perex').'</td>
                        <td style="padding: 3px;">'.PTX_String::escape(strip_tags($params['article_data']['perex_'.$this->_locale])).'</td>
                    </tr>
                    <tr style="background: #f0f0f0;">
                        <td style="width: 150px;padding: 3px;">'.$this->_trans->trans('Article Url').'</td>
                        <td style="padding: 3px;"><a href="http://'.$_SERVER['HTTP_HOST'].$params['article_link'].'">'.$this->_trans->trans('Go to article').'</a></td>
                    </tr>
                </tbody>
            </table>';
        
        // Footer.
        $html .= $this->__htmlFooter(true);
        
        return (string)$html;     
    }
    
    /**
     * Comment Text (Private).
     * 
     * prepares body in text format
     * @param array $commentData - data about comment   
     * @param array $params - additional params 
     * @return string body of email
     */
    public function _commentNotifyText(array $commentData,array $params) {
        // Html.
        $html = $this->_trans->trans('Your comment has been approved')."\r\n";
        $html .= "-------------------------------\r\n\r\n";
        
        $html .= $this->_trans->trans('Message')."\r\n-----------------------\r\n";
        $html .= $formData['message']."\r\n\r\n";

        $html .= $this->_trans->trans('Data about comment')."\r\n-----------------------\r\n";
        $html .= $this->_trans->trans('Name and Surname').': '.PTX_String::escape($commentData['name'])."\r\n";
        $html .= $this->_trans->trans('E-mail').': '.PTX_String::escape($commentData['email'])."\r\n";
        $html .= $this->_trans->trans('Website').': '.PTX_String::escape($commentData['website'])."\r\n\r\n";
        
        $html .= PTX_String::escape($params['article_data']['name_'.$this->_locale])."\r\n-----------------------\r\n";
        $html .= $this->_trans->trans('Article Url'). ' - http://'.$_SERVER['HTTP_HOST'].$params['article_link']."\r\n";

        return (string)$html;         
    }
	
    /**
     * Contact form Html (Private).
     * 
     * prepares body in html format
     * @param array $data - data for email    
     * @return string body of email
     */
    public function _contactFormHtml(array $data) {
        // Html.
        $html = $this->__htmlHeader();
        
        // Info box.
        $html .= '<p style="text-align:justify;">'.$this->_trans->trans('New message has been sent from contact form.').'</p>';
        
        $html .= '<p><u>'.$this->_trans->trans('Message').'</u></p>';
        $html .= '<div>'.nl2br($data['message']).'</div>';
        
        $html .= '
            <table style="font-family: Georgia, \'New York CE\', utopia, serif; font-size: 13px; width: 820px; border: 1px solid #f0f0f0; margin-top: 15px;">
                <tbody>
                    <tr style="background: #f0f0f0;">
                        <td style="width: 150px;padding: 3px;">'.$this->_trans->trans('Name and Surname').'</td>
                        <td style="padding: 3px;">'.PTX_String::escape($data['name']).'</td>
                    </tr>
                    <tr>
                        <td style="width: 150px;padding: 3px;">'.$this->_trans->trans('E-mail').'</td>
                        <td style="padding: 3px;">'.PTX_String::escape($data['email']).'</td>
                    </tr>
                    <tr style="background: #f0f0f0;">
                        <td style="width: 150px;padding: 3px;">'.$this->_trans->trans('Phone').'</td>
                        <td style="padding: 3px;">'.PTX_String::escape($data['phone']).'</td>
                    </tr>
                </tbody>
            </table>';
        
        // Footer.
        $html .= $this->__htmlFooter(false);
        
        return (string)$html;     
    }
    
    /**
     * Contact Form Text (Private).
     * 
     * prepares body in text format
     * @param array $data - data form email
     * @return string body of email
     */
    public function _contactFormText(array $data) {
        // Html.
        $html = $this->_trans->trans('New message has been sent from contact form.')."\r\n";
        $html .= "-------------------------------\r\n\r\n";

        $html .= $this->_trans->trans('Name and Surname').': '.PTX_String::escape($data['name'])."\r\n";
        $html .= $this->_trans->trans('E-mail').': '.PTX_String::escape($data['email'])."\r\n";
        $html .= $this->_trans->trans('Phone').': '.PTX_String::escape($data['phone'])."\r\n\r\n";
        
        $html .= $this->_trans->trans('Message')."\r\n-----------------------\r\n";
        $html .= $data['message'];

        return (string)$html;         
    }
	
	/**
     * Forgot Login Html (Private).
     * 
     * prepares body in html format
     * @param array $userData - data about user
     * @param string $passwd - new password
     * @return string body of email
     */
    public function _forgotLoginHtml(array $userData,$passwd) {
        // Html.
        $html = $this->__htmlHeader();
        
        // Info box.
        $html .= '<p style="text-align:justify;">'.$this->_transParam->transParam('Your password for <strong>~0~</strong> has been reseted.',array('~0~'=>PTX_String::escape($this->_projectConfig['name'])),false).'</p>';

        $html .= '
            <table>
                <tbody>
                    <tr style="background: #f0f0f0;">
                        <td style="width: 150px;padding: 3px;">'.$this->_trans->trans('E-mail').'</td>
                        <td style="padding: 3px;">'.PTX_String::escape($userData['email']).'</td>
                    </tr>
                    <tr>
                        <td style="width: 150px;padding: 3px;">'.$this->_trans->trans('Password').'</td>
                        <td style="padding: 3px;">'.$passwd.'</td>
                    </tr>
                </tbody>
            </table>';
        
        // Footer.
        $html .= $this->__htmlFooter(true);
        
        return (string)$html;     
	}
	
    /**
     * Forgot Login Text (Private).
     * 
     * prepares body in text format
     * @param array $userData - data about user
     * @param string $passwd - new password
     * @return string body of email
     */
    public function _forgotLoginText(array $userData,$passwd) {
        // Html.
        $html = $this->_transParam->transParam('Your password for *** ~0~ *** has been reseted.',array('~0~'=>PTX_String::escape($this->_projectConfig['name'])),false)."\r\n";
        $html .= "-------------------------------\r\n\r\n";

        $html .= $this->_trans->trans('E-mail').': '.PTX_String::escape($userData['email'])."\r\n";
        $html .= $this->_trans->trans('Password').': '.$passwd."\r\n\r\n";

        $html .= $this->_transParam->transParam('We wish you good day, Team of ~0~',array('~0~'=>PTX_String::escape($this->_projectConfig['name'])))."\r\n";
        
        return (string)$html;         
    }
    
  
    /**
     * Headers.
     * 
     * adds some headers into email.
     */
    private function __headers() {
        $this->addHeader('X-MailGenerator', 'PTX CMS');
        $this->addHeader('X-greetingsTo', 'Tomas Pavlatka', true); // multiple values
    }
    
    /**
     * Html done box (Private).
     * 
     * returns code for done box.
     * @param $message - message
     * @return code.
     */
    private function __htmlDoneBox($message) {
        $box .= '<p style="border:2px solid #369; background-color:#E9F0FF;width: 805px; padding: 5px; font: 13px "Trebuchet MS", Tahoma, Verdana, Arial, Helvetica, sans-serif;line-height: 1.2em;">';
        $box .= $message;
        $box .= '</p>';
        
        return (string)$box;
    }
    
    /**
     * Html footer (Private).
     * 
     * returns html footer for email
     * @params $info - added info message.
     * @returm html footer 
     */
    private function __htmlFooter($info = false) {
        
        $code = null;
        if($info) {
            $code .= '<p style="text-align:justify;">'.$this->_transParam->transParam('We wish you good day, Team of ~0~',array('~0~' => PTX_String::escape($this->_projectConfig['name'.$this->_locale]))).'</p>';
        }
        
        $code .= '</body></html>';
        
        return (string)$code;
    }
    
    /**
     * Html header (Private).
     * 
     * returns html header for email
     * @returm html header 
     */
    private function __htmlHeader() {
        return '<html><body style="font-family: Georgia, \'New York CE\', utopia, serif; font-size: 13px; width: 820px;">';
    }
    
    /**
     * Html info box (Private).
     * 
     * returns code for info box.
     * @param $message - message
     * @return code.
     */
    private function __htmlInfoBox($message) {
        $box  = '<p style="border:2px solid #BBDF8D; background-color:#EAF7D9;width: 805px; padding: 5px; font: 13px "Trebuchet MS", Tahoma, Verdana, Arial, Helvetica, sans-serif;line-height: 1.2em;">';
        $box .= $message;
        $box .= '</p>';
        
        return (string)$box;
    }
}