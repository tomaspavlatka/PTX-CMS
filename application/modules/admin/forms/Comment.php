<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 9.11.2010
**/

class Admin_Form_Comment extends Admin_Form_AppForm {
    
    /************* PUBLIC FUNCTION ***************/
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

        // name //
        $name = new Zend_Form_Element_Text('name');
        $name
            ->setLabel($this->_trans->trans('Person name'))
            ->setOptions(array('class'=>"long big"))
            ->addFilters(array('StripTags','StringTrim'))
            ->addValidator('StringLength',false,array(1,150))
            ->setRequired(true);
                        
        // content //
        $content = new Zend_Form_Element_Textarea('message');
        $content 
            ->setLabel($this->_trans->trans('Leave a Reply'))
            ->addFilters(array('StripTags','StringTrim'))
            ->setOptions(array('class' => "long",'rows'=>10,'cols'=>100))
            ->setRequired(true);
            
        // Parent id.
        $parentId = new Zend_Form_Element_Select('parent');
        $parentId 
            ->setLabel($this->_trans->trans('Parent'))
            ->addMultiOption(0,$this->_trans->trans('- top comment -'))
            ->setRequired(true);
            
        $tree = $this->_getTree($options);
        foreach($tree as $row) {
            $typeName = str_repeat('-- ',$row['level']).$row['personname'].' ('.date('d/m/Y',$row['created']).')';
            $parentId->addMultiOption($row['id'],$typeName);
        }
            
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
            
        // Element.s            
        $this->addElements(array($name,$content,$parentId,$status,$submit));
        
        // Decoration.
        $this->_setDecoration(); 
    }
    
    /************* PRIVATE FUNCTION *************/
    /**
     * Get tree.
     * 
     * returns tree view for comments.
     * @param $options - options
     * @return tree
     */
    private function _getTree($options) {
        $treeObj = new Admin_Model_Tree_Comment();
        return $treeObj->getTree(0,0," > -1",$options['parent_type'],$options['parent_id']);
    }
}