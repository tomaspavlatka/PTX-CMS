<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Sep 23, 2011
 */
  
class Admin_BannerController extends Zend_Controller_Action {    
    
    /************** PUBLIC FUNCTION **************/
    /**
     * Init.
     */
    public function init() {
        $this->view->h1 = $this->view->trans('Banners');
        
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
        $type = $this->_getParam('type',null);
        // First verification.
        if(empty($type)) {
            PTX_Message::setMessage4User('admin', 'warning', $this->view->trans('You must select section first.'));
            $this->_redirect('/admin/banner/list');
        }
        
        // Form.
        $params = array(
            'parent_type' => 'sections',
            'sections'    => $this->_getSections('list','> -1'),
            'section'     => $type);
        $form = new Admin_Form_Banner($params);
        $this->view->form = $this->_addForm($this->getRequest(),$form,$params);
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('New Banner');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Delete.
     */
    public function deleteAction() {
        // Data.
        $idBanner = $this->_getParam('id');
        $bannerObj = new Admin_Model_Banner($idBanner);
        $data = $bannerObj->getData(false,true);
        
        // Verification.
        if(!isset($data['status']) || $data['status'] < 0) {
            $this->_redirect('/admin/index');
        } 
        
        // Delete.
        $rows = $bannerObj->delete();
        
        // Message + redirect.
        if($rows > 0) {
            PTX_Message::setMessage4User('admin','done',$this->view->transParam('Banner <b>~0~</b> has been deleted.',array('~0~'=>$data['name_'.$this->_locale]),false));
        } else {
            PTX_Message::setMessage4User('admin','warning',$this->view->transParam('Banner <b>~0~</b> could not be deleted.',array('~0~'=>$data['name_'.$this->_locale]),false));
        }
        $this->_redirect(PTX_Anchor::getAnchor('admin'));
    }
    
    /**
     * Edit.
     */
    public function editAction() {
        // Data.
        $idBanner = $this->_getParam('id');
        $bannerObj = new Admin_Model_Banner($idBanner);
        $data = $bannerObj->getData(false,true);
        
        // Verification.
        if(!isset($data['status']) || $data['status'] < 0) {
            $this->_redirect('/admin/index');
        } 
        
        // Form.
        $params = array(
            'parent_type' => 'sections',
            'sections' => $this->_getSections('list','> -1'),
            'section' => $this->_getParam('type',null),
            'id' => $data['id']);
        $form = new Admin_Form_Banner($params);
        $this->view->form = $this->_editForm($this->getRequest(),$form,$bannerObj,$params);
        
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
        $bannerSections = $this->_getSections('list','> -1');
        $keys = array_keys($bannerSections);
        if(isset($keys[0])) {
            $this->_redirect('/admin/banner/list/type/'.$keys[0]);
        } 
        
        $this->view->h2 = $this->view->trans('Sections are missing');
    }
    
    /**
     * List.     
     */
    public function listAction() {
        // Parameters.
        $params = array('type' => $this->_getParam('type',null));
        if(empty($params['type'])) {
            $this->_redirect('/admin/banner');
        }
       
        // Zend_Db_Select.
        $treeObj = new Admin_Model_Tree_Banner();
        $treeParams['parent_type'] = 'sections';
        $treeData = $treeObj->getTree($params['type'],0,' > -1',$treeParams); 
        
        // Thumbnails.
        $thumbnailsParams = array(
            'original_path' => './userfiles/images/banner/',
            'return_type' => 'tag',
            'settings' => array('width'=>30, 'height'=>30, 'save_path'=>'./tmp/phpthumb/50/', 'check_size_if_exists'=>true),
        );
        
        $data4View = array();
        foreach($treeData as $row) {
            $thumbnailsParams['alt'] = $row['name_'.$this->_locale];
            $thumbnailsParams['orig_width'] = $row['image_width'];
            $thumbnailsParams['orig_height'] = $row['image_height'];
            if(!empty($row['image_file'])) {
                $row['thumbnail'] = $this->view->ptxPhpThumb($row['image_file'],'thumbnail',$thumbnailsParams);
            } else {
            	$row['thumbnail'] = $this->view->trans('code');
            }
            $row['name'] = $row['name_'.$this->_locale];
            $data4View[] = $row; 
        }
        
        // View variables.
        $this->view->treeData = $treeData;
        $this->view->params = $params;
        $this->view->data4View = $data4View;
        $this->view->bannerTypes = $this->_getSections('list','> -1');
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('List');
        $this->view->headTitle($this->view->h1.' - '.$this->view->h2, 'PREPEND');
        
        // Anchor.
        PTX_Anchor::createAnchor('admin');
    }

    /**
     * Logo.     
     */
    public function logoAction() {
        // Data.
        $id = $this->_getParam('id');
        $bannerObj = new Admin_Model_Banner($id);
        $data = $bannerObj->getData(false,true);
        
        // Verificatin. 
        if(!isset($data['status']) || $data['status'] < 0) {
            $this->_redirect('/admin/index');
        }  
        
        // Form.
        $form = new Admin_Form_BannerLogo();
        $this->view->form = $this->_logoForm($this->getRequest(),$form,$bannerObj);
        
        // View variables.
        $this->view->data = $data;
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        $this->view->banners = $this->_getBanners($data['parent_type'],$data['parent_id']," > -1");
        
        // Page setting.
        $this->view->h2 = $this->view->trans('Logo');
        $this->view->headTitle($this->view->h1 .' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Position.
     */
    public function positionAction() {
        // nactem parametry
        $id = $this->_getParam('id');
        $way = $this->_getParam('way');
        
        // zmenime pozici
        $bannerObj = new Admin_Model_Banner($id);
        $bannerObj->changePosition($way);
        $this->_redirect(PTX_Anchor::getAnchor('admin'));
    }
    
    /**
     * Transform. 
     */
    public function transformAction() {
        $link = '/admin/banner/list';
        
        if($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            
            if(isset($data['type']) && !empty($data['type'])) {
                $link .= '/type/'.urlencode(trim($data['type']));
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
            		$secureFilePath = './userfiles/images/banner/'.$secureName;
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

            	// Save banner.
                $formData = $form->getValues();
                $bannersObj = new Admin_Model_Banners();
                $idBanner = $bannersObj->save($formData,$params);                
                
                PTX_Message::setMessage4User('admin','done',$this->view->transParam('Banner <b>~0~</b> has been stored.',array('~0~'=>$formData['name'.$this->_locale]),false));
                if($formData['continue'] == 0) {
                    $this->_redirect('/admin/banner/list');
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
     * @param Admin_Model_Banner $bannerObj
     * @param array $params - additional params.
     * @return Zend_Form
     */
    private function _editForm(Zend_Controller_Request_Http $request, Zend_Form $form, Admin_Model_Banner $bannerObj, array $params) {
        $data = $bannerObj->getData(false,true);
        
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                $formData = $form->getValues();
                $bannerObj->update($formData,$params);
                
                PTX_Message::setMessage4User('admin','done',$this->view->transParam('Banner <b>~0~</b> has been updated.',array('~0~'=>$formData['name'.$this->_locale]),false));
                $this->_redirect(PTX_Anchor::getAnchor('admin'));
            }
                
        } else {
            $populate = array(
                'parent' => $data['parent_id'],
                'code'   => $data['code'],
                'target' => $data['target'],
                'status' => $data['status']);
            $transData = Admin_Model_AppModel::bindTranslations($data, array('name','title','link'));
            $populate = array_merge($populate,$transData);
            
            $form->populate($populate);
        }
        
        return $form;
    }
    
    /**
     * Get Banners (Private).
     * 
     * returns array with articles.
     * @param string $parentType - type of parent
     * @param integer $parentId - id of parent
     * @param string $status - status of articles we need
     * @return array list of articles
     */
    private function _getBanners($parentType, $parentId, $status = " > -1") {
        $select = Admin_Model_DbSelect_Banners::pureSelect();
        $select->columns(array('id','name_'.$this->_locale.' as name'))->where('status '.$status)->where('parent_type = ?',$parentType)->where('parent_id = ?',(int)$parentId)->order('position asc');
        $stmt = $select->query();
        $data = $stmt->fetchAll();
        
        $list = array();
        if(!empty($data)) {
            foreach($data as $key => $values) {
                $list[$values['id']] = $values['name'];
            }
        }
        
        return (array)$list;
    }
    
    /**
     * Get sections (Private).
     * 
     * return list of the section for banners
     * @param string $resultType - type of the result
     * @return section
     */
    private function _getSections($resultType = 'list', $status = ' > -1') {
        $select = Admin_Model_DbSelect_Sections::pureSelect();
        $select->columns(array('id','name_'.$this->_locale.' as name'))->where('status '.$status)->order('name asc');
        $stmt = $select->query();
        $sections = $stmt->fetchAll();
        
        if($resultType == 'array') {
        	return (array)$sections;
        } else if($resultType == 'list') {
        	$list = array();
        	if(!empty($sections)) {
	        	foreach($sections as $key => $values) {
	        		$list[$values['id']] = $values['name'];
	        	}
        	}
        	
        	return (array)$list;
        }
    }
    
    /**
     * Logo form.
     * 
     * manages form
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Form $form
     * @param Admin_Model_Banner $bannerObj
     */
    private function _logoForm(Zend_Controller_Request_Http $request, Zend_Form $form, Admin_Model_Banner $bannerObj) {
        $bannerData = $bannerObj->getData(false,true);
        
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                $imageSize = array();
                if(isset($_FILES['logo']['name']) && !empty($_FILES['logo']['name'])) {
                    
                    $extension = PTX_Image2::getImageExtension($_FILES['logo']['type']);
                    $secureName = PTX_Uri::getUri($bannerData['name'.$this->_locale]).'-'.time().'.'.$extension;
                    $secureFilePath = './userfiles/images/banner/'.$secureName;
                    $form->logo->addFilter('Rename',array('target' => $secureFilePath,'overwrite'=>true));
                    
                    if(!$form->logo->receive()) {
                        PTX_Message::setMessage4User('admin','warning',$this->view->trans('Problem during uploading category logo.'));
                        $secureName = null;
                    } else {
                        $imageSize = getimagesize($secureFilePath);
                    }
                }

                $filename = (isset($secureName)) ? $secureName : null;
                $bannerObj->updateLogo($filename,$imageSize);
                
                PTX_Message::setMessage4User('admin','done',$this->view->transParam('Logo for category <b>~0~</b> has been updated.',array('~0~'=>$bannerData['name'.$this->_locale]),false));
                $this->_redirect('/admin/banner/logo/id/'.$bannerData['id']);
            } else {
                $form->populate($form->getValues());
            }
        } 
        
        return $form;
    }
}