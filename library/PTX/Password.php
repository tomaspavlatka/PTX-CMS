<?php

final class PTX_Password {
    /************* PUBLIC STATIC FUNCTION **************/
    /**
     * nahodne vygeneruje integer
     * @param $lenght - delka 
     * @return nahodne cislo
     */
    public static function randomInt($length) {
        // pripravime si promenou
        $randomInt = null;
        
        // projdem cyklem
        for($i=0; $i<$length; $i++) {
            if($i == 0) {
                $randomInt .= rand(1,9);
            } else {
                $randomInt .= rand(0,9);
            }
        }
        
        // vratime nahodne cislo
        return $randomInt;
    }    
    
    /**
     * nahodne heslo
     * @param $lenght - delka
     * @param $strength - sila  
     * @return nahodne cislo
     */
    public static function generatePassword($length=9, $strength=0) {
        $vowels = 'aeuy';
        $consonants = 'bdghjmnpqrstvz';
    
        if ($strength >= 1) {
            $consonants .= 'BDGHJLMNPQRSTVWXZ';
        }
        
        if ($strength >= 2) {
            $vowels .= "AEUY";
        }
        
        if ($strength >= 3) {
            $consonants .= '23456789';
        
        }
        
        if ($strength == 4) {
            $consonants .= '@#$%';
        }
 
        $password = null;
        
        $alt = time() % 2;
        for ($i = 0; $i < $length; $i++) {
            if ($alt == 1) {
                $password .= $consonants[(rand() % strlen($consonants))];
                $alt = 0;
            } else {
                $password .= $vowels[(rand() % strlen($vowels))];
                $alt = 1;
            }
        }
        
        return $password;
    }
    
    /**
     * vygeneruje heslo
     * @param $length - delka 
     * @param $bigLetters - zaclenit velka pismena
     * @param $bigVowels - velke samohlasky
     * @param $numbers - cisla
     * @param $specialChars - specialni znaky
     * @return heslo
     */
    public static function generatePasswordPtx($length=9, $bigLetters = false, $bigVowels = false, $numbers = false, $specialChars = false) {
        $vowels = 'aeuy';
        $consonants = 'bdghjmnpqrstvz';
    
        if ($bigLetters) {
            $consonants .= 'BDGHJLMNPQRSTVWXZ';
        }
        
        if ($bigVowels) {
            $vowels .= "AEUY";
        }
        
        if ($numbers) {
            $consonants .= '0123456789';
        
        }
        
        if ($specialChars) {
            $consonants .= '@#$%';
        }
 
        $password = null;
        
        $alt = time() % 2;
        for ($i = 0; $i < $length; $i++) {
            if ($alt == 1) {
                $password .= $consonants[(rand() % strlen($consonants))];
                $alt = 0;
            } else {
                $password .= $vowels[(rand() % strlen($vowels))];
                $alt = 1;
            }
        }
        
        return $password;
    }
    
    /**
     * prelozi heslo do slovni podoby
     * @param $password - hesl
     * @return slovni podoba
     */
    public static function translatePassword($password) {
        $array = array('a'=>'adam','b'=>"babička",'c'=>"cecílie",'d'=>"dagmar",'e'=>"eliška",'f'=>"františka",'g'=>"gustav",
            'h'=>"hana",'i'=>"ivan",'j'=>"jakub",'k'=>"kamil",'l'=>"luboš",'m'=>"mirek",'n'=>"nina",'o'=>"oto",'p'=>"pavel",'q'=>"quest",
            'r'=>"radek",'s'=>"simona",'t'=>"tomáš",'u'=>"uršula",'v'=>"vašek",'w'=>"worfram",'x'=>"xenie",'y'=>"ypsilon",'z'=>"zora",
        
            'A'=>'Adam','B'=>"Babička",'C'=>"Cecílie",'D'=>"Dagmar",'E'=>"Eliška",'F'=>"Františka",'G'=>"Gustav",
            'H'=>"Hana",'I'=>"Ivan",'J'=>"Jakub",'K'=>"Kamil",'L'=>"Luboš",'M'=>"Mirek",'N'=>"Nina",'O'=>"Oto",'P'=>"Pavel",'Q'=>"Quest",
            'R'=>"Radek",'S'=>"Simona",'T'=>"Tomáš",'U'=>"Uršula",'V'=>"Vašek",'W'=>"Worfram",'X'=>"Xenie",'Y'=>"Ypsilon",'Z'=>"Zora",
        
            1=>"jedna",2=>"dvě",3=>"tři",4=>"čtyři",5=>"pět",6=>"šest",7=>"sedm",8=>"osm",9=>"devět",0=>"nula",
            '@'=>"zavináč",'#'=>"hash",'$'=>"dolar",'%'=>"procento");

        $stringArray = PTX_Parser::toArray($password);
        $length = strlen($password);
        
        $translatedPassword = null;
        for($i = 0; $i < $length; $i++) {
            $translatedPassword .= strtr($stringArray[$i],$array);
            
            if($i < ($length-1)) {
                $translatedPassword .= ' - ';
            }
        }
        
        return $translatedPassword;
    }
}