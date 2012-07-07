<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Sep 24, 2011
 */
  
class Admin_Form_BannerLogo extends Admin_Form_AppForm {
    
    /**
     * Construct.
     * 
     * constructor of class
     * @param $options
     */
    public function __construct($options = null) {
        parent::__construct($options);
            
        // Translation.
        $this->_loadTranslation();
        
        // Logo.
        $photo = new Zend_Form_Element_File('logo');
        $photo
            ->setLabel($this->_trans->trans('Logo'))
            ->setDestination('./userfiles/images/banner/')
            ->addValidator('Size', false, 512000)
            ->addValidator('Extension', false, 'jpg,png,gif');
                    
        // submit //
        $submit = new Zend_Form_Element_Submit('submit');
        $submit
            ->setOptions(array('class'=>"input-submit"))    
            ->setLabel($this->_trans->trans('Submit'));

        // Elements.            
        $this->addElements(array($photo,$submit));
        
        // Decorators.
        $this->_setDecoration(true,true);
    }
}