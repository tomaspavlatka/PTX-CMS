<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 6, 2010
**/

class Advert_Form_AppForm extends Zend_Form {
    
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
     * @param array $transFields - array with name of fields with translations
     * @param array $params - additional params
     */
    protected function _setDecoration($label = true, $multiPart = false, $transFields = array(), array $params = array()) {
        foreach ($this->getElements() as $el) {
            $el->addDecorator('Label',array('tag'=>'div','tagClass'=>'form_label','requiredSuffix' => ' *','id' => 'dt_' . $el->getName()));
            $el->addDecorator('Description',array('tag' => 'p','class' => 'hint','id' => 'hint_' . $el->getName()));
            $el->addDecorator('Errors',array('style' => 'color: red; font-weight: bold;'));
            $el->addDecorator('HtmlTag',array('tag' => 'div','class'=>'forminput','id' => 'dd_'.$el->getName()));
            
            if(strstr($el->helper,'Radio')) {
                $el->setSeparator('&nbsp;&nbsp;&nbsp;');
            }
            
            if($el->getName() == 'submit') {
                $el->removeDecorator('Label');
                $el->removeDecorator('DtDdWrapper');
            }
        }   
        
        // Params for global decorators.
        $decoratorsParams = array(
            'fieldset_class' => null,
            'fieldset_id'    => 'zendform',
            'form_class'     => null,
            'form_id'        => 'zendform',
            'html_tag_tag'   => 'div',
            'html_tag_class' => null,
            'html_tag_id'    => 'zendform');
        $decoratorsParams = array_merge($decoratorsParams,$params);
        
        $this->addDecorator('FormElements')
             ->addDecorator('Fieldset',array('class'=>$decoratorsParams['fieldset_class'],'id'=>$decoratorsParams['fieldset_id']))
             ->addDecorator('HtmlTag',array('tag'=>$decoratorsParams['html_tag_tag'],'class'=>$decoratorsParams['html_tag_class'],'id'=>$decoratorsParams['html_tag_id']))
             ->addDecorator('Form',array('class'=>$decoratorsParams['form_class'],'id'=>$decoratorsParams['form_id']));

        // Multipart.
        if($multiPart) {
            $this->setAttrib('enctype', 'multipart/form-data');
        }
        
        $this->setDecorators(array(array('ViewScript', array('viewScript' => 'ad/form/ad.phtml','form'=>$this,'columns'=>$transFields))));
        
        $this->setDisableLoadDefaultDecorators(false); 
    }
}