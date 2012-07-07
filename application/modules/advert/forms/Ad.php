<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 23.8.2010
**/

class Advert_Form_Ad extends Advert_Form_AppForm {
    
    /**
     * Construct.
     * 
     * constructor of class
     * @param array $params - additional params
     * @param array $options - options
     */
    public function __construct(array $params = array(), array $options = null) {
        parent::__construct($options);

        // Translation.
        $this->_loadTranslation();
        
        // Name.
        $name = new Zend_Form_Element_Text('name');
        $name
            ->setLabel($this->_trans->trans('Name'))
            ->setOptions(array('class'=>"long big",'maxlength'=>255))
            ->setDescription($this->_trans->trans('Input name of your classified ad. Clear and understable name can increase success of your classified ad.'))
            ->addFilters(array('StripTags','StringTrim'))
            ->setRequired(true)
            ->addValidator('StringLength',false,array(1,255));
            
        // Content.
        $content = new Zend_Form_Element_Textarea('content');
        $content
            ->setLabel($this->_trans->trans('Ad Content'))
            ->setOptions(array('class'=>"long",'rows'=>20,'cols'=>10))
            ->setDescription($this->_trans->trans('Do not put phone numbers, e-mails or other kind of contact. There\'re separated fields for contant'))
            ->setRequired(true)
            ->addFilters(array('StripTags','StringTrim'));
            
        // Category.    
        $category = new Zend_Form_Element_Select('category');
        $category
            ->setLabel($this->_trans->trans('Category'))
            ->setOptions(array('class'=>"long",'size'=>10))
            ->setDescription($this->_trans->trans('Select best suitable category for your classified ad'))
            ->setRequired(true)
            ->addMultiOption(null,$this->_trans->trans('- select category -'))
            ->addMultiOptions($params['categories'])
            ->addFilters(array('StripTags','StringTrim'));

        // Location.    
        $location = new Zend_Form_Element_Select('location');
        $location
            ->setLabel($this->_trans->trans('Location'))
            ->setOptions(array('class'=>"long",'size'=>10))
            ->setDescription($this->_trans->trans('Select best suitable location for your classified ad'))
            ->setRequired(true)
            ->addMultiOption(null,$this->_trans->trans('- select location -'))
            ->addMultiOptions($params['locations'])
            ->addFilters(array('StripTags','StringTrim'));
            
        // Price.
        $price = new Zend_Form_Element_Text('price');
        $price
            ->setLabel($this->_trans->trans('Price'))
            ->setOptions(array('class'=>"long",'maxlength'=>255))
            ->addFilters(array('StripTags','StringTrim'))
            ->addValidator('Float')
            ->setDescription($this->_trans->trans('Input numbers only - can be decimal'))
            ->addValidator('StringLength',false,array(1,255));
            
        // Price Text.    
        $priceText = new Zend_Form_Element_Select('pricetext');
        $priceText
            ->setLabel($this->_trans->trans('Price Text'))
            ->setOptions(array('class'=>"long"))
            ->setDescription($this->_trans->trans('Select one of the predefined options'))
            ->addMultiOption(null,$this->_trans->trans('- select price option -'))
            ->addMultiOptions($params['price_options'])
            ->addFilters(array('StripTags','StringTrim'));
            
        // Advert Type.    
        $advertType = new Zend_Form_Element_Select('adtype');
        $advertType
            ->setLabel($this->_trans->trans('Advert Type'))
            ->setOptions(array('class'=>"long"))
            ->setDescription($this->_trans->trans('Select type of your classified ad'))
            ->setRequired(true)
            ->addMultiOption(null,$this->_trans->trans('- select type of classified ad -'))
            ->addMultiOptions($params['ad_types'])
            ->addFilters(array('StripTags','StringTrim'));

        // Phone.
        $phone = new Zend_Form_Element_Text('phone');
        $phone
            ->setLabel($this->_trans->trans('Phone'))
            ->setOptions(array('class'=>"long",'maxlength'=>255))
            ->addFilters(array('StripTags','StringTrim'))
            ->setDescription($this->_trans->trans('Enter landline / mobile number'))
            ->setRequired(true)
            ->addValidator('StringLength',false,array(1,255));

        // Password.
        $passwd = new Zend_Form_Element_Text('password');
        $passwd
            ->setLabel($this->_trans->trans('Password'))
            ->setOptions(array('class'=>"long",'maxlength'=>255))
            ->addFilters(array('StripTags','StringTrim'))
            ->setDescription($this->_trans->trans('You might need it to edit / delete your ad'))
            ->setRequired(true)
            ->addValidator('StringLength',false,array(1,255));

        // Email.
        $email = new Zend_Form_Element_Text('email');
        $email
            ->setLabel($this->_trans->trans('E-mail'))
            ->setOptions(array('class'=>"long",'maxlength'=>255))
            ->addFilters(array('StripTags','StringTrim'))
            ->setRequired(true)
            ->setDescription($this->_trans->trans('Must be valid e-mail address. Will not be visible on page.'))
            ->addValidator('StringLength',false,array(1,255));
            
        // submit //
        $submit = new Zend_Form_Element_Submit('submit');
        $submit
            ->setOptions(array('class'=>"input-submit button"))    
            ->setLabel($this->_trans->trans('Add Classified Ad'));

        // Elements.
        $this->addElements(array($name,$content,$advertType,$category,$location,$price,$priceText,$passwd,$phone,$email,$submit));
        
         // Set decoration.
        $this->_setDecoration(true,false,array(),array('fieldset_id'=>'ad_form'));
    }
}