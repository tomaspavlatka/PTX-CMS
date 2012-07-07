<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Nov 9, 2011
 */
 
class Advert_IndexController extends Zend_Controller_Action{
    
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
    }

    /**
     * index
     */
    public function indexAction() {
        
        $categories = $this->_getCategories();
        $locations = $this->_getLocations();
        
        //
        $select = Advert_Model_DbSelect_Ads::pureSelect();
        $select->columns(array('id','category_id','location_id','name','url','help_type','price','price_text','created'))
            ->where('status = 1')->order('updated DESC')->limit(15);
        $stmt = $select->query();
        $ads = $stmt->fetchAll();
        
        $categoryObj = new Default_Model_Tree_Category();
        foreach($ads as $key => $values) {
            $ads[$key]['top_category'] = $categoryObj->getTopCategoryUrl($values['category_id'],$this->_locale);
        }
        
        $this->view->ads = $ads;
        $this->view->categories = $categories;
        $this->view->locations = $locations;
    }
    
    
    /**
     * Get Categories (Private).
     * 
     * returns array with categories.
     * @return array with data about categories
     */
    private function _getCategories() {
        $categoryObj = new Default_Model_Tree_Category();
        $treeData = $categoryObj->getTree(0,0," = 1",array('parent_type'=>'adverts'));
        
        $categories = array();
        foreach($treeData as $key => $values) {
            $categories[$values['id']] = $values;
        }
        
        return (array)$categories;
    }
    
    /**
     * Get Locations (Private).
     * 
     * returns array with locations.
     * @return array with data about locations
     */
    private function _getLocations() {
        $categoryObj = new Default_Model_Tree_Location();
        $treeData = $categoryObj->getTree(0,0," = 1");
        
        $locations = array();
        foreach($treeData as $key => $values) {
            $locations[$values['id']] = $values;
        }
        
        return (array)$locations;
    }
}