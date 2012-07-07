<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Nov 9, 2011
 */
 
class Advert_Model_Ads extends Advert_Model_AppModels {
    
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        $this->_dao = new Advert_Model_DbTable_Ad();
    }
}