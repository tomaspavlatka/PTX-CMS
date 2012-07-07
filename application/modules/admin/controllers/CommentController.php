<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 10.11.2010
**/

class Admin_CommentController extends Zend_Controller_Action {
    
    /************** PUBLIC FUNCTION **************/
    /** 
     * Init.
     */
    public function init() {
        $this->view->h1 = $this->view->trans('Comments');
        
        $response = $this->getResponse();
        $response->insert('menuTop',$this->view->render('_menu/top.phtml'));
        $response->insert('incTop',$this->view->render('_inc/top-row.phtml'));
        $response->insert('incLeft',$this->view->render('_inc/left.phtml'));
        
        PTX_Anchor::set($this->getRequest());
    }
    
    /**
     * Delete.     
     */
    public function deleteAction() {
        // Data.
        $idComment = $this->_getParam('id');
        $commentObj = new Admin_Model_Comment($idComment);
        $commentData = $commentObj->getData();
        
        // pokud tu nemame co delat 
        if(!isset($commentData->status) || $commentData->status < 0) {
            $this->_redirect('/admin');
        } 
        
        // Delete.
        $rows = $commentObj->delete();
        
        // Message + Redirect
        if($rows > 0) {
            PTX_Message::setMessage4User('admin','done',$this->view->trans('Comment has been deleted.'));
        } else {
            PTX_Message::setMessage4User('admin','done',$this->view->trans('Comment could not be deleted.'));
        }
        $this->_redirect(PTX_Anchor::getAnchor('admin'));
    }
    
    /**
     * Edit.     
     */
    public function editAction() {
        // Data.
        $idComment = $this->_getParam('id');
        $commentObj = new Admin_Model_Comment($idComment);
        $commentData = $commentObj->getData();
        
        // Verification. 
        if(!isset($commentData->status) || $commentData->status < 0) {
            $this->_redirect('/admin');
        } 
        
        // Form
        $options = array(
            'parent_type' => $commentData->parent_type,
            'parent_id' => $commentData->parent_id,
        );
        $form = new Admin_Form_Comment($options);
        $this->view->form = $this->_editForm($this->getRequest(),$form,$commentObj);
        
        // View variables.
        $this->view->commentData = $commentData;
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page settings.
        $this->view->h2 = $this->view->trans('Edit_2');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Index.     
     */
    public function indexAction() {
        $this->_redirect('/admin');
    }
    
    /**
     * List     
     */
    public function listAction() {
        // Parameters.
        $idParent = $this->_getParam('id',null);
        $parentType = $this->_getParam('type',null);
        
        // Verification
        if(empty($idParent) || empty($parentType)) {
            $this->_redirect('/admin');
        }
        
        // Comments.
        $treeObj = new Admin_Model_Tree_Comment();
        $comments = $treeObj->getTree(0,0," > -1",$parentType,$idParent);
        $comments4View = array();
        foreach($comments as $row) {
            $rowData = array(
                'id' => $row['id'],
                'name' => str_repeat('-- ',$row['level']).$row['personname'],
                'message' => $row['message'],
                'status' => $row['status'],
            );
            $comments4View[] = $rowData;
        }
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        $this->view->comments = $comments4View;
        $this->view->parentData = $this->_getParentData($parentType,$idParent);
        $this->view->params = $this->_getAllParams();
        
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
        $idComment = $this->_getParam('id');
        $commentObj = new Admin_Model_Comment($idComment);
        $commentData = $commentObj->getData();
        
        // Verification.
        if(!isset($commentData->status) || $commentData->status < 0) {
            $this->_redirect('/admin');
        } 
        
        // Form.
        $options = array(
            'parent_type' => $commentData->parent_type,
            'parent_id' => $commentData->parent_id,
        );
        $form = new Admin_Form_Comment($options);
        $this->view->form = $this->_replyForm($this->getRequest(),$form,$commentObj);
        
        // View variables.
        $this->view->commentData = $commentData;
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('Reply');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /************** PRIVATE FUNCTION **************/
    /**
     * Edit form.
     * 
     * manages form
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Form $form
     * @param Admin_Model_Comment $commentObj
     * @return Zend_Form
     */
    private function _editForm(Zend_Controller_Request_Http $request, Zend_Form $form, Admin_Model_Comment $commentObj) {
        $data = $commentObj->getData();
        
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                $formData = $form->getValues();
                $commentObj->update($formData);

                PTX_Message::setMessage4User('admin','done',$this->view->trans('Comment has been updated.'));
                $this->_redirect(PTX_Anchor::getAnchor('admin'));
            }
        } else {
            $populateArray = array(
                'name'         => $data->personname,
                'message'      => $data->message,
                'parent'    => $data->comment_id);
               
            $form->populate($populateArray);
        }
        
        return $form;
    }
    
    /**
     * Get parent data.
     * 
     * returns data about parent
     * @param $parentType - type of parent
     * @param $idParent - id of parent
     * @return data about parent
     */
    private function _getParentData($parentType,$idParent) {
        if($parentType == 'articles') {
            $articleObj = new Admin_Model_Article($idParent);
            return $articleObj->getData(false,true);
        }    
    }
    
    /**
     * Reply form.
     * 
     * manages form
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Form $form
     * @param Admin_Model_Comment $commentObj
     * @return Zend_Form
     */
    private function _replyForm(Zend_Controller_Request_Http $request, Zend_Form $form, Admin_Model_Comment $commentObj) {
        $data = $commentObj->getData();
        
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                $formData = $form->getValues();
                
                $commentsObj = new Admin_Model_Comments();
                $commentsObj->save($formData,$data->parent_type,$data->parent_id);

                PTX_Message::setMessage4User('admin','done',$this->view->trans('Your reply has been stored.'));
                $this->_redirect(PTX_Anchor::getAnchor('admin'));
            }
        } else {
            $populateArray = array(
                'name'      => Zend_Auth::getInstance()->getStorage()->read()->name,
                'parent'    => $data->id);
               
            $form->populate($populateArray);
        }
        
        return $form;
    }
}