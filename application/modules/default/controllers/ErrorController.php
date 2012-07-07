<?php

class ErrorController extends Zend_Controller_Action {

    public function init() {
        $response = $this->getResponse();
        
        // Widgets.
        /*$response->insert('widgetFooter',$this->view->render('_widget/footer.phtml'));
        $response->insert('widgetSidebar',$this->view->render('_widget/sidebar.phtml'));
        $response->insert('widgetTop',$this->view->render('_widget/top.phtml'));
        $response->insert('widgetUnder',$this->view->render('_widget/under.phtml'));
        
        // Menu
        $response->insert('menuHeader',$this->view->render('_menu/header.phtml'));
        $response->insert('menuMain',$this->view->render('_menu/main.phtml'));
        $response->insert('menuBottom',$this->view->render('_menu/bottom.phtml'));     */

        // projectconfig
        $this->_config = PTX_Session::getSessionValue('settings', 'project');
        $this->_locale = Zend_Registry::get('PTX_Locale');
        $this->view->locale = Zend_Registry::get('PTX_Locale');        
        // pro vyhledavace
        $this->view->headMeta()->appendName('robots','noindex,nofollow');
        $this->view->headMeta()->appendName('googlebot','nosnippet,noarchive');       
    }
    
	/************** PUBLIC FUNCTION *************/
    public function errorAction() {
        $errors = $this->_getParam('error_handler');
        
        switch ($errors->type) { 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                break;
            default:
                // application error 
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Application error';
                break;
        }
        
        
        $this->view->h1 = $this->view->trans('Oops, what are you looking for is not there');
        $this->view->headTitle($this->view->h1, 'PREPEND');
        
        echo $this->view->exception = $errors->exception;
        $this->view->request   = $errors->request;
    }
}

