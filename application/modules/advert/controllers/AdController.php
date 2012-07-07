<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Nov 9, 2011
 */

class Advert_AdController extends Zend_Controller_Action{
    
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
        
        $this->_priceOptions = array(
            'free' => $this->view->trans('For free'),
            'deal' => $this->view->trans('Deal'),
            'nomatter' => $this->view->trans('What ever you give'),
            'offer'  => $this->view->trans('Waiting for your offer'));

        $this->_adTypes = array(
            'offer' => $this->view->trans('OFFER - I would like to sell, I offer'),
            'demand' => $this->view->trans('DEMAND - I would like to buy, I am looking for'),
        );
    }

    /**
     * Add
     */
    public function addAction() {
        $treeObj = new Admin_Model_Tree_Category();
        $locationsObj = new Default_Model_Tree_Location();
        
        // Form.
        $params = array(
            'categories' => $treeObj->getCategories('adverts','= 1',array('name'),null,'list'),
            'locations' => $locationsObj->getLocations('= 1',array('name'),null,'list'),
            'price_options' => $this->_priceOptions,
            'ad_types' => $this->_adTypes,
        );
        $form = new Advert_Form_Ad($params);
        $this->view->form = $this->_addForm($this->getRequest(),$form);
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("user");
        $this->view->params = $params;
        
        // Page settings.
        $this->view->h2 = $this->view->trans('New Article');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * index
     */
    public function indexAction() {
        
    }
    
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
                // Prices.
                if(empty($formData['price']) && empty($formData['pricetext'])) {
                    PTX_Message::setMessage4User('user','error',$this->view->trans('You must insert price or choose one of predefined options for price.'));
                    $correct = false;
                }
                
                // Save ad.
                if($correct) {
                    $adsObj = new Advert_Model_Ads();
                    $adId = $adsObj->save($formData);
                    
                    $treeObj = new Default_Model_Tree_Category();
                    $categoryUrl = $treeObj->findTopCategoryUrl($formData['category'],$this->_locale);
                
                    PTX_Message::setMessage4User('user','done',$this->view->transParam('Classified Ad <b>~0~</b> has been stored.',array('~0~'=>$formData['name']),false));
                    $url = $this->view->baseUrl().$this->view->ptxUrl(array('caturl'=>$categoryUrl,'id'=>$adId,'url'=>PTX_Uri::getUri($formData['name'])),'ad','true');
                    $this->_redirect($url);
                }
            } else {
                $form->populate($form->getValues());
            }
        } 
        
        return $form;
    }
}