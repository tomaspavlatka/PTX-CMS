<?php
class PTX_String {
	
    /**
     * Escape.
     * 
     * escapes string.
     * @param $string - string to be escaped
     * @return escaped string
     */
    public static function escape($string) {
        return trim(htmlspecialchars(strip_tags($string)));
    }
	
	/**
	 * pripravi klicova slova
	 * @param $string - retezec
	 * @param $minLength - min delka slova
	 * @return klicova slova
	 */
	public static function parseKeywords($string,$minLength = 2) {
		$myString = PTX_Uri::getUri($string);
		$explode = explode('-',$myString);
		$cExplode = (count($explode)-1);
		
		$keywordsTip = array();
		
		for($i=0; $i <= $cExplode; $i++) {
			for($j = 0; $j <= $cExplode; $j++) {
				if($i == $j) {
					continue;
				} else {
					if(strlen($explode[$i]) >= $minLength && strlen($explode[$j]) >= $minLength && $explode[$j] != $explode[$i]) {
					   $tip = $explode[$i].' '.$explode[$j];
					   if(!in_array($tip,$keywordsTip)) {
					       $keywordsTip[] = $tip;
					   }
					} 
				} 
			}
		}
		
		return $keywordsTip;
	}
	
	/**
     * vybere z reteze jenom dany pocet slov
     * @param $string - retezec
     * @param $count - pocet slov
     * @return retezec s danym poctem slov
     */
    public static function wrapWord($string,$count) {
        // pokud nemame kladne
        if($count < 0) {
            // vyhodime novou vyjimku
            throw new PTX_Exception(__CLASS__.": Count must be plus value :) - $count");
        }
        
        // rozparsujeme si retezec
        $parseArray = explode(' ',$string);
        
        // spoctem si, kolik mame prvku v poli
        $cParseArray = count($parseArray);
        
        // pokud poce prvku je mensi nez pozadovany pocet slov
        if($cParseArray <= $count) {
            // vratime cely string
            return $string;
        } else {
            // pripravime si string
            $returnString = null;
            
            // projdem polem
            for($i=0; $i< $count; $i++) {
                // pridame si slovo do retezce
                $returnString .= $parseArray[$i].' ';   
            }
            
            $returnString .= ' ...';
            // vratime ho 
            return $returnString;
        }
    }    
}