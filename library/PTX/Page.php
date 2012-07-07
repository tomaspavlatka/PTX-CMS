<?php
abstract class PTX_Page {
	
    /**
     * spocte margin
     * @param $level - level
     * @return margin
     */
    public static function countMargin($level) {
        // vratime margin
        return (10 * log(3 + $level) - 10); 
    }
    
    /**
     * vrati pole s id
     * @param $array
     * @param $id
     * @return pole s idcky
     */
    public static function getIdsArray(array $array,$idColumn) {
    	$idsArray = array();
    	
    	foreach($array as $row) {
    	    if(isset($row[$idColumn])) {
    	        $idsArray[] = $row[$idColumn];
    	    }
    	}
    	
    	return $idsArray;
    } 
}