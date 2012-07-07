<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 22.8.2010
**/

require_once APPLICATION_PATH.'/helpers/PtxUrl.php';

class Zend_View_Helper_GetWidget4Print {

	/* 
	 * Cache (Private).
	 * 
	 * holds object with cache
	 */
    private $_cache;
    
    /* 
     * Data (Private).
     * 
     * holds data for an object.
     */
    private $_data;
    
    /*
     * Base Url Helprer (Private).
     * 
     * holds instance of Zend_View_Helper_BaseUrl
     */
    private $_baseUrlHelper;
    
    /*
     * Locale (Private).
     * 
     * holds actual locale.
     */
    private $_locale;
    
    /*
     * Params.
     */
    private $_params = array(
        'header_before' => '<div class="widget-title tb">',
        'header_after' => '</div>',
        'block_before' => null,
        'block_after' => null,    
        'item_before' => null,
        'item_after' => null,
        
        'name_style' => 'link',
        'name_before' => null,
        'name_after' => null,
        'name_column' => 'name',
        'name_link_extra' => null,
        
        'link_extra' => false,
        'link_extra_before' => null,
        'link_extra_after' => null,
        'link_extra_column' => null,
        'link_extra_extra' => null,
        
        'image_show' => false,
        'image_before' => null,
        'image_after' => null,
        'image_width' => 30,        
        
        'date_show' => false,
        'perex_strip' => '<img><b><u><i><em><strong>',
        'date_format' => 'd.m.',
        'text_before' => null,
        'text_after' => null,
        'text_words_number' => 25,
    );
    
    /*
     * Params Banners (Private).
     * 
     * special setting for banners.
     */
    private $_paramsBanners = array(
       'image_folder' => '',
       'image_width'  => 220,
       'image_height' => 0,
       'image_alt'    => null,
       
       'image_ne' => 'no', // possible text / image / no
       'image_ne_text' => null,
       'image_ne_text_class' => 'msg info',
       'image_ne_image' => null,
       'image_ne_image_width' => null,
       'image_ne_image_height' => null,
    );
    
    /*
     * Url Helper (Private)
     * 
     * holds instace of Zend_View_Helper_Url
     */
    private $_urlHelper;
    
    /************* PUBLIC FUNCTION **************/
    /**
     * Construct.
     * 
     * construct of class.
     */
    public function __construct() {
        $this->_cache = Zend_Registry::get('ptxcachelong');
        $this->_urlHelper = new Zend_View_Helper_PtxUrl();
        $this->_baseUrlHelper = new Zend_View_Helper_BaseUrl();
        $this->_locale = Zend_Registry::get('PTX_Locale');
    }
    
    /**
     * Escape
     * 
     * escape string
     * @param $string - string 
     * @return escaped string
     */
    public function escape($string) {
        return htmlspecialchars(trim($string));
    }
    
    /**
     * Get widget 4 print
     * 
     * returns codes to print widgets
     * @param integer $placeId - ID of widget place
     * @param array $params - parameters
     * @return code to be printed
     */
    public function getWidget4Print($placeId, array $params = array()) {        
        $this->_getData($placeId);
        
        // We do not have any data.
        if(!is_array($this->_data) || count($this->_data) == 0) {
            return null;
        } 
        
        // Overwrite params.
        $this->_params = array_merge($this->_params,$params);
        $code = null;
        
        // Foreach.
        foreach($this->_data as $row) {
            switch($row['parent_type']) {
                case 'articlelast':
                case 'articlerandom':
                case 'articleshown':                    
                    $code .= $this->_articleList($row);
                    break;
                case 'banner':
                	$code .= $this->_banners($row);
                	break;
                case 'category':                    
                    $code .= $this->_categoriesMenu($row);
                    break;
                case 'text':
                    $code .= $this->_widgetText($row);
                    break;
                case 'twitter':
                    $code .= $this->_widgetTwitter($row);
                    break;
                default:
                    $code .= $row['parent_type'];
            }
        }
        
        return $code;
    }
    
