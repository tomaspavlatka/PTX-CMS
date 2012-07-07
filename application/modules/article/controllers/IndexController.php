<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 18.8.2010
**/

class Article_IndexController extends Zend_Controller_Action{
    
    /************* PUBLIC FUNCTION  ***************/
    public function init() {
        $response = $this->getResponse();
        
        // Widgets.
        $response->insert('widgetFooter',$this->view->render('_widget/footer.phtml'));
        $response->insert('widgetSidebar',$this->view->render('_widget/sidebar.phtml'));
        $response->insert('widgetTop',$this->view->render('_widget/top.phtml'));
        $response->insert('widgetUnder',$this->view->render('_widget/under.phtml'));
        
        // Menu
        $response->insert('menuHeader',$this->view->render('_menu/header.phtml'));
        $response->insert('menuMain',$this->view->render('_menu/main.phtml'));
        $response->insert('menuBottom',$this->view->render('_menu/bottom.phtml'));

        // projectconfig
        $this->_config = PTX_Session::getSessionValue('settings', 'project');
        $this->_locale = Zend_Registry::get('PTX_Locale');
        $this->view->locale = Zend_Registry::get('PTX_Locale');
        
        // pro vyhledavace
        $this->view->headMeta()->appendName('robots','index,follow');
        $this->view->headMeta()->appendName('googlebot','snippet,archive');
    }

    /**
     * index
     */
    public function indexAction() {
        $this->_redirect('/',array('code'=>302));
    }
    
}