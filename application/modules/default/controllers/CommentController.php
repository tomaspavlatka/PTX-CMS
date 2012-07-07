<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 9.11.2010
**/

class CommentController extends Zend_Controller_Action {
    
    /************** PUBLIC FUNCTION **************/
    /**
     * PreDispatch.
     */
    public function preDispatch() {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        
        // projectconfig
        $this->_config = PTX_Session::getSessionValue('settings', 'project');
        $this->_locale = Zend_Registry::get('PTX_Locale');
        $this->view->locale = Zend_Registry::get('PTX_Locale');
    }
    
    /**
     * Approve.
     */
    public function approveAction() {
        
        $commentId = $this->_getParam('id');
        $code = $this->_getParam('code');

        $commentObj = new Default_Model_Comment($commentId);
        $commentData = $commentObj->getData(false,true);
        
        if(!isset($commentData['status']) || $commentData['status'] < 0) {
            PTX_Message::setMessage4User('user','error',$this->view->trans('There is no comment as you requested.'));
            $this->_redirect('/');
        } else if($commentData['status'] == 1) {
            PTX_Message::setMessage4User('user','notice',$this->view->trans('This comment is already approved.'));
            $this->_redirect($this->_getParentLink($commentData['parent_id'],$commentData['parent_type']));
        } else if($commentData['status'] == 2) {
            if($commentData['code'] == $code) {
                $commentObj->approve();
                PTX_Message::setMessage4User('user','done',$this->view->trans('Comment has been approved.'));
                
                $articleObj = new Article_Model_Article($commentData['parent_id']);
                $articleData = $articleObj->getData(false,true);
        
                $categoryObj = new Default_Model_Category($articleData['category_id']);
                $categoryData = $categoryObj->getData(false,true);
                
                // Notify user.
                $mailerObj = new Default_Model_Mailer('utf-8');
                $params = array(
                    'article_data'  => $articleData,
                    'category_data' => $categoryData);
                $mailerObj->commentNotify($commentData,$params);
                
            } else {
                PTX_Message::setMessage4User('user','error',$this->view->trans('Comment cannot be approved because of wrong code. Try to copy it again or approve comment from administration.'));
            }
            $this->_redirect($this->_getParentLink($commentData['parent_id'],$commentData['parent_type']));
        }
    }
    
    /**
     * Delete.
     */
    public function deleteAction() {
        
        $commentId = $this->_getParam('id');
        $code = $this->_getParam('code');

        $commentObj = new Default_Model_Comment($commentId);
        $commentData = $commentObj->getData(false,true);
        
        if(!isset($commentData['status']) || $commentData['status'] < 0) {
            PTX_Message::setMessage4User('user','error',$this->view->trans('There is no comment as you requested.'));
            $this->_redirect('/');
        } else if($commentData['code'] == $code) {
            $commentObj->delete();
            PTX_Message::setMessage4User('user','done',$this->view->trans('Comment has been deleted.'));
        } else {
            PTX_Message::setMessage4User('user','error',$this->view->trans('Comment cannot be approved because of wrong code. Try to copy it again or approve comment from administration.'));
        }

        $this->_redirect($this->_getParentLink($commentData['parent_id'],$commentData['parent_type']));
    }
    
    /**
     * Get parent link (Private).
     * 
     * returns link for parent
     * @param integer $parentId - id parent
     * @param string $parentType - type of parent 
     * @return link
     */
    private function _getParentLink($parentId,$parentType) {
        if($parentType == 'articles') {
            $articleObj = new Article_Model_Article($parentId);
            $articleData = $articleObj->getData(false,true);
    
            $categoryObj = new Default_Model_Category($articleData['category_id']);
            $categoryData = $categoryObj->getData(false,true);
            
            return $this->view->baseUrl().$this->view->ptxUrl(array('caturl'=>$categoryData['url_'.$this->_locale],'url'=>$articleData['url_'.$this->_locale],'id'=>$articleData['id']),'article',true);
        } else {
            return '/';
        }
    }
}