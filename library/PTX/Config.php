<?php
/**
 * Author: Tomas Pavlatka
 * Email: tomas.pavlatka@gmail.com
 * Created: 24.1.2010 7:32:01
 */
 
class PTX_Config {
	
	/************* PUBLIC STATIC FUNCTION **************/
	public static function generateDefaultTopMenu($menus, Admin_Model_Menus $menusObj, $xmlPath) {
		$config  = '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $config .= "<configdata>\n";
            $config .= "\t<nav>\n";
                $config .= self::generateDefaultMenuTree($menus,$menusObj,0);
          $config .= "\t</nav>\n";
        $config .= "</configdata>\n";
        $fp = fopen($xmlPath, 'w');
        fwrite($fp,$config);
        
        return true;
	}
	
	public static function generateAdminTopNavig($menus,Admin_Model_AdminMenus $menusObj, $xmlPath) {
		
		$config  = '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $config .= "<configdata>\n";
            $config .= "\t<nav>\n";
                $config .= self::generateAdminTopTree($menus,$menusObj,0);
		  $config .= "\t</nav>\n";
        $config .= "</configdata>\n";
		$fp = fopen($xmlPath, 'w');
        fwrite($fp,$config);
        
        return true;
	}
	
	public static function generateAdminTopTree($menus,$menusObj,$level) {
		$config = null;
		
		foreach($menus as $row) {
			$tabContainer = "\t\t".str_repeat("\t",$level);
			
			$config .= $tabContainer.'<'.$row['menu_controller'].$row['menu_action'].'>'."\n";
                $config .= self::_generatePage($row,$level);
			
				$children = $menusObj->getChildren($row['id_menu']," = 1");
				
				if(count($children) > 0) {
                    $config .= $tabContainer.'<pages>'."\n";	                				    
					$config .= self::generateAdminTopTree($children,$menusObj,($level+1));
					$config .= $tabContainer.'</pages>'."\n";
				}
            $config .= $tabContainer."</".$row['menu_controller'].$row['menu_action'].">\n";
		}
        
        return $config;
	}
	
    public static function generateDefaultMenuTree($menus,$menusObj,$level) {
    	$config = null;
        
        foreach($menus as $row) {
            $tabContainer = "\t\t".str_repeat("\t",$level);
            
            $config .= $tabContainer.'<menu'.$row['id_menu'].'>'."\n";
                $config .= self::_generateDefaultMenu($row,$level);
                
	            $children = $menusObj->getChildren($row['id_menu']," = 1");
	                
	            if(count($children) > 0) {
	               $config .= $tabContainer.'<pages>'."\n";                                        
	               $config .= self::generateDefaultMenuTree($children,$menusObj,($level+1));
	               $config .= $tabContainer.'</pages>'."\n";
                }
            $config .= $tabContainer."</menu".$row['id_menu'].">\n";
        }
        
        return $config;
    }
	/************* PRIVATE STATIC FUNCTION **************/
	private static function _generatePage($row,$level) {
		$tabPage = "\t\t\t".str_repeat("\t",$level);
		
            $page = $tabPage."<label>".$row['menu_name']."</label>\n";
            $page .= $tabPage."<module>".$row['menu_module']."</module>\n";
            $page .= $tabPage."<controller>".$row['menu_controller']."</controller>\n";
            $page .= $tabPage."<description>".$row['menu_description']."</description>\n";
            $page .= $tabPage."<action>".$row['menu_action']."</action>\n";
            $page .= $tabPage."<resource>".$row['menu_module'].":".$row['menu_controller']."</resource>\n";
            $page .= $tabPage."<privilege>".$row['menu_action']."</privilege>\n";
            
		return $page;
	}
	
	private static function _generateDefaultMenu($row,$level) {
        $tabPage = "\t\t\t".str_repeat("\t",$level);
        
            $page = $tabPage."<name>".$row['menu_name']."</name>\n";
            $page .= $tabPage."<link>".$row['menu_link']."</link>\n";
            $page .= $tabPage."<target>".$row['menu_target']."</target>\n";
            $page .= $tabPage."<title>".$row['menu_title']."</title>\n";
            $page .= $tabPage."<input>".$row['id_input']."</input>\n";
            $page .= $tabPage."<type>".$row['menu_type']."</type>\n";
            
        return $page;
	}
}