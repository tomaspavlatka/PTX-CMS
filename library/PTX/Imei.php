<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 1.8.2010
**/

class PTX_Imei {
    
    /************** PUBLIC STATIC FUNCTION *************/
    /**
     * zjisti, zda je imei validni
     * @param $imei 0 imei
     * @return true | false
     */
    public static function isValid($imei) {
        if(!is_numeric($imei)) {
            return false;
        } else {
            if(!preg_match('/^[0-9]*$/',$imei)) {
                return false;
            } else {
                $strlen = strlen($imei);
                if($strlen != 15) {
                    return false;
                } else {             
                    return self::_isValid($imei);
                }
            }
        }
    }
    
    /************** PRIVATE STATIC FUNCTION *************/
    /**
     * spocte sum
     * @param $digits - cisla
     * @return suma
     */
    private static function _countSum(array $digits) {
        $sum = 0;
        $counter = 1;
        foreach($digits as $digit) {
            if($counter++ %2 == 0) {
                $localSum = (int)($digit*2);
                
                if($localSum > 9) {
                    $localSum = 1+($localSum-10);
                }
                
                $sum += (int)$localSum;
                
            } else {
                $sum += (int)$digit;
            } 
            
            if($counter == 15) {
                break;
            }
        }
        
        return (int)$sum;
    }
        
    /**
     * zkontroluje, zda je imei validni
     * @param $imei 0 imei
     * @return true | false
     */
    private static function _isValid($imei) {
        $digitsArray = array();
        for($i=0; $i < 15; $i++) {
            $digitsArray[] = substr($imei,$i,1);
        }
        
        $sum = self::_countSum($digitsArray);
        $modulo = $sum % 10;
        if($modulo == 0) { 
            $checkDigit = 0;
        } else {
            $checkDigit = 10 - $modulo;
        }
        
        return ($checkDigit == $digitsArray[14]) ? true : false;
    }
}