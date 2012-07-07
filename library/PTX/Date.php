<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 10.6.2010
**/

class PTX_Date {
    
    /**
     * premeni datum na mktime
     * @param $date - datum 
     * @param $separator - jak je oddelene datum
     * @param $year - na jakem indexu najdem rok
     * @param $month - na jakem indexu najdem mesic
     * @param $day - na jakem indexu najdem den
     * @return mktime
     */
    public static function convertDate2Mktime($date,$separator = '/',$year = 0, $month = 1, $day = 2) {
        $explode = explode($separator,$date);
        
        return mktime(0,0,1,$explode[$month],$explode[$day],$explode[$year]);
    }
}