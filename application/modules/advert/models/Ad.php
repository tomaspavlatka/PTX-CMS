<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Nov 9, 2011
 */
 
class Advert_Model_Ad extends Advert_Model_AppModel {
    
    /**
     * Construct.
     * 
     * constructor of class
     * @param $id - ID
     */
    public function __construct($id) {
        $this->_dao = new Advert_Model_DbTable_Ad();
        $this->_id = (int)$id;
    }
}