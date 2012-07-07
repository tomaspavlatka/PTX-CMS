<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 6, 2010
**/

class Admin_Form_AppForm extends Zend_Form {
    
    protected $_trans;
    protected $_transParam;
    protected $_languages;
    protected $_locale;
    
    /**
     * Construct. 
     * 
     * constructor of class
     * @param $options - options
     * @param $method - form method value
     * @param $action - form action value
     */
    public function __construct($options = null, $method = 'post', $action = null) {
        parent::__construct($options);
        
        // Form setting.
        $this->setMethod($method);
        $this->setAction($action);
        $this->setOptions(array('id'=>"zendform"));
        
        $this->_languages = Admin_Model_DbSelect_Languages::getActive();
        $this->_locale = Zend_Registry::get('PTX_Locale');
    }
    
    /************ PROTECTED FUNCTION *************/
    /**
     * Add CSRF protection.
     * 
     * adds csrf protectrion for form
     */
    protected function _addCsrfProtection() {
        $this->addElement('hash', 'no_csrf_foo', array('salt' => 'unique'));
    }
    
    /**
     * Get tags.
     * 
     * returns tags 
     * @return tags
     */
    protected function _getTags() {
        $locale = Zend_Registry::get('PTX_Locale');
        
        $select = Admin_Model_DbSelect_Tags::pureSelect();
        $select->columns(array('id','name_'.$locale.' as name'))->where('status > -1')->order('name asc');
        $stmt = $select->query();
        return $stmt->fetchAll();    
    }    
    
    /**
     * Load translation.
     * 
     * loads translation for form, if needed.
     */
    protected function _loadTranslation() {
        require_once APPLICATION_PATH.'/helpers/Trans.php';
        $this->_trans = new Zend_View_Helper_Trans;
    }
    
    /**
     * Load translation params.
     * 
     * loads translation params for form, if needed.
     */
    protected function _loadTranslationParam() {
        require_once APPLICATION_PATH.'/helpers/TransParam.php';
        $this->_transParam = new Zend_View_Helper_TransParam();
    }
    
    /**
     * Set decoration.
     * 
     * sets decoration for form
     * @param boolean $label - make decoration for label ?
     * @param boolean $multiPart - include $this->setAttrib('enctype', 'multipart/form-data');
     * @apram array $transFields - array with name of fields with translations
     */
    protected function _setDecoration($label = true, $multiPart = false, $transFields = array()) {
        foreach ($this->getElements() as $el) {
            $el->addDecorator('Label',array('tag'=>'td','tagClass'=>'form_label','requiredSuffix' => ' *','id' => 'dt_' . $el->getName()));
            $el->addDecorator('Description',array('tag' => 'p','class' => 'hint','id' => 'hint_' . $el->getName()));
            $el->addDecorator('Errors',array('style' => 'color: red; font-weight: bold;'));
            $el->addDecorator('HtmlTag',array('tag' => 'td','class'=>'forminput','id' => 'dd_'.$el->getName()));
            
            if(strstr($el->helper,'Radio')) {
                $el->setSeparator('&nbsp;&nbsp;&nbsp;');
            }
            
            if($el->getName() == 'submit') {
                $el->removeDecorator('Label');
                $el->removeDecorator('DtDdWrapper');
            }
        }   
        $this->addDecorator('FormElements')
             ->addDecorator('Fieldset')
             ->addDecorator('HtmlTag',array('tag' => 'table','class' => 'zendform'))
             ->addDecorator('Form');

        // Multipart.
        if($multiPart) {
            $this->setAttrib('enctype', 'multipart/form-data');
        }
        
        $this->setDecorators(array(array('ViewScript', array('viewScript' => '_forms/language.phtml','form'=>$this,'columns'=>$transFields))));
        
        $this->setDisableLoadDefaultDecorators(false); 
    }
}