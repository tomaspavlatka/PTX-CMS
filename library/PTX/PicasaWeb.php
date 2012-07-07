<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Sep 24, 2011
 */

class PTX_PicasaWeb {
	
	/*
	 * Feed Url (Private).
	 * 
	 * holds feed url info.
	 */
	private $_feedUrl = "http://picasaweb.google.com/data/feed/api/user/";
	
	/*
	 * sXML (Private).
	 * 
	 * holds actual value of simplexml_load_file
	 */
	private $_sXML;
	
	/*
	 * User (Private).
	 * 
	 * holds user's ids.
	 */
	private $_user = null;
	
	/**
	 * Construct.
	 * 
	 * constructor of the class
	 * @param string $user - user' nickname in picasa
	 */
	public function __construct($user) {
		$this->_user = $user;
	}
	
	/**
	 * Albums.
	 * 
	 * get information about albums.
	 * @param array $params - additinal params
	 * @return array with details about albums	 
	 */
	public function albums(array $params = array()) {
		// Load data.
        $feedURL = $this->_feedUrl.urlencode($this->_user).'?kind=album';
        $this->_sXML = simplexml_load_file($feedURL);
        
        // Builds albums.
        $data = array();
        foreach ($this->_sXML->entry as $key => $values) {
        	$right = (string)$values->rights;
        	if(array_key_exists($right,$data)) {
        		$data[$right][] = $values;
        	} else {
        		$data[$right] = array($values);
        	}
        }
        
        // Restring according to permission.
        if(isset($params['rights_only'])) {
        	foreach($data as $key => $values) {
        		if(!in_array($key,$params['rights_only'])) {
        			unset($data[$key]);
        		}
        	}
        }
		
        // Build album's details.
        $albums = array();
        foreach($data as $key => $values) {
        	foreach($values as $albumKey => $albumValues) {
        		$albums[] = $this->_albumDetails($albumValues,$params);
        	}
        }
        
        return (array)$albums;
	}
	
	/**
	 * Album details (Private).
	 * 
	 * builds information about album
	 * @param object $albumData - data about album
	 * @param array $params - additional params.
	 * @return array data about album
	 */
	private function _albumDetails($albumData, array $params = array()) {
		if(!isset($params['album_data']) || !is_array($params['album_data'])) {
			$params['album_data'] = array('id','published','updated','title','summary','rights');
		}
		
		// Global details.
		$details = array();
		foreach($params['album_data'] as $key => $value) {
			$details[$value] = (string)$albumData->$value;
		}
		$details['albumid'] = $this->_parseId($details['id'],'albumid');
		
		// If we need a photos.
        if(isset($params['photos']) && $params['photos'] == true) {
			if(!isset($params['photo_data']) || !is_array($params['photo_data'])) {
				$params['photo_data'] = array('id','published','updated','title','summary');
			}
        
            $details['photos'] = array(
	            'feed_url' => str_replace('/entry/','/feed/',$albumData->id),
			    'photos' => array());
			
            // Find photos.
			$photosXml = simplexml_load_file($details['photos']['feed_url']);
			foreach($photosXml->entry as $photoKey => $photoValues) {
				$photo = array();
				foreach($params['photo_data'] as $key => $value) {
					if($value == 'title') {
                        $photo['title'] = (!($photoValues->summary instanceof SimpleXMLElement)) ? (string)$photoValues->summary : (string)$albumData->title;
					} else if($value == 'summary') {
						$photo['summary'] = (!($photoValues->summary instanceof SimpleXMLElement)) ? (string)$photoValues->summary : (string)$albumData->summary;
					} else {
						$photo[$value] = (string)$photoValues->$value;
					}
				}
				$photo['src'] = (string)$photoValues->content->attributes()->src;
				$photo['image_type'] = (string)$photoValues->content->attributes()->type;
				$photo['photoid'] = $this->_parseId($photo['id'],'photoid');
				$details['photos']['photos'][] = $photo;
			}
			
			$details['photos']['count'] = count($details['photos']['photos']);
		}
		
		// Return.
		return (array)$details;
	}
	
	/**
	 * Parse id (Private).
	 * 
	 * Parses id from a string.
	 * @param string $string - string
	 * @param string $ident - ident
	 * @return id
	 */
	public function _parseId($string,$ident) {
        $explode = explode('/',$string);
        $cExplode = count($explode);
        for($i = 0; $i < $cExplode; $i++) {
        	if($explode[$i] == $ident && isset($explode[$i+1])) {
        		return $explode[$i+1];
        	}
        }
	}
}
