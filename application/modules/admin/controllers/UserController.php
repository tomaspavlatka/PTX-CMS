<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 24.7.2010
**/

class Admin_UserController extends Zend_Controller_Action {    
    
    /************** VARIABLES **************/
    private $_user;
    
    /************** PUBLIC FUNCTION **************/
    /**
     * Init.
     */
    public function init() {
        $this->view->h1 = $this->view->trans('Admin users');
        
        $response = $this->getResponse();
        $response->insert('menuTop',$this->view->render('_menu/top.phtml'));
        $response->insert('incTop',$this->view->render('_inc/top-row.phtml'));
        $response->insert('incLeft',$this->view->render('_inc/left.phtml'));
        
        PTX_Anchor::set($this->getRequest());
        
        $this->_user = Zend_Auth::getInstance()->getStorage()->read();
    }

    /**
     * Add.
     */
    public function addAction() {
        // Form.
        $options = array(
            'user_role' => $this->_user->role,
        );
        $form = new Admin_Form_User($options);
        $this->view->form = $this->_addForm($this->getRequest(),$form);
        
        // Final view.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('New user');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Delete.      
     */
    public function deleteAction() {
        // Data.
        $id = $this->_getParam('id');
        $userObj = new Admin_Model_User($id);
        $data = $userObj->getData();
        
        // Verification. 
        if(!isset($data->status) || $data->status < 0) {
            $this->_redirect('/admin');
        } 
        
        // smazem 
        $rows = $userObj->delete();
        
        // Message + Redirect.
        if($rows > 0) {
            PTX_Message::setMessage4User('admin','done',$this->view->transParam('User <b>~0~</b> has been deleted.',array('~0~'=>$data->name),false));
        } else {
            PTX_Message::setMessage4User('admin','warning',$this->view->transParam('User <b>~0~</b> could not be deleted.',array('~0~'=>$data->name),false));
        }
        $this->_redirect(PTX_Anchor::getAnchor('admin'));
    }

    /**
     * Detail.     
     */
    public function detailAction() {
        // Data.
        $id = $this->_getParam('id');
        $userObj = new Admin_Model_User($id);
        $data = $userObj->getData();
        
        // Verification. 
        if(!isset($data->status) || $data->status < 0) {
            $this->_redirect('/admin');
        } else if($this->_user->role != 'sadmins' && $id != $this->_user->id) {
            $this->_redirect('/admin');
        }
        
        // View variables.
        $this->view->data = $data;
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page settings.
        $this->view->h2 = $this->view->trans('Detail');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
        
        // Anchor.
        PTX_Anchor::createAnchor('admin');
    }
    
    /**
     * Edit.     
     */
    public function editAction() {
        // Data.
        $id = $this->_getParam('id');
        $userObj = new Admin_Model_User($id);
        $data = $userObj->getData();
        
        // Verification. 
        if(!isset($data->status) || $data->status < 0) {
            $this->_redirect('/admin/');
        } else if($this->_user->role != 'sadmins' && $id != $this->_user->id) {
            $this->_redirect('/admin');
        }
        
        // Form.
        $options = array(
            'id' => $id,
            'user_role' => $this->_user->role,
        );
        $form = new Admin_Form_User($options);
        $this->view->form = $this->_editForm($this->getRequest(),$form,$userObj);
        
        // View variables.
        $this->view->data = $data;
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('Edit_2');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Index.
     */
    public function indexAction() {
        $this->_redirect('/admin/user/list');
    }
    
    /**
     * List.     
     */
    public function listAction() {
        // Parameters.
        $params = array(
            'sort'   => $this->_getParam('sort','name'),
            'role'   => $this->_getParam('role',null),
            'input'  => $this->_getParam('input',25),
            'search' => $this->_getParam('search',null));
        
        // Zend_Db_Select.
        $select = Admin_Model_DbSelect_Users::pureSelect();
        $select->columns(array('id','email','name','role','status'))->where('status > -1');
        $this->_completeSelectList($select,$params);
        
        // Paginator.
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setItemCountPerPage($params['input'])->setCurrentPageNumber($this->_getParam('page',1));
        $this->view->paginator = $paginator;
        
        // Final view.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        $this->view->sortPossibilities = $this->_getSortPossibilities();
        $this->view->userRoles = $this->_getRoles();
        $this->view->params = $params;
        
        // Anchor.
        PTX_Anchor::createAnchor('admin');
        
        // Page setting.
        $this->view->h2 = $this->view->trans('List');
        $this->view->headTitle($this->view->h1.' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Password.
     */
    public function passwordAction() {
        // Data.
        $id = $this->_getParam('id');
        $userObj = new Admin_Model_User($id);
        $data = $userObj->getData();
        
        // Verification. 
        if(!isset($data->status) || $data->status < 0) {
            $this->_redirect('/admin/');
        } else if($this->_user->role != 'sadmins' && $id != $this->_user->id) {
            $this->_redirect('/admin');
        }
        
        // Form.
        $form = new Admin_Form_UserPasswd();
        $this->view->form = $this->_passwdForm($this->getRequest(),$form,$userObj);
        
        // View variables.
        $this->view->data = $data;
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('Password');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Transform. 
     */
    public function transformAction() {
        $link = '/admin/user/list';
        
        if($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();

            if(isset($data['role']) && !empty($data['role'])) {
                $link .= '/role/'.urlencode($data['role']);
            }
            
            if(isset($data['sort']) && !empty($data['sort'])) {
                $link .= '/sort/'.urlencode($data['sort']);
            }
            
            if(isset($data['input']) && !empty($data['input'])) {
                $link .= '/input/'.urlencode($data['input']);
            }
            
            if(isset($data['search']) && !empty($data['search']) && $data['search'] != $this->view->trans('Search')) {
                $link .= '/search/'.urlencode($data['search']);
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
                $correct = true;
                
                // User exists.
                $usersObj = new Admin_Model_Users();
                if($usersObj->userExists($formData['email'])) {
                    PTX_Message::setMessage4User('admin','warning',$this->view->transParam('Email <b>~0~</b> is already registered in our database.',array('~0~'=>$formData['email']),false));
                    $correct = false;
                }
                
                // Correct.
                if($correct) {
                    $idUser = $usersObj->save($formData);
                    
                    if($formData['continue'] == 0) {
                        $this->_redirect('/admin/user/detail/user/'.(int)$idUser);
                    } else {
                        PTX_Message::setMessage4User('admin','done',$this->view->transParam('User <b>~0~</b> has been stored.',array('~0~'=>$formData['personname']),false));
                        $form->populate($formData);
                    }
                } else {
                    $form->populate($formData);    
                }
            } else {
                $form->populate($form->getValues());
            }
        } 
        
        return $form;
    }
    
    /**
     * Complete select.
     * 
     * complete select Zend_Deb_Select
     * @param Zend_Deb_Select $select - select
     * @param $params - params
     */
    private function _completeSelectList(Zend_Db_Select $select,$params) {
        // Role.
        if(!empty($params['role'])) {
            $select->where('role = ?',$params['role']);
        }
        
        // Sort.
        if(!empty($params['sort'])) {
            switch($params['sort']) {
                case 'name':
                    $select->order('name asc');
                    break;
                case 'name2':
                    $select->order('name desc');
                    break;                   
                case 'role':
                    $select->order('role asc');
                    break;              
                case 'role2':
                    $select->order('role desc');
                    break;                            
            }
        }
        
        // Search.
        if(!empty($params['search'])) {
            $string = urldecode($params['search']);
            $select->where('email LIKE ? OR name LIKE ?',"%{$string}%");
        }
    }
    
    /**
     * Edit form.
     * 
     * manages form
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Form $form
     * @param Admin_Model_User $userObj
     * @return Zend_Form
     */
    private function _editForm(Zend_Controller_Request_Http $request, Zend_Form $form, Admin_Model_User $userObj) {
        $data = $userObj->getData();
        
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                $formData = $form->getValues();
                $correct = true;
                
                // User exists.
                $usersObj = new Admin_Model_Users();
                if($usersObj->userExists($formData['email'],$data->id)) {
                    PTX_Message::setMessage4User('admin','error',$this->view->trans('Email is allready in our database.'));
                    $correct = false;
                }
                
                // Correct.
                if($correct) {
                    // Small hack which allows user to edit their profile.
                    if($this->_user->role != 'sadmins') {
                       $formData['role'] = $this->_user->role;
                       $formData['status'] = $this->_user->status; 
                    }
                    
                    $userObj->update($formData);
                    PTX_Message::setMessage4User('admin','done',$this->view->transParam('User <b>~0~</b> has been updated.',array('~0~'=>$formData['personname']),false));
                    $this->_redirect(PTX_Anchor::getAnchor('admin'));
                } else {
                    $form->populate($formData);    
                }
            }
        } else {
            $populateArray = array(
               'role'        => $data->role,
               'email'       => $data->email,
               'personname'  => $data->name,
               'status'      => $data->status,
               'locale'      => $data->locale);
               
            $form->populate($populateArray);
        }
        
        return $form;
    }
    
    /**
     * Get sort possibilities.
     * 
     * returns possibilities for sort option
     * @return sort possibilities
     */
    private function _getSortPossibilities() {
        $possible = array(
            'name'   => $this->view->trans('Name A-Z'),
            'name2'  => $this->view->trans('Name Z-A'),
            'role'   => $this->view->trans('Role A-Z'),
            'role2'  => $this->view->trans('Role Z-A'),
        );
        
        return $possible;
    }
    
    /**
     * Get roles.
     * 
     * returns possible roles to be set up
     * @return possible roles
     */
    private function _getRoles() {
        $possible = array(
            'users'   => $this->view->trans('Users'),
            'sadmins'  => $this->view->trans('Super Admins'),
            'admins'  => $this->view->trans('Admins'));
        
        return $possible;
    }
    
    /**
     * Passwd form.
     * 
     * manages form
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Form $form
     * @param Admin_Model_User $userObj
     * @return Zend_Form
     */
    private function _passwdForm(Zend_Controller_Request_Http $request, Zend_Form $form, Admin_Model_User $userObj) {
        $data = $userObj->getData();
        
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                $formData = $form->getValues();
                $correct = true;
                
                // Passwords save.
                if($formData['password'] != $formData['passwordconfirm']) {
                    PTX_Message::setMessage4User('admin','error',$this->view->trans('Passwords must be same.'));
                    $correct = false;
                }
                
                // Correct.
                if($correct) {
                    $userObj->updatePasswd($formData['password']);
                    PTX_Message::setMessage4User('admin','done',$this->view->trans('Password has been updated.'));
                    $this->_redirect(PTX_Anchor::getAnchor('admin'));
                } else {
                    $form->populate($formData);    
                }
            }
        } 
        
        return $form;
    }
}