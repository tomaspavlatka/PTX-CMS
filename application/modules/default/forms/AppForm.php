<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 16, 2010
**/

class Default_Form_AppForm extends Zend_Form {
    
    /************ VARIABLES *************/
    protected $_trans;
    protected $_transParam;
    
    /************ PUBLIC FUNCTION *************/
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
     * @param $label - make decoration for label ?
     * @param $multiPart - include $this->setAttrib('enctype', 'multipart/form-data');
     * @param $class - class of form
     */
    protected function _setDecoration($label = true, $multiPart = false,$class = 'zendform') {
       foreach ($this->getElements() as $el) {
            // if label. 
            if($label) { 
                $el->addDecorator('Label',array('tag' => 'div','class'=>'formlabel','requiredSuffix' => ' *','id' => 'dt_' . $el->getName()));
            }
            $el->addDecorator('Description',array('tag' => 'p','class' => 'hint','id' => 'hint_' . $el->getName()));
            $el->addDecorator('Errors',array('style' => 'color: red; font-weight: bold;'));
            $el->addDecorator('HtmlTag',array('tag' => 'div','class'=>'forminput','id' => 'dd_'.$el->getName()));
        }   

        $this->addDecorator('FormElements')
             ->addDecorator('Fieldset')
             ->addDecorator('HtmlTag',array('tag' => 'div','class' => $class))
             ->addDecorator('Form');

        // Multipart.
        if($multiPart) {
            $this->setAttrib('enctype', 'multipart/form-data');
        }

        $this->setDisableLoadDefaultDecorators(false); 
    }
}