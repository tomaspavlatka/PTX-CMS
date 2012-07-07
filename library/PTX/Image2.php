<?php
/**
 * Author: Tomas Pavlatka
 * Email: tomas.pavlatka@gmail.com
 * Created: 14.7.2009 17:31:26
 */

class PTX_Image2 {
    /************ PUBLIC STATIC FUNCTION ************/
    /**
     * zmeni fotku
     * @param $nw - nova sirka
     * @param $nh - nova vyska
     * @param $source - zdroj
     * @param $stype - typ
     * @param $dest - umisteni
     * @return unknown_type
     */
    public static function resizeImage($nw, $nh, $source, $stype, $dest) {
        $size = getimagesize($source);
        $w = $size[0];
        $h = $size[1];
     
        switch($stype)  {
            case 'gif':
                $simg = imagecreatefromgif($source);
                break;
            case 'jpg':
            case 'jpeg':
                $simg = imagecreatefromjpeg($source);
                break;
            case 'png':
                $simg = imagecreatefrompng($source);
                break;
        }
     
        $nw = ($w < $nw) ? $w : $nw;
        $nh = ($h < $nh) ? $h : $nh;
        
        $dimg = imagecreatetruecolor($nw, $nh);
        $wm = $w/$nw;
        $hm = $h/$nh;
     
        $h_height = $nh/2;
        $w_height = $nw/2;
     
        if($w > $h)  {
            $adjusted_width = $w / $hm;
            $half_width = $adjusted_width / 2;
            $int_width = $half_width - $w_height;
     
            imagecopyresampled($dimg,$simg,-$int_width,0,0,0,$adjusted_width,$nh,$w,$h);
        }  elseif(($w <$h) || ($w == $h)) {
            $adjusted_height = $h / $wm;
            $half_height = $adjusted_height / 2;
            $int_height = $half_height - $h_height;
     
            imagecopyresampled($dimg,$simg,0,-$int_height,0,0,$nw,$adjusted_height,$w,$h);
        } else {
            imagecopyresampled($dimg,$simg,0,0,0,0,$nw,$nh,$w,$h);
        }
     
        imagejpeg($dimg,$dest,100);
    }
    
    public static function resizeImageA2H($maxHeight,$source,$sType,$destination) {
        $size = getimagesize($source);
        $imageWidth = $size[0];
        $imageHeight = $size[1];
     
        switch($sType) {
            case 'gif':
                $simg = imagecreatefromgif($source);
                break;
            case 'jpg':
            case 'jpeg':
                $simg = imagecreatefromjpeg($source);
                break;
            case 'png':
                $simg = imagecreatefrompng($source);
                break;
        }
         
        $pomer = ($imageHeight < $maxHeight) ? 1 : ($imageHeight / $maxHeight);
        
        $newWidth = $imageWidth / $pomer;
        $newHeight = $maxHeight;
        
        $dimg = imagecreatetruecolor($newWidth, $newHeight);
     
        imagecopyresampled($dimg,$simg,0,0,0,0,$newWidth,$newHeight,$imageWidth,$imageHeight);
     
        imagejpeg($dimg,$destination,100);
    }
    
    public static function resizeImageMax($maxWidth,$maxHeight,$source,$sType,$destination) {
        $size = getimagesize($source);
        $imageWidth = $size[0];
        $imageHeight = $size[1];
     
        switch($sType) {
            case 'gif':
                $simg = imagecreatefromgif($source);
                break;
            case 'jpg':
            case 'jpeg':
                $simg = imagecreatefromjpeg($source);
                break;
            case 'png':
                $simg = imagecreatefrompng($source);
                break;
        }
        
        $pomerWidth = $imageWidth / $maxWidth;
        $pomerHeight = $imageHeight / $maxHeight;
        
        if(($imageHeight / $pomerWidth) > $imageHeight) {
            $newWidth = $imageWidth / $pomerHeight ;
            $newHeight = $maxHeight;
        } else {
            $newWidth = $maxWidth;
            $newHeight = $imageHeight / $pomerHeight;
        }
                
        $dimg = imagecreatetruecolor($newWidth, $newHeight);
     
        imagecopyresampled($dimg,$simg,0,0,0,0,$newWidth,$newHeight,$imageWidth,$imageHeight);
     
        imagejpeg($dimg,$destination,100);
    }
    
    public static function getImageExtension($type) {
    	switch($type) {
    		case 'image/jpeg':
            case 'image/pjpeg':
    			return 'jpg';
    		case 'image/gif':
    			return 'gif';
            case 'image/png':
            	return 'png';
    	}
    }
}
 
 