    /**
     * Article list.
     * 
     * returns code for articles
     * @param $widgetData - data
     * @return code
     */
    private function _articleList($widgetData) {
            // Parse params.
            $params = PTX_Parser::parseWidgetParam($widgetData['param']);
            $params['parent_type'] = $widgetData['parent_type'];
            
            // Zend_Db_Select + Data
            $select = Article_Model_DbSelect_Articles::pureSelect();
            $select
                ->columns(array('articles.id','articles.name_'.$this->_locale.' as name','articles.short_name_'.$this->_locale.' as short_name','articles.published','articles.perex_'.$this->_locale.' as perex','articles.url_'.$this->_locale.' as url_article','image_file'))
                ->where('articles.status = 1')->limit($params['number']);
            $this->_completeSelect($select,$params); 
            $stmt = $select->query();
            $articles = $stmt->fetchAll();
    
            // Prepare code.
            $code = null;
            if(is_array($articles) && count($articles) > 0) {
                if(isset($widgetData['show_name']) && $widgetData['show_name'] == 1) {
                    $code .= $this->_params['header_before'].$this->escape($widgetData['name']).$this->_params['header_after'];
                }
    
                $code .= $this->_params['block_before'];
                foreach($articles as $row) {
                    if(!empty($row['perex'])) {
                        $code .= $this->_params['item_before'];
                        
                        // Date.
                        if($this->_params['date_show'] == true) {
                    	    $code .= $this->_params['date_before'].date($this->_params['date_format'],$row['published']).$this->_params['date_after'];
                        }
                        
                        // Name.
                        if($this->_params['name_style'] == 'link') {
                            $code .= $this->_params['name_before'];
                            $code .= '<a href="'.$this->_baseUrlHelper->baseUrl().$this->_urlHelper->ptxUrl(array('url'=>$row['url_article'],'id'=>$row['id']),'article',true).'"
                                            title="'.$this->escape( PTX_String::wrapWord(strip_tags($row['perex']),15)).'" '.$this->_params['name_link_extra'].'>'.$this->escape($row[$this->_params['name_column']]).'</a>';
                            $code .= $this->_params['name_after'];
                        } else if($this->_params['name_style'] == 'text') {
                            $code .= $this->_params['name_before'].$this->escape($row[$this->_params['name_column']]).$this->_params['name_after'];
                        }
                        
                        // Text.
                        $code .= $this->_params['text_before'];
                        
                        // Image.
                        if($this->_params['image_show'] && !empty($row['image_file']) && file_exists('.'.$row['image_file'])) {
                            $code .= '<img src="'.$row['image_file'].'" alt="'.$this->escape($row[$this->_params['name_column']]).'" class="featured-picture" width="'.$this->_params['image_width'].'" />';                        
                        }
                        
                        if(!empty($this->_params['text_words_number'])) {
                            $code .= PTX_String::wrapWord(strip_tags($row['perex'],'<img><b><u><i><em><strong>'), $this->_params['text_words_number']);
                        } else {
                            if($this->_params['perex_strip'] == false) {
                                $code .= trim($row['perex']);
                            } else if(!empty($this->_params['perex_strip'])) {
                                $code .= trim(strip_tags($row['perex'],$this->_params['perex_strip']));
                            } else {
                                $code .= trim(strip_tags($row['perex']));
                            }
                        }    
                        $code .= $this->_params['text_after'];
                        
                        
                        // Link extra.
                        if($this->_params['link_extra'] == true) {
                            $code .= $this->_params['link_extra_before'];
                            $code .= '<a href="'.$this->_baseUrlHelper->baseUrl().$this->_urlHelper->ptxUrl(array('url'=>$row['url_article'],'id'=>$row['id']),'article',true).'"
                                            title="'.$this->escape( PTX_String::wrapWord(strip_tags($row['perex']),15)).'" '.$this->_params['link_extra_extra'].'>'.$this->escape($row[$this->_params['link_extra_column']]).'</a>';
                            $code .= $this->_params['link_extra_after'];
                        }
                        
                        $code .= $this->_params['item_after'];
                    }
                }
                $code .= $this->_params['block_after'];
            }
            
            // Save to cache.
            //$this->_cache->save($code,$ident);
        //}
        
        // Return.
        return $code;
    }
    
