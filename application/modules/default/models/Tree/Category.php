<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 20.11.2010
**/

class Default_Model_Tree_Category extends Default_Model_Tree_AppModel {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct(); 
        
        $this->_name = 'categories';
        $this->_transFields = array('name','seo_keywords','perex','seo_description','url','url_hash');
    }
    
    /**
     * Get Categories.
     * 
     * returns array with categories.
     * @param string $type - parent type of categories
     * @param string $status - status of pages we need
     * @param array  $mandatory - mandatory fields
     * @param integer $skip - id of top record of branch in tree which must be skipped
     * @param string $returnType - type of return
     * @return array list of categories
     */
    public function getCategories($type, $status = " > -1", array $mandatory = array(), $skip = null, $returnType = 'array') {
        $tree = $this->getTree(0,0,$status,array('parent_type'=>$type,'mandatory'=>$mandatory));
        
        $list = array(); 
        foreach($tree as $key => $values) {
            if($values['id'] == $skip) {
                $level = $values['level'];
                continue;
            } else if (isset($level) && $level < $values['level']) {
                continue;
            } else {
                $level = null;
            }
            
            if($returnType == 'list') {
                $list[$values['id']] = str_repeat('-- ',$values['level']).$values['name'];
            } else {
                $list[] = $values;
            }
        }
        
        return (array)$list;
    }
    
   /**
     * Find top category url.
     * 
     * finds url for top category.
     * @param integer $categoryId - id of category
     * @param string $locale - locale
     * @return url about top parent.
     */
    public function getTopCategoryUrl($categoryId,$locale) {
        $topParent = $this->getTopParent($categoryId);
        
        if(!empty($topParent) && isset($topParent['url_'.$locale])) {
            return $topParent['url_'.$locale];
        } else {
            return null;
        }
    }
}