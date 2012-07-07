<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 25, 2010
**/

// Require classes.
require APPLICATION_PATH .'/helpers/TransParam.php';

class Zend_View_Helper_Twitter {
    
    /**
     * Username
     */
    private $_username;
    
    /**
     * Options.
     */
    private $_options;
    
    /**
     * Options.
     */
    private $_twitterObj;
    
    /**
     * Trans params.
     */
    private $_transParam;
    
    
    /**
     * Twitter.
     * 
     * main function of class.
     * @param $action - what should be done
     * @param $options - options
     * @return code to be shown
     */
    public function twitter($action, array $options) {
        $this->_options = $options;
        $this->_username = (isset($options['username'])) ? $options['username'] : null;
        $this->_transParam = new Zend_View_Helper_TransParam();

        if($this->_createObject()) {
            switch($action) {
                case 'usertimeline':
                    $data = $this->_userTimeLine();
                    break;
            }
        }
        
        // We have data
        if(isset($data)) {
            return $this->_createCode($data);
        }
    }
    
    /**
     * Create code.
     * 
     * creates html code to be shown on page
     * @param $data
     * @return html code to be shown
     */
    private function _createCode($data) {
        $code = null;
        
        foreach($data->status as $status) {
            $code .= '<p class="item">';
                $code .= '<span class="date full-date tb"><abbr class="published" title="'.$status->created_at.'">'.date('d/m/Y',strtotime($status->created_at)).'</abbr></span> ';
                $code .=  '<span class="alt-font">'.htmlspecialchars($status->text).'</span>&nbsp;<a href="http://twitter.com/pavlatom/status/'.$status->id.'" target="_blank" title="Tomáš Pavlátka: '.htmlspecialchars($status->text).'">...&nbsp;&#187;</a>';
            $code .= '</p>';
        }
        
        $code .= '<p class="t-right">';
            $rss = '<a href="http://twitter.com/statuses/user_timeline/61483005.rss" title="'.$this->_transParam->transParam('Tomas Pavlatka\'s Twitter RSS',array()).'">RSS</a>';
            $twitter = '<a href="http://twitter.com/pavlatom/" title="'.$this->_transParam->transParam('Tomas Pavlatka\'s Twitter',array()).'">'.$this->_transParam->transParam('Profile',array()).'</a>';
            
            //$code .= '<span class="alt-font">More tweets:</span> '.$rss.' - '.$twitter;
            $code .= $this->_transParam->transParam('<span class="alt-font">More tweets:</span> ~0~ - ~1~',array('~0~'=>$rss,'~1~'=>$twitter),false,false);
        $code .= '</p>';
        
        //Zend_Debug::dump($data);
        return $code;
    }
    
    /**
     * Create object.
     * 
     * creates object for Twitter.
     * @return true | false - (false)
     */
    private function _createObject() {
        // 1) We have config file.
        if(isset($this->_options['config']) && file_exists($this->_options['config'])) {            
            // 2) We have username.
            if(!empty($this->_username)) {
                $token = file_get_contents($this->_options['config']);   

                try{
                    // Twitter obj.
                    $this->_twitterObj = new Zend_Service_Twitter(array(
                        'username' => $this->_username,
                        'accessToken' => unserialize($token)
                    ));
                    
                    if($this->_twitterObj->account->verifyCredentials()) {
                        return true;
                    }
                } catch(Exception $e) {}
            }
        }
        
        // Default return.
        return false;
    }
    
    /**
     * User time line.
     * 
     * returns user time line for user.
     * @return data from twitter
     */
    private function _userTimeLine() {
        // Params.
        $params = array(
            'count' => (isset($this->_options['limit'])) ? (int)$this->_options['limit'] : 5,
            'include_entities' => 1,
        );
        
        return $this->_twitterObj->status->userTimeline($params);
    }
}