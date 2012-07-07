<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 15, 2010
**/

class Admin_Form_WidgetArticle extends Admin_Form_AppForm {
    
    /**
     * Construct.
     * 
     * constructor of class
     * @param $options - options
     */
    public function __construct($options = null) {
        parent::__construct($options);
            
        // Translation.
        $this->_loadTranslation();
            
        foreach($this->_languages as $key => $values) {
            // Name
            $name = new Zend_Form_Element_Text('name'.$values['code']);
            $name
               ->setLabel($this->_trans->trans('Name'))
               ->setOptions(array('class'=>"long big",'maxlength'=>50))
               ->addFilters(array('StripTags','StringTrim'))
               ->addValidator('StringLength',false,array(1,50));
                                           
            if($key == 0) {
                $name->setRequired(true);
            }
                
            $this->addElements(array($name));
        } 

        // Numbers.            
        $number = new Zend_Form_Element_Select('number');
        $number
            ->setLabel($this->_trans->trans('Number of articles'))
            ->setRequired(true)
            ->setValue(5);
            
        for($i = 1; $i < 11; $i++) {
            $number->addMultiOption($i,$i);
        }
        
        // Categories.
        $category = new Zend_Form_Element_Multiselect('category');
        $category
            ->setLabel($this->_trans->trans('Category'))
            ->setRequired(true)
            ->setValue(0)
            ->addMultiOption(0,$this->_trans->trans('- all categories -'));

        $cats = $this->_getCategories();
        foreach($cats as $row) {
            $category->addMultiOption($row['id'],str_repeat('-- ',$row['level']).$row['name_'.$this->_locale]);
        }
            
        // showname //
        $showName = new Zend_Form_Element_Radio('showname');
        $showName
            ->setLabel($this->_trans->trans('Show name'))
            ->addMultiOption(1,$this->_trans->trans('Yes'))
            ->addMultiOption(0,$this->_trans->trans('No'))
            ->setRequired(true)
            ->setValue(0);
                        
        // status //
        $status = new Zend_Form_Element_Radio('status');
        $status
            ->setLabel($this->_trans->trans('Status'))
            ->addMultiOption(1,$this->_trans->trans('Active'))
            ->addMultiOption(0,$this->_trans->trans('Non-active'))
            ->setRequired(true)
            ->setValue(1);
            
        // submit //
        $submit = new Zend_Form_Element_Submit('submit');
        $submit
            ->setOptions(array('class'=>"input-submit"))    
            ->setLabel($this->_trans->trans('Submit'));
            
        $this->addElements(array($number,$category,$showName,$status,$submit));
        
        // Decorations.
        $transFields = array('name');
        $this->_setDecoration(true,false,$transFields); 
    }
    
    /**
     * Get categories.
     * 
     * return tree view of categories for articles
     * @return tree veiw of categories
     */
    private function _getCategories() {
        $treeObj = new Admin_Model_Tree_Category();
        return $treeObj->getTree(0,0," > -1",array('parent_type'=>'articles'));    
    }
}
