<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Sep 24, 2011
 */
  

class Admin_Form_WidgetBanner extends Admin_Form_AppForm {
    
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
            ->setLabel($this->_trans->trans('Number of banners'))
            ->setRequired(true)
            ->setValue(5);
            
        for($i = 1; $i < 11; $i++) {
            $number->addMultiOption($i,$i);
        }
        
        // Setion..
        $section = new Zend_Form_Element_Multiselect('category');
        $section
            ->setLabel($this->_trans->trans('Section'))
            ->setOptions(array('class'=>'medium'))
            ->setRequired(true);

        $sections = $this->_getSections();
        foreach($sections as $row) {
            $section->addMultiOption($row['id'],$row['name']);
        }
            
        // showname //
        $showName = new Zend_Form_Element_Radio('showname');
        $showName
            ->setLabel($this->_trans->trans('Show name'))
            ->addMultiOption(1,$this->_trans->trans('Yes'))
            ->addMultiOption(0,$this->_trans->trans('No'))
            ->setRequired(true)
            ->setValue(0);
                        
        // Random.
        $random = new Zend_Form_Element_Radio('random');
        $random
            ->setLabel($this->_trans->trans('Shuffle first'))
            ->addMultiOption(1,$this->_trans->trans('Yes'))
            ->addMultiOption(0,$this->_trans->trans('No'))
            ->setRequired(true)
            ->setValue(1);
            
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
            
        $this->addElements(array($number,$section,$showName,$random,$status,$submit));
        
        // Decorations.
        $transFields = array('name');
        $this->_setDecoration(true,false,$transFields); 
    }
    
    /*************** PRIVATE FUNTION ***************/
    /**
     * Get categories.
     * 
     * return tree view of categories for articles
     * @return tree veiw of categories
     */
    private function _getSections() {
        $select = Admin_Model_DbSelect_Sections::pureSelect();
        $select->columns(array('id','name_'.$this->_locale.' as name'))->where('status > -1')->order('name asc')->where('parent_type = ?','banners');
        $stmt = $select->query();
        $data = $stmt->fetchAll();
        
        return (array)$data;
    }
}