    /**
     * Banners.
     * 
     * builds banners widget
     * @param array $widgetData - data for widget
     * @return menu with categories
     */
    private function _banners($widgetData) {
        //$ident = 'widget'.(int)$widgetData['id'];
        
//        if(!($code = $this->_cache->load($ident))) {
            $code = null;
            
            // Parse params.
            $params = PTX_Parser::parseWidgetParam($widgetData['param']);
            $params['parent_type'] = $widgetData['parent_type'];
          
            // Sections.
            $explode = explode('#',$params['cats']);
            
            // Banners.
            $select = Admin_Model_DbSelect_Banners::pureSelect();
            $select->columns(array('id','name_'.$this->_locale.' as name','title_'.$this->_locale.' as title','link_'.$this->_locale.' as link','code','image_file','target'))
                ->where('parent_type = ? ','sections')->where('parent_id IN (?)',$explode)->where('status = 1')->order('position asc');
            $select->limit((int)$params['number']);
            $stmt = $select->query();
            $data = $stmt->fetchAll();
            
            
            if($params['shuffle'] == 1) {
            	shuffle($data);
            }
            
            // Merge params.
            if(isset($this->_params['banners'])) {
            	$this->_paramsBanners = array_merge($this->_paramsBanners,$this->_params['banners']);
            }
            
            if(!empty($data)) {
                require_once APPLICATION_PATH.'/helpers/Picture.php';
	            $pictureHelper = new Zend_View_Helper_Picture();
	            
            	$code .= $this->_params['block_before'];
	            foreach($data as $key => $values) {
	            	$this->_paramsBanners['image_alt'] = $this->escape($values['title']);
	            	
	            	$code .= $this->_params['item_before'];
	            	 
	            	$code .= $this->_params['banner_before'];
	            	    if(!empty($values['link'])) {
                            $code .= '<a href="'.$values['link'].'" title="'.$this->escape($values['title']).'" target="_blank">';
	            	    } 
	            	    if(!empty($values['image_file'])) {
                            $code .= $pictureHelper->picture($values['image_file'],$this->_paramsBanners);
	            	    }
	                    if(!empty($values['link'])) {
                            $code .= '</a>';
                        }
	            	$code .= $this->_params['banner_after'];
	            	$code .= $this->_params['item_after'];
	            }
                $code .= $this->_params['block_after'];
            }
            
            // Save to cache.
//            $this->_cache->save($code,$ident);
//        }
        
        // Return.
        return $code;
    }
    
    /**
     * Categories menu.
     * 
     * builds menu with categories
     * @param array $widgetData - data for widget
     * @return menu with categories
     */
    private function _categoriesMenu($widgetData) {
        $ident = 'widget'.(int)$widgetData['id'];
        
        if(!($code = $this->_cache->load($ident))) {
            $code = null;
            
            // Parse params.
            $params = PTX_Parser::parseWidgetParam($widgetData['param']);
            $params['parent_type'] = $widgetData['parent_type'];
            
            // Zend_Db_Select + Data
            $select = Default_Model_DbSelect_Categories::pureSelect();
            $select->columns(array('name','perex','url'))->where('status = 1')->where('parent_type = ?',$widgetData['content'])->order('position asc');
            $stmt = $select->query();
            $categories = $stmt->fetchAll();
    
            // Prepare code.
            if(is_array($categories) && count($categories) > 0) {
                if(isset($widgetData['show_name']) && $widgetData['show_name'] == 1) {
                    $code .= $this->_params['header_before'].$this->escape($widgetData['name']).$this->_params['header_after'];
                }
    
                $code .= $this->_params['block_before'];
                foreach($categories as $row) {
                    $code .= $this->_params['item_before'];
                    $code .= '<a href="'.$this->_baseUrlHelper->baseUrl().$this->_urlHelper->url(array('url'=>$row['url']),'article-category',true).'"
                                    title="'.$this->escape(strip_tags($row['perex'])).'">'.$this->escape($row['name']).'</a>';
                    $code .= $this->_params['item_after'];
                }
                $code .= $this->_params['block_after'];
            }
            
            // Save to cache.
            $this->_cache->save($code,$ident);
        }
        
        // Return.
        return $code;
    }
    
