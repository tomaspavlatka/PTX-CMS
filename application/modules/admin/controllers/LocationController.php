<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Oct 22, 2011
 */
 
class Admin_LocationController extends Zend_Controller_Action {    
    
    /************** PUBLIC FUNCTION **************/
    /**
     * Init.
     */
    public function init() {
        $this->view->h1 = $this->view->trans('Locations');
        
        $response = $this->getResponse();
        $response->insert('menuTop',$this->view->render('_menu/top.phtml'));
        $response->insert('incTop',$this->view->render('_inc/top-row.phtml'));
        $response->insert('incLeft',$this->view->render('_inc/left.phtml'));
        
        PTX_Anchor::set($this->getRequest());
        $this->_locale = Zend_Registry::get('PTX_Locale');
        $this->view->locale = $this->_locale;
    }
    
    /**
     * Add.     
     */
    public function addAction() {
        // Form.
        $treeObj = new Admin_Model_Tree_Location();
        $treeData = $treeObj->getTree(0,0,' > -1',array('mandatory'=>array('name')));
        $list = array();
        foreach($treeData as $key => $values) {
            $list[$values['id']] = str_repeat('-- ',$values['level']).$values['name'];
        }
        $params = array(
            'parents' => $list,
        );
        $form = new Admin_Form_Location($params);
        $this->view->form = $this->_addForm($this->getRequest(),$form,$params);
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('New Location');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Delete.
     */
    public function deleteAction() {
        // Data.
        $locationId = $this->_getParam('id');
        $locationObj = new Admin_Model_Location($locationId);
        $data = $locationObj->getData(false,true);
        
        // Verification.
        if(!isset($data['status']) || $data['status'] < 0) {
            $this->_redirect('/admin/index');
        } 
        
        // Delete.
        $rows = $locationObj->delete();
        
        // Message + redirect.
        if($rows > 0) {
            PTX_Message::setMessage4User('admin','done',$this->view->transParam('Location <b>~0~</b> has been deleted.',array('~0~'=>$data['name_'.$this->_locale]),false));
        } else {
            PTX_Message::setMessage4User('admin','warning',$this->view->transParam('Location <b>~0~</b> could not be deleted.',array('~0~'=>$data['name_'.$this->_locale]),false));
        }
        $this->_redirect(PTX_Anchor::getAnchor('admin'));
    }
    
    /**
     * Edit.
     */
    public function editAction() {
        // Data.
        $locationId = $this->_getParam('id');
        $locationObj = new Admin_Model_Location($locationId);
        $data = $locationObj->getData(false,true);
        
        // Verification.
        if(!isset($data['status']) || $data['status'] < 0) {
            $this->_redirect('/admin/index');
        } 
        
        // Form.
        $treeObj = new Admin_Model_Tree_Location();
        $treeData = $treeObj->getTree(0,0,' > -1',array('mandatory'=>array('name')));
        $list = array();
        foreach($treeData as $key => $values) {
            $list[$values['id']] = str_repeat('-- ',$values['level']).$values['name'];
        }
        $params = array(
            'parents' => $list,
            'id' => $data['id']);
        $form = new Admin_Form_Location($params);
        $this->view->form = $this->_editForm($this->getRequest(),$form,$locationObj,$params);
        
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
        $this->_redirect('/admin/location/list');
    }
    
    /**
     * List.     
     */
    public function listAction() {
        // Zend_Db_Select.
        $treeObj = new Admin_Model_Tree_Location();
        $treeData = $treeObj->getTree(0,0,' > -1');
        
        // View variables.
        $this->view->treeData = $treeData;
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('List');
        $this->view->headTitle($this->view->h1.' - '.$this->view->h2, 'PREPEND');
        
        // Anchor.
        PTX_Anchor::createAnchor('admin');
    }

    /**
     * Position.
     */
    public function positionAction() {
        // nactem parametry
        $id = $this->_getParam('id');
        $way = $this->_getParam('way');
        
        // zmenime pozici
        $locationObj = new Admin_Model_Location($id);
        $locationObj->changePosition($way);
        $this->_redirect(PTX_Anchor::getAnchor('admin'));
    }
    
    /**
     * Add form.
     * 
     * manages form
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Form $form
     * @param array $params - additional params
     * @return Zend_Form
     */
    private function _addForm(Zend_Controller_Request_Http $request, Zend_Form $form, array $params) {
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                
                $params['image_file'] = null;
                
                if(isset($_FILES['logo']['name']) && !empty($_FILES['logo']['name'])) {
                    $extension = PTX_Image2::getImageExtension($_FILES['logo']['type']);
                    $secureName = PTX_Uri::getUri($_POST['name'.$this->_locale]).'-'.time().'.'.$extension;
                    $secureFilePath = './userfiles/images/location/'.$secureName;
                    $form->logo->addFilter('Rename',array('target' => $secureFilePath,'overwrite'=>true));

                    if(!$form->logo->receive()) {
                        PTX_Message::setMessage4User('admin','warning',$this->view->trans('Problem during uploading category logo.'));
                        $secureName = null;
                    } else {
                        $imageSize = getimagesize($secureFilePath);
                        $params['image_width'] = $imageSize[0];
                        $params['image_height'] = $imageSize[1];
                    }
                    
                    $params['image_file'] = $secureName;
                }

                // Save location.
                $formData = $form->getValues();
                $locationsObj = new Admin_Model_Locations();
                $locationId = $locationsObj->save($formData,$params);                
                
                PTX_Message::setMessage4User('admin','done',$this->view->transParam('Location <b>~0~</b> has been stored.',array('~0~'=>$formData['name'.$this->_locale]),false));
                if($formData['continue'] == 0) {
                    $this->_redirect('/admin/location/list');
                } else {
                    $form->populate($formData);    
                }
            }
        } 
        
        return $form;
    }
    
    /**
     * Edit form.
     * 
     * manages form
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Form $form
     * @param Admin_Model_Location $locationObj
     * @param array $params - additional params.
     * @return Zend_Form
     */
    private function _editForm(Zend_Controller_Request_Http $request, Zend_Form $form, Admin_Model_Location $locationObj, array $params) {
        $data = $locationObj->getData(false,true);
        
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                $formData = $form->getValues();
                $locationObj->update($formData,$params);
                
                PTX_Message::setMessage4User('admin','done',$this->view->transParam('Location <b>~0~</b> has been updated.',array('~0~'=>$formData['name'.$this->_locale]),false));
                $this->_redirect(PTX_Anchor::getAnchor('admin'));
            }
                
        } else {
            $populate = array(
                'parent' => $data['parent_id'],
                'status' => $data['status']);
            $transData = Admin_Model_AppModel::bindTranslations($data, array('name'));
            $populate = array_merge($populate,$transData);
            
            $form->populate($populate);
        }
        
        return $form;
    }
}