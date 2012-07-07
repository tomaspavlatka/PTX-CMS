<?php

class Admin_ManageController extends Zend_Controller_Action {

    /************* PUBLIC FUNCTION *************/
    public function init() {
        /* Initialize action controller here */
    	$this->view->h1 = $this->view->trans('Management');
    	
    	$response = $this->getResponse();
        $response->insert('menuTop',$this->view->render('_menu/top.phtml'));
        $response->insert('incTop',$this->view->render('_inc/top-row.phtml'));
        $response->insert('incLeft',$this->view->render('_inc/left.phtml'));
    }
    
    /**
     * admin
     */
    public function adminAction() {
        // page setting
        $this->view->h2 = $this->view->trans('Administration');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Advert
     */
    public function advertAction() {
        // page setting
        $this->view->h2 = $this->view->trans('Advert');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * content
     */
    public function contentAction() {
        // page setting
        $this->view->h2 = $this->view->trans('Content');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * page
     */
    public function pageAction() {
        // page setting
        $this->view->h2 = $this->view->trans('Page management');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Photogallery.
     */
    public function photogalleryAction() {
        // page setting
        $this->view->h2 = $this->view->trans('Photogallery');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
}