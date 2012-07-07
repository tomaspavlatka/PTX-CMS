<?php
/**
 * Author: Tomas Pavlatka
 * Email: tomas.pavlatka@gmail.com
 * Created: 23.1.2010 18:56:16
 */
 
class Admin_Model_AdminMenu extends Admin_Model_AppModel {
	
	/************* PUBLIC FUNCTION *************/
	/**
	 * Construct.
	 * 
	 * constructor of class
	 * @param $id - ID	 
	 */
	public function __construct($id) {
		$this->_dao = new Admin_Model_DbTable_AdminMenu();
		$this->_id = (int)$id;
	}
	
	/**
	 * Can be deleted.
	 * 
	 * checks whether record can be deleted
	 * @return true | false
	 */
	public function canBeDeleted() {
	    
	    // Children.
	    if($this->hasChildren()) {
	        return false;
	    }
	    
	    return true;
	}
	
	/**
     * Change position.
     * 
     * changes position for record 
     * @param $way - smer
     * @return true | false
     */
    public function changePosition($way) {
        $this->_checkData();
        
        $data4Change = $this->_dao->find4PositionChange($this->_data->parent_id,$this->_data->position,$way);

        if(isset($data4Change->position)) {
            $this->_dao->updatePosition($this->_data->position,$data4Change->id);
            $this->_dao->updatePosition($data4Change->position,$this->_id);
            
            return true;
        } else{
            return false;
        }
    } 
	
	/**
	 * Get children.
	 * 
	 * return direct children
	 * @return children
	 */
	public function getChildren() {
	    return $this->_dao->getChildren($this->_id);
	}
	
	/**
	 * Has chilrend.
	 * 
	 * check whether children exist
	 * @return true | false
	 */
	public function hasChildren() {
	    $children = $this->getChildren();
	    
	    return (count($children) > 0) ? true : false;
	}

    /**
     * Update.
     * 
     * updates data in database
     * @param $formData - data to be updated
     * @return number of affected rows in db
     */
    public function update(array $formData) {
        $this->_checkData(false);
        
        $updPosition = ($this->_data->parent_id != $formData['parent']) ? true : false;
        return $this->_dao->ptxUpdate($formData,$this->_id,$updPosition);
    }
}