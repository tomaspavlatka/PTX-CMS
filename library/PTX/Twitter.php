<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 24.6.2010
**/

class PTX_Twitter {
    
    /************ VARIABLES *************/
    private $_username;
    private $_password;
    private $_twitter;
    
    /************ CONSTRUCTOR *************/
    /**
     * konstruktor
     * @param $username - uzivat jmeno
     * @param $password - heslo     
     */
    public function __construct($username,$password) {
        $this->_username = $username;
        $this->_password = $password;
        
        $this->_twitter = new Zend_Service_Twitter($this->_username,$this->_password);
        
        if(!$this->_twitter->account->verifyCredentials()) {
            throw new PTX_Exception(__CLASS__.": Problem with credentials for Twitter");
        }
    }
    
    /**
     * destruktor     
     */
    public function __destruct() {
        // ukonci session pro Twitter
        $this->_twitter->account->endSession();
    }
    
    /************ PUBLIC FUNCTION *************/
    /**
     * vrati mi posledni zaznamy, kt jsem poslal
     * @param $page - strana
     * @param $count - pocet
     * @return data
     */
    public function getPosts($page = 1, $count = 10) {
        $params = array('page'=>(int)$page,'count'=>(int)$count);

        return $this->_twitter->status->userTimeline($params);
    }
    
    /************ PUBLIC STATIC FUNCTION *************/
    /**
     * prepise datum z twitteru na mktime
     * @param $date - datum
     * @return mktime
     */
    public static function transformDate($date) {
        // Wed Jun 23 04:18:05 +0000 2010
        $array = explode(' ',$date);
        $time = explode(':',$array[3]);
        
        return mktime($time[0],$time[1],$time[2],self::_getMonthNumber($array[1]),$array[2],$array[5]);
    }
    
    /**
     * vrati mesic jako cislici
     * @param $month - zkratka mesice
     * @return cislice
     */
    public static function _getMonthNumber($month) {
        switch($month) {
            case 'Jan':
                return 1;
            case 'Feb':
                return 2;
            case 'Mar':
                return 3;
            case 'Apr':
                return 4;
            case 'May':
                return 5;
            case 'Jun':
                return 6;
            case 'Jul':
                return 7;
            case 'Aug':
                return 8;
            case 'Sep':
                return 9;
            case 'Oct':
                return 10;
            case 'Nov':
                return 11;
            case 'Dec':
                return 12;
        }        
    }
}