<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 24.7.2010
**/

class Admin_IndexController extends Zend_Controller_Action {
    
    /************** VARIABLES **************/
    private $_user;
    
    /*************** PUBLIC FUNCTION ****************/
    public function init() {
        /* Initialize action controller here */
        $this->view->h1 = $this->view->trans('Admin');
        
        $response = $this->getResponse();
        $response->insert('menuTop',$this->view->render('_menu/top.phtml'));
        $response->insert('incTop',$this->view->render('_inc/top-row.phtml'));
        $response->insert('incLeft',$this->view->render('_inc/left.phtml'));
        
        $this->_user = Zend_Auth::getInstance()->getStorage()->read();
    }
    
    /**
     * index
     */
    public function indexAction() {
        // page setting
        $this->view->h2 = $this->view->trans('Dashboard');
        $this->view->headTitle($this->view->h1.' - '.$this->view->h2, 'PREPEND');
        
        $this->_messages();
        
        PTX_Anchor::createAnchor('admin');
        $this->view->msgs = PTX_Message::getMessage4User("admin");
    }
    
    /*************** PRIVATE FUNCTION ****************/
    /**
     * pripravi widget messages
     * @return unknown_type
     */
    private function _messages() {
        // pripravime si dbsql
        $select = Admin_Model_DbSelect_Messages::pureSelect();
        $select->columns(array('id','user_id_from','subject','created','read'))->where('status > -1')->where('user_id_to = ?',(int)$this->_user->id);
        $select->order('created desc')->limit(10);
        $stmt = $select->query();
        $messages = $stmt->fetchAll();
    
        foreach($messages as &$row) {
            $userObj = new Admin_Model_User($row['user_id_from']);
            $row['author'] = $userObj->getData();
        }
        
        $this->view->messages = $messages;
    }
}