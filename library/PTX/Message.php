<?php
/**
 * Author: Tomas Pavlatka
 * Email: tomas.pavlatka@gmail.com
 * Created: 23.1.2010 22:38:45
 */
 
class PTX_Message {
    /************ VARIABLE ************/

    /************ CONST ************/
    const MESSAGE_4_USER = 'message_4_user';

    /************ PUBLIC FUNCTION ************/

    /************ PUBLIC STATIC FUNCTION ************/
    /**
     * ziskame si zpravy ze SESSION
     * @param $namespace - namespace 
     * @param $key - klic
     * @return hodnota
     */
    public static function getMessage4User($namespace,$key = null) {
        // pripravime si klic
        $key4Use = (!empty($key)) ? $key : self::MESSAGE_4_USER;
        
        // vytahnem si pole ze SESSION
        $array = PTX_Session::getSessionValue($namespace,$key4Use);
        
        // anulujem si promenou v SESSION
        PTX_Session::setSessionValue($namespace,$key4Use,array());
        
        // vratime hodnotu
        return $array;
    }
    
    /**
     * ulozi novou zpravu do SESSION
     * @param $namespace - namespace
     * @param $value - hodnota
     * @param $key - klic
     */
    public static function setMessage4User($namespace,$result,$text,$key = null) {
        // pripravime si klic
        $key4Use = (!empty($key)) ? $key : self::MESSAGE_4_USER;
        
        // vytahnem si pole ze SESSION
        $array = PTX_Session::getSessionValue($namespace,$key4Use);
        
        $array[] = array($result => $text);
        
        // ulozime si promenou do SESSION
        PTX_Session::setSessionValue($namespace,$key4Use,$array);
    }
}