    /**
     * Get data.
     * 
     * return data for widget place
     * @param integer $placeId - id widget place
     * @return data for widget place
     */
    private function _getData($placeId) {
        $select = Admin_Model_DbSelect_Widgets::pureSelect();
        $select->columns(array('name_'.$this->_locale.' as name','content_'.$this->_locale.' as content','parent_type','show_name','param'))
            ->where('name_'.$this->_locale.' != ""')->where('status = ?',1)->order('position ASC')->where('widget_place_id = ?',(int)$placeId);
        $stmt = $select->query();
        $this->_data = $stmt->fetchAll();
    }
    
    /**
     * Complete select.
     * 
     * completes select query according to categories
     * @param $select - select
     * @param $params - parameters
     */
    private function _completeSelect($select,$params) {        
        // Just some categories.
        if(isset($params['cats'])) {
            $select->join(array('relation' => 'category_relations'),'articles.id = relation.parent_id')->where('relation.parent_type = ?','articles')->where('relation.category_id = ?',(int)$params['cats']);
        }
        // Order by.
        switch($params['parent_type']) {
            case 'articlelast':
                $select->order('articles.published desc');
                break;
            case 'articlerandom':
                // Sort options.
                $sortOptions = array('articles.name_'.$this->_locale,'articles.shown','articles.seo_keywords_'.$this->_locale,'articles.perex_'.$this->_locale);
                $sortWays = array('asc','desc');
                $randNumber = rand(0,count($sortOptions)-1);
                $randNumber2 = rand(0,count($sortWays)-1);
                $select->order($sortOptions[$randNumber].' '.$sortWays[$randNumber2]);
                break;
            case 'articleshown':
                $select->order('articles.shown desc');
                break;
        }
    }
    
    /**
     * Widget text.
     * 
     * returns code to print text widget
     * @param $widgetData - widget data
     * @return code
     */
    private function _widgetText($widgetData) {
        $code = $this->_params['block_before'];
        if(isset($widgetData['show_name']) && $widgetData['show_name'] == 1) {
            $code .= $this->_params['header_before'].$this->escape($widgetData['name']).$this->_params['header_after'];
        }

        if(isset($widgetData['content'])) {
            $code .= $this->_params['item_before'].$widgetData['content'].$this->_params['item_after'];
        }

        $code .= $this->_params['block_after'];

        return $code;
    }
    
    /**
     * Widget twitter.
     * 
     * returns code to print twitter widget
     * @param $widgetData - widget data
     * @return code
     */
    private function _widgetTwitter($widgetData) {
        $ident = 'widget'.(int)$widgetData['id'];
        
        if(!$code = $this->_cache->load($ident)) {
            $code = null;
            if(isset($widgetData['show_name']) && $widgetData['show_name'] == 1) {
                $code .= $this->_params['header_before'].$this->escape($widgetData['name']).$this->_params['header_after'];
            }
            
            // Require class.
            require_once APPLICATION_PATH .'/helpers/Twitter.php';
            $twitterObj = new Zend_View_Helper_Twitter();
            
            // Params.
            $params = array(
                'username' => 'pavlatom',
                'config' => APPLICATION_PATH.'/configs/'.PROJECT_FOLDER.'/config.tweet',
            );
            $code .= $twitterObj->twitter('usertimeline',$params);
        
            $code .= '<div class="fix"></div>';
            
            $this->_cache->save($code,$ident);
        }
        
        return $code;
    }
}