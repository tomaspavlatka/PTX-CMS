<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 1.8.2010
**/

class Admin_View_Helper_UserName {
    
    /************** PUBLIC FUNCTION ***************/
    /**
     * vrati nazev bezneho problemu
     * @param $id - id
     * @return unknown_type
     */
    public function userName($id) {
        $userObj  = new Admin_Model_User($id);
        $userData = $userObj->getData();
        
        return (isset($userData->name)) ? $userData->name : null;
    }
}