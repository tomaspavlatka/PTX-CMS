<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* http://patsx.com
* Date: 13.6.2010
**/

final class PTX_Filter {
    
    /************* PUBLIC STATIC FUNCTION **************/
    /**
     * upravi retezec pro zobrazeni na strankach
     * @param $string - retezec
     * @return upraveny retezec
     **/                    
    public static function escape($string) {
        return htmlspecialchars(trim($string));
    }
}