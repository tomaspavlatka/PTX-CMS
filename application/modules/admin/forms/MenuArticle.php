<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 7.9.2010
**/

class Admin_Form_MenuArticle extends Admin_Form_AppForm {
    
    /**
     * Construct.
     * 
     * constructor of class
     * @param $options - options
     */
    public function __construct($params = array(),$options = null) {
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
               
            // Href title
            $title = new Zend_Form_Element_Text('title'.$values['code']);
            $title
                ->setLabel($this->_trans->trans('Href title'))
                ->setOptions(array('class'=>"long",'maxlength'=>255))
                ->addFilters(array('StripTags','StringTrim'))
                ->addValidator('StringLength',false,array(1,255));
                                           
            if($key == 0) {
                $name->setRequired(true);
            }
                
            $this->addElements(array($name,$title));
        } 
        
        // Parent.
        $parent = new Zend_Form_Element_Select('parent');
        $parent
            ->setLabel($this->_trans->trans('Parent'))
            ->setOptions(array('class'=>'medium'))
            ->addMultiOption(0,$this->_trans->trans('- top input -'))            
            ->setRequired(true);
            
        foreach($params['parents'] as $key => $values) {
            $parent->addMultiOption($values['id'],str_repeat('-- ',$values['level']).$values['name_'.$this->_locale]);
        }   
        
        // input //
        $input = new Zend_Form_Element_Select('input');
        $input
            ->setLabel($this->_trans->trans('Article'))
            ->addMultiOption(null,$this->_trans->trans('- select article -'))            
            ->setRequired(true);
            
        $inputs = $this->_getArticles();
        foreach($inputs as $row) {
            $input->addMultiOption($row['id'],$row['name']);
        }
            
        // target //
        $target = new Zend_Form_Element_Radio('target');
        $target
            ->setLabel($this->_trans->trans('Link goes to'))
            ->addMultiOption(1,$this->_trans->trans('Same window / tab'))
            ->addMultiOption(2,$this->_trans->trans('New window / tab'))
            ->setValue(1);

        // continue //
        $continue = new Zend_Form_Element_Radio('continue');
        $continue
            ->setLabel($this->_trans->trans('Continue'))
            ->addMultiOption(1,$this->_trans->trans('Add another menu input'))
            ->addMultiOption(0,$this->_trans->trans('Menu input detail'))
            ->setValue(1);
            
        // status //
        $status = new Zend_Form_Element_Radio('status');
        $status
            ->setLabel($this->_trans->trans('Status'))
            ->addMultiOption(1,$this->_trans->trans('Active'))
            ->addMultiOption(0,$this->_trans->trans('Non-active'))
            ->setValue(1);
            
        // submit //
        $submit = new Zend_Form_Element_Submit('submit');
        $submit
            ->setOptions(array('class'=>"input-submit"))    
            ->setLabel($this->_trans->trans('Submit'));
            
        if(!isset($options['id'])) {
            $this->addElements(array($input,$parent,$target,$status,$continue,$submit));
        } else {
            $this->addElements(array($input,$parent,$target,$status,$submit));
        }
        
        $transFields = array('name','title');
        $this->_setDecoration(true,false,$transFields);
    }
    
    /*************** PRIVATE FUNTION ***************/
    /**
     * Get articles.
     * 
     * returns articles for select
     * @return articles
     */
    private function _getArticles() {
        $select = Admin_Model_DbSelect_Articles::pureSelect();
        $select->columns(array('id','name_'.$this->_locale.' as name'))->where('status > -1')->order('name asc');
        $stmt = $select->query();
        return $stmt->fetchAll();    
    }
}