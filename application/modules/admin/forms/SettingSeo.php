<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Oct 15, 2011
 */
 
class Admin_Form_SettingSeo extends Admin_Form_AppForm {
    
    /**
     * Construct.
     * 
     * constructor of class
     * @param $options - options
     */
    public function __construct($options = array()) {
        parent::__construct($options);
        
        // Translations.
        $this->_loadTranslation();
        
        foreach($this->_languages as $key => $values) {
            $indexTitle = new Zend_Form_Element_Text('indextitle'.$values['code']);
            $indexTitle
                ->setLabel($this->_trans->trans('General Title'))
                ->setOptions(array('class'=>"long",'maxlength'=>75))
                ->addFilters(array('StripTags','StringTrim'))
                ->addValidator('StringLength',false,array(1,75));
    
            // index description //
            $indexDescription = new Zend_Form_Element_Text('indexdescription'.$values['code']);
            $indexDescription
                ->setLabel($this->_trans->trans('General Description'))
                ->setOptions(array('class'=>"long",'maxlength'=>165))
                ->addFilters(array('StripTags','StringTrim'))
                ->addValidator('StringLength',false,array(1,165));
                
            // index keywords //
            $indexKeywords = new Zend_Form_Element_Text('indexkeywords'.$values['code']);
            $indexKeywords
                ->setLabel($this->_trans->trans('General Keywords'))
                ->setOptions(array('class'=>"long",'maxlength'=>255))
                ->addFilters(array('StripTags','StringTrim'))
                ->addValidator('StringLength',false,array(1,255));
                
            // photogallery title //
            $photogalleryTitle = new Zend_Form_Element_Text('photogallerytitle'.$values['code']);
            $photogalleryTitle
                ->setLabel($this->_trans->trans('Photogallery Title'))
                ->setOptions(array('class'=>"long",'maxlength'=>75))
                ->addFilters(array('StripTags','StringTrim'))
                ->addValidator('StringLength',false,array(1,75));
    
            // photogallery description //
            $photogalleryDescription = new Zend_Form_Element_Text('photogallerydescription'.$values['code']);
            $photogalleryDescription
                ->setLabel($this->_trans->trans('Photogallery Description'))
                ->setOptions(array('class'=>"long",'maxlength'=>165))
                ->addFilters(array('StripTags','StringTrim'))
                ->addValidator('StringLength',false,array(1,165));
                
            // photogallery keywords //
            $photogalleryKeywords = new Zend_Form_Element_Text('photogallerykeywords'.$values['code']);
            $photogalleryKeywords
                ->setLabel($this->_trans->trans('Photogallery Keywords'))
                ->setOptions(array('class'=>"long",'maxlength'=>255))
                ->addFilters(array('StripTags','StringTrim'))
                ->addValidator('StringLength',false,array(1,255));
                
            $this->addElements(array($indexTitle,$indexKeywords,$indexDescription,$photogalleryTitle,$photogalleryDescription,$photogalleryKeywords));
        } 
            
        // submit //
        $submit = new Zend_Form_Element_Submit('submit');
        $submit
            ->setOptions(array('class'=>"input-submit"))    
            ->setLabel($this->_trans->trans('Submit'));
            
        $this->addElements(array($submit));
        
        // Set decoration.
        $transFields = array('indextitle','indexdescription','indexkeywords','photogallerytitle','photogallerydescription','photogallerykeywords');
        $this->_setDecoration(true,false,$transFields);
    }
}