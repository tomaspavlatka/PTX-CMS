<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Oct 22, 2011
 */
 
class Default_Model_Tree_Location extends Default_Model_Tree_AppModel {
    
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct(); 
        
        $this->_name = 'locations';
        $this->_transFields = array('name');
    }
    
    /**
     * Get Locations.
     * 
     * returns array with locations.
     * @param string $status - status of pages we need
     * @param array  $mandatory - mandatory fields
     * @param integer $skip - id of top record of branch in tree which must be skipped
     * @param string $returnType - type of return
     * @return array list of categories
     */
    public function getLocations($status = " > -1", array $mandatory = array(), $skip = null, $returnType = 'array') {
        $tree = $this->getTree(0,0,$status,array('mandatory'=>$mandatory));
        
        $list = array(); 
        foreach($tree as $key => $values) {
            if($values['id'] == $skip) {
                $level = $values['level'];
                continue;
            } else if (isset($level) && $level < $values['level']) {
                continue;
            } else {
                $level = null;
            }
            
            if($returnType == 'list') {
                $list[$values['id']] = str_repeat('-- ',$values['level']).$values['name'];
            } else {
                $list[] = $values;
            }
        }
        
        return (array)$list;
    }
}