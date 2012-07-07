<?php
final class PTX_File {
    
	public static function getFileExtension($type) {
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