<?php
/**
 * Author: Tomas Pavlatka
 * Email: tomas.pavlatka@gmail.com
 * Created: 23.1.2010 20:05:42
 */
 
class Admin_Form_AdminMenu extends Admin_Form_AppForm {
	
    /**
     * Construct.
     * 
     * 
     * constructor of class
     * @param $options
     */
	public function __construct($options = null) {
		parent::__construct($options);

		// Translation.
		$this->_loadTranslation();
		
		// name //
		foreach($this->_languages as $key => $values) {
			$name = new Zend_Form_Element_Text('name'.$values['code']);
	        $name
	            ->setLabel($this->_trans->trans('Name'))
	            ->setOptions(array('class'=>"long big",'maxlength'=>100))
	            ->addFilters(array('StripTags','StringTrim'))
	            ->addValidator('StringLength',false,array(1,100));
	            
	        $description = new Zend_Form_Element_Text('description'.$values['code']);
	        $description
	            ->setLabel($this->_trans->trans('Description'))
	            ->setOptions(array('class'=>"long",'maxlength'=>150))
	            ->addFilters(array('StripTags','StringTrim'))
	            ->addValidator('StringLength',false,array(1,150));
	            
            if($key == 0) {
            	$name->setRequired(true);
            }
	            
            $this->addElements(array($name,$description));
		} 

        // parent //
        $parent = new Zend_Form_Element_Select('parent');
        $parent
            ->setLabel($this->_trans->trans('Parent menu'))
            ->setOptions(array('class'=>"medium"))
            ->addMultiOption(0,$this->_trans->trans('- top menu -'))
            ->setRequired(true);        
        
        // nacte strom            
        $tree = $this->_getTree();       
        foreach($tree as $row) {
        	if($options['id'] == $row['id']) {
        		$level = $row['level'];
        		continue;
        	} else if (isset($level) && $level < $row ['level']) {
        		continue;
        	} else {
        		$level = null;
        	}
        	
        	$menuName = str_repeat('-- ',$row['level']).$row['name'];
        	$parent->addMultiOption($row['id'],$menuName);
        }                 
            
        // controller //
        $controller = new Zend_Form_Element_Text('controller');
        $controller
            ->setLabel($this->_trans->trans('Controller'))
            ->addFilters(array('StripTags','StringTrim'))
            ->setOptions(array('class' => "medium",'maxlength'=>20))
            ->addValidator('StringLength',false,array(1,20))
            ->setRequired(true);
            
        // module //
        $module = new Zend_Form_Element_Text('module');
        $module
            ->setLabel($this->_trans->trans('Module'))
            ->addFilters(array('StripTags','StringTrim'))
            ->setOptions(array('class' => "medium"))
            ->setValue('admin')
            ->setOptions(array('class' => "medium",'maxlength'=>20))
            ->addValidator('StringLength',false,array(1,20))
            ->setRequired(true);
                        
        // action //
        $action = new Zend_Form_Element_Text('action');
        $action 
            ->setLabel($this->_trans->trans('Action'))
            ->addFilters(array('StripTags','StringTrim'))
            ->setOptions(array('class' => "medium"))
            ->setOptions(array('class' => "medium",'maxlength'=>20))
            ->addValidator('StringLength',false,array(1,20))
            ->setRequired(true);
                
        // params //
        $params = new Zend_Form_Element_Text('params');
        $params
            ->setLabel($this->_trans->trans('Params'))
            ->addFilters(array('StripTags','StringTrim'))
            ->setOptions(array('class' => "medium",'maxlength'=>255))
            ->addValidator('StringLength',false,array(1,255));
            
        // status //
        $status = new Zend_Form_Element_Radio('status');
        $status
            ->setLabel($this->_trans->trans('Status'))
            ->addMultiOption(1,$this->_trans->trans('Active'))
            ->addMultiOption(0,$this->_trans->trans('Non-active'))
            ->setValue(1);
            
        // continue //
        $continue = new Zend_Form_Element_Radio('continue');
        $continue
            ->setLabel($this->_trans->trans('Continue'))
            ->addMultiOption(1,$this->_trans->trans('Add another menu input'))
            ->addMultiOption(0,$this->_trans->trans('Menu input detail'))
            ->setValue(1);
            
        // submit //
        $submit = new Zend_Form_Element_Submit('submit');
        $submit
            ->setOptions(array('class'=>"input-submit"))    
            ->setLabel($this->_trans->trans('Submit'));
            
        if(isset($options['id'])) {            
            $this->addElements(array($parent,$module,$controller,$action,$params,$status,$submit));
        } else {
            $this->addElements(array($parent,$module,$controller,$action,$params,$status,$continue,$submit));
        }
        
        // Set decoration.
        $transFields = array('name','description');
        $this->_setDecoration(true,false,$transFields);
	}
	
	/************** PRIVATE FUNCTION *************/
	/**
	 * Get tree.
	 * 
	 * return tree
	 * @return tree
	 */
	private function _getTree(){
		$treeObj = new Admin_Model_Tree_AdminMenu();
		return $treeObj->getTree(0,0," > -1");
	}
}