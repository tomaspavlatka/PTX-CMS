<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 25.7.2010
**/

class Admin_MessageController extends Zend_Controller_Action {
    
    /************** VARIABLES **************/
    private $_user;
    
    /************** PUBLIC FUNCTION **************/
    public function init() {
        $this->view->h1 = $this->view->trans('Messages');
        
        $response = $this->getResponse();
        $response->insert('menuTop',$this->view->render('_menu/top.phtml'));
        $response->insert('incTop',$this->view->render('_inc/top-row.phtml'));
        $response->insert('incLeft',$this->view->render('_inc/left.phtml'));
        
        $this->_user = Zend_Auth::getInstance()->getStorage()->read();
    }

    /**
     * Add.
     */
    public function addAction() {
        // Form.
        $options = array(
            'id_user' => $this->_user->id
        );
        $form = new Admin_Form_Message($options);
        $this->view->form = $this->_addForm($this->getRequest(),$form);
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('New message');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Control.     
     */
    public function controlAction() {
        // Parameters.
        $params = array(
            'sort'   => $this->_getParam('sort','date2'),
            'input'  => $this->_getParam('input',25),
            'user'   => $this->_getParam('user',null),
            'userto' => $this->_getParam('userto',null),
            'search' => $this->_getParam('search',null));
        
        // Zend_Db_Select.
        $select = Admin_Model_DbSelect_Messages::pureSelect();
        $select->columns(array('id','user_id_from','user_id_to','subject as name','created','read'))->where('status > -1');
        $this->_completeSelectList($select,$params,'control');
        
        // Paginator.
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setItemCountPerPage($params['input'])->setCurrentPageNumber($this->_getParam('page',1));
        foreach($paginator as &$row) {
            $userObj = new Admin_Model_User($row['user_id_from']);
            $userData = $userObj->getData();
            $row['author'] = (isset($userData->name)) ? $userData->name : $this->view->trans('System');
            
            $userObj = new Admin_Model_User($row['user_id_to']);
            $userData = $userObj->getData();
            $row['sentto'] = (isset($userData->name)) ? $userData->name : $this->view->trans('System');
        }
        $this->view->paginator = $paginator;
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        $this->view->sortPossibilities = $this->_getSortPossibilities();
        $this->view->users = $this->_getUsers(false);
        $this->view->params = $params;
        
        // page setting.
        $this->view->h2 = $this->view->trans('Control');
        $this->view->headTitle($this->view->h1.' - '.$this->view->h2, 'PREPEND');
        
        // Anchor.
        PTX_Anchor::createAnchor('admin');
    }
    
    /**
     * Detail.     
     */
    public function detailAction() {
        // Data.
        $id = $this->_getParam('id');
        $messageObj = new Admin_Model_Message($id);
        $data = $messageObj->getData();
        
        // Verification. 
        if(!isset($data->status) || $data->status < 0) {
            $this->_redirect('/admin');
        } 
        
        // Author.
        $userObj = new Admin_Model_User($data['user_id_from']);
        $this->view->author = $userObj->getData();
        
        // Mark as read.
        if($data->user_id_to == $this->_user->id) {
            $messageObj->markAsRead();
        }
            
        // View variables.
        $this->view->nextMessage = $messageObj->getNeighborMessage('next');
        $this->view->previousMessage = $messageObj->getNeighborMessage('previous');
        $this->view->data = $data;
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('Detail');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
        
        // Anchor.
        PTX_Anchor::createAnchor('admin');
    }
    
    /**
     * Forward.
     */
    public function forwardAction() {
        // Data.
        $id = $this->_getParam('id');
        $messageObj = new Admin_Model_Message($id);
        $data = $messageObj->getData();
        
        // Verification. 
        if(!isset($data->status) || $data->status < 0) {
            $this->_redirect('/admin');
        } 
        
        $this->_redirect('/admin/message/add/id/'.(int)$id.'/type/forward');
    }
    
    /**
     * Index.
     */
    public function indexAction() {
        $this->_redirect('/admin/message/list');
    }
    
    /**
     * List.     
     */
    public function listAction() {
        // Parameters.
        $params = array(
            'sort'   => $this->_getParam('sort','date2'),
            'input'  => $this->_getParam('input',25),
            'user'   => $this->_getParam('user',null),
            'search' => $this->_getParam('search',null));
        
        // Zend_Db_Select
        $select = Admin_Model_DbSelect_Messages::pureSelect();
        $select->columns(array('id','user_id_from','subject as name','created','read'))->where('status > -1')->where('user_id_to = ?',(int)$this->_user->id);
        $this->_completeSelectList($select,$params,'list');
        
        // Paginator.
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setItemCountPerPage($params['input'])->setCurrentPageNumber($this->_getParam('page',1));
        foreach($paginator as &$row) {
            $userObj = new Admin_Model_User($row['user_id_from']);
            $userData = $userObj->getData();
            $row['author'] = (isset($userData->name)) ? $userData->name : $this->view->trans    ('System');
        }
        $this->view->paginator = $paginator;
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        $this->view->sortPossibilities = $this->_getSortPossibilities();
        $this->view->users = $this->_getUsers();        
        $this->view->user = $this->_user;
        $this->view->params = $params;
        
        // Page settings.
        $this->view->h2 = $this->view->trans('List');
        $this->view->headTitle($this->view->h1.' - '.$this->view->h2, 'PREPEND');
        
        // Anchor.
        PTX_Anchor::createAnchor('admin');
    }

    /**
     * Reply.
     */
    public function replyAction() {
        // Data.
        $id = $this->_getParam('id');
        $messageObj = new Admin_Model_Message($id);
        $data = $messageObj->getData();
        
        // Verification. 
        if(!isset($data->status) || $data->status < 0) {
            $this->_redirect('/admin');
        } 
        
        $this->_redirect('/admin/message/add/id/'.(int)$id.'/user/'.(int)$data->user_id_from.'/type/reply');
    }
    
    /**
     * Sent.     
     */
    public function sentAction() {
        // Parameters.
        $params = array(
            'sort'   => $this->_getParam('sort','date2'),
            'input'  => $this->_getParam('input',25),
            'user'   => $this->_getParam('user',null),
            'search' => $this->_getParam('search',null));
        
        // Zend_Db_Select
        $select = Admin_Model_DbSelect_Messages::pureSelect();
        $select->columns(array('id','user_id_to','subject as name','created','read'))->where('status > -1')->where('user_id_from = ?',(int)$this->_user->id);
        $this->_completeSelectList($select,$params,'sent');
        
        // Paginator.
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setItemCountPerPage($params['input'])->setCurrentPageNumber($this->_getParam('page',1));
        foreach($paginator as &$row) {
            $userObj = new Admin_Model_User($row['user_id_to']);
            $userData = $userObj->getData();
            $row['author'] = (isset($userData->name)) ? $userData->name : $this->view->trans('System');
        }
        $this->view->paginator = $paginator;
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        $this->view->sortPossibilities = $this->_getSortPossibilities();
        $this->view->users = $this->_getUsers();        
        $this->view->user = $this->_user;
        $this->view->params = $params;
        
        // Page settings.
        $this->view->h2 = $this->view->trans('List');
        $this->view->headTitle($this->view->h1.' - '.$this->view->h2, 'PREPEND');
        
        // Anchor.
        PTX_Anchor::createAnchor('admin');
    }
    
    /**
     * Transform. 
     */
    public function transformAction() {
        $link = '/admin/message/';
        
        if($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            
            if(isset($data['action']) && !empty($data['action'])) {
                $link .= urlencode(trim($data['action']));
            } else {
                $link .= 'list';
            }            
            
            if(isset($data['sort']) && !empty($data['sort'])) {
                $link .= '/sort/'.urlencode(trim($data['sort']));
            }
            
            if(isset($data['input']) && !empty($data['input'])) {
                $link .= '/input/'.urlencode(trim($data['input']));
            }
            
            if(isset($data['user']) && !empty($data['user'])) {
                $link .= '/user/'.(int)trim($data['user']);
            }
            
            if(isset($data['userto']) && !empty($data['userto'])) {
                $link .= '/userto/'.(int)trim($data['userto']);
            }
            
            if(isset($data['search']) && !empty($data['search']) && $data['search'] != $this->view->trans('Search')) {
                $link .= '/search/'.urlencode(trim($data['search']));
            }
        }
               
        $this->_redirect($link);
    }
    /************** PRIVATE FUNCTION **************/
    /**
     * Add form.
     * 
     * manages form
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Form $form
     * @return Zend_Form
     */
    private function _addForm(Zend_Controller_Request_Http $request, Zend_Form $form) {
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                $formData = $form->getValues();
                
                $messagesObj = new Admin_Model_Messages();
                $idMessage = $messagesObj->save($formData,$this->_user->id);

                if($formData['continue'] == 0) {
                    $this->_redirect('/admin/message/detail/message/'.(int)$idMessage);
                } else {
                    $userObj = new Admin_Model_User($formData['to']);
                    $userData = $userObj->getData();
                    
                    $name = $userData->name .' ('.$userData->email.')';
                    PTX_Message::setMessage4User('admin','done',$this->view->transParam('Message to <b>~0~</b> has been sent.',array('~0~'=>$name),false));
                    $form->populate($form->getValues());
                }
            } else {
                $form->populate($form->getValues());
            }
        } else {
            $idMessage = $request->getParam('id');
            $idUser = $request->getParam('user');
            $type = $request->getParam('type');
            
            $populate = array('to'=>$request->getParam('user'));
            if(!empty($idMessage)) {
                $messageObj = new Admin_Model_Message($idMessage);
                $messageData = $messageObj->getData();
                
                if($messageData->user_id_to == $this->_user->id) {
                    
                    if($type == 'reply') {
                        if(substr($messageData->subject,0,3) != 'Re:') { 
                            $populate['subject'] = 'Re: '.$messageData->subject;
                        } else {
                            $populate['subject'] = $messageData->subject;
                        }
                    } else if($type == 'forward') {
                        if(substr($messageData->subject,0,3) != 'Fw:') { 
                            $populate['subject'] = 'Fw: '.$messageData->subject;
                        } else {
                            $populate['subject'] = $messageData->subject;
                        }
                    } else {
                        $populate['subject'] = $messageData->subject;
                    }                            
                    $populate['message'] = $messageData->message; 
                }
            }
            
            $form->populate($populate);
        }
        
        return $form;
    }
    
    /**
     * Complete select.
     * 
     * completes Zend_Db_Select instance settings
     * @param Zend_Db_Select $select - select
     * @param $params - params
     * @param $type - type of list
     */
    private function _completeSelectList(Zend_Db_Select $select,$params, $type) {
        // Sort.
        if(!empty($params['sort'])) {
            switch($params['sort']) {
                case 'date':
                    $select->order('created asc');
                    break;
                case 'date2':
                    $select->order('created desc');
                    break;
                case 'subject':
                    $select->order('subject asc');
                    break;
                case 'subject2':
                    $select->order('subject desc');
                    break;                   
            }
        }
        
        // Search.
        if(!empty($params['search'])) {
            $string = urldecode($params['search']);
            $select->where('subject LIKE ? OR message LIKE ?',"%{$string}%");
        }
        
        // User.
        if(!empty($params['user'])) {
            if($type == 'list') {
                $select->where('user_id_from = ?',(int)$params['user']);
            } else if($type == 'sent') {
                $select->where('user_id_to = ?',(int)$params['user']);
            } else if($type == 'control') {
                $select->where('user_id_from = ?',(int)$params['user']);
            }
        }
        
        // User to.
        if(!empty($params['userto'])) {
            if($type == 'control') {
                $select->where('user_id_to = ?',(int)$params['userto']);
            }
        }
    }
    
    /**
     * Get sort possibilities.
     * 
     * return possible option for sort
     * @return possible option for sort
     */
    private function _getSortPossibilities() {
        $possible = array(
            'date'   => $this->view->trans('Date 01.01.-31.12.'),
            'date2'  => $this->view->trans('Date 31.12.-01.01.'),
            'subject'   => $this->view->trans('Subject A-Z'),
            'subject2'  => $this->view->trans('Subject Z-A'),
        );
        
        return $possible;
    }
    
    /**
     * Get users.
     * 
     * returns all users
     * @param $excludeMe - logged user should be exluded
     * @return users
     */
    private function _getUsers($excludeMe = true) {
        // Zend_Db_Select
        $select = Admin_Model_DbSelect_Users::pureSelect();
        $select->columns(array('id','name','email'))->where('status > -1')->order('name asc');
        
        // Exlude. 
        if($excludeMe) {
            $select->where('id != ?',$this->_user->id);
        }
        
        $stmt = $select->query();
        $data = $stmt->fetchAll();
        
        $users = array();
        foreach($data as $user) {
            $users[$user['id']] = $user['name'] .' ('.$user['email'].')';
        }
        
        return $users;
    } 
}