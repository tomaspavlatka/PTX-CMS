<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 9.11.2010
**/

class Article_AjaxController extends Zend_Controller_Action {
    /************** PREDISPATCH **************/
    public function preDispatch() {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
    }
    
    /************** PUBLIC FUNCTION **************/
    /**
     * Comment form.
     */
    public function commentFormAction() {
        $form = new Default_Form_Comment();
        echo $form.'<div class="fix"></div>';        
    }
}