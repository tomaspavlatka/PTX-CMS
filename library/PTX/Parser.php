<?php

class PTX_Parser {
	
    /**
     * rozparsuje string a ulozi ho do pole
     * @param $string - string
     * @return pole
     */
	public static function toArray($string) {
        $length = strlen($string);
        
        $stringArray = array ();
        for($i = 0; $i < $length; $i++) {
            $stringArray[] = substr($string,$i,1);
        }
        
        return $stringArray;
	}
	
	/**
	 * rozparsuje parametry u widgetu
	 * @param $param - parametry
	 * @return pole array({ident}=>{value});
	 */
	public static function parseWidgetParam($param) {
		$paramArray = array();
        
        $explodeArray = explode('|~|',$param);
        
        foreach($explodeArray as $row) {
            $inExplodeArray = explode('=',$row);
            if(is_array($inExplodeArray) && count($inExplodeArray) == 2) {
                $paramArray[$inExplodeArray[0]] = $inExplodeArray[1]; 
            }
        }
        
        return $paramArray;
	}
}