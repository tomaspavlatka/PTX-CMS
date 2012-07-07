<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Nov 9, 2011
 */
 
class Advert_Model_DbTable_Ad extends Advert_Model_DbTable_AppModel {
    
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        
        $this->_name = 'ads';
    }
    
    /**
     * Save.
     * 
     * save new record into database.
     * @param array $formData - data to be saved.     
     * @param array $params - additional params 
     * @return id of inserted record
     */
    public function save(array $formData,array $params = array()) {
        $data = array(
            'category_id'    => (int)$formData['category'],
            'location_id'    => (int)$formData['location'],
            'name'           => $formData['name'],
            'url'            => PTX_Uri::getUri($formData['name']),
            'help_type'      => $formData['adtype'],
            'content'        => $formData['content'],
            'price'          => (float)(str_replace('.',',',$formData['price'])),
            'price_text'     => $formData['pricetext'],
            'phone'          => $formData['phone'],
            'email'          => $formData['email'],
            'passwd'         => md5($formData['password'].SECURITY_SALT),   
            'created'        => time(),
            'updated'        => time(),
            'status'         => 1);
        
        $id = parent::insert($data);
        if(is_numeric($id)) {
            $this->_saveLog($data);
        }
        return (int)$id; 
    }
}