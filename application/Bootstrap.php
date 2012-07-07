<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
	/************ VARIABLES *************/
    private $_acl = null;
	private $_config;
	
	/************ PUBLIC FUNCTIONS *************/
    protected function _initAutoload() {        
        $modelLoader = new Zend_Application_Module_Autoloader(array(
                        'namespace' => 'Default',
                        'basePath' => APPLICATION_PATH.'/modules/default'));
        
		if(Zend_Auth::getInstance()->hasIdentity()) {
			Zend_Registry::set('role', Zend_Auth::getInstance()->getStorage()->read()->role);
		} else {
			Zend_Registry::set('role', 'guests');
		}
		
        $this->_acl = new Default_Model_LibraryAcl;
        $this->_auth = Zend_Auth::getInstance();
        Zend_Registry::set('PTX_Auth',$this->_auth);
        
        // nactem si config
        $this->_config = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini',APPLICATION_ENV);
        Zend_Registry::set('config',$this->_config);
        
        define('PROJECT_FOLDER',$this->_config->ptx->project->folder);
        define('SECURITY_SALT','OPj|ND`7\r(T]ye)Wtyx|l:zd6Pq9jVo=GJh((4Eh5Z?[Y-Os|*u5~E~+DQxc*j');
        
        Zend_Registry::set('Zend_Locale',$this->_config->ptx->project->locale);
        Zend_Registry::set('PTX_Locale',substr($this->_config->ptx->project->locale,0,2));
        
        $resource = $this->getPluginResource('db');
        $db = $resource->getDbAdapter();    
        Zend_Registry::set('db', $db);    
        $fc = Zend_Controller_Front::getInstance();
        $fc->registerPlugin(new Default_Plugin_AccessCheck($this->_acl));
        $fc->registerPlugin(new Default_Plugin_LayoutSwitcher());
        $fc->registerPlugin(new Default_Plugin_Locale());
        $fc->registerPlugin(new Default_Plugin_MenuInfo());
        $fc->registerPlugin(new Default_Plugin_Project($db));
        
        $this->_prepareRouter($fc);
                        
        return $modelLoader;
    }
	
	protected function _initViewHelpers() {
		$this->bootstrap('layout');
		$layout = $this->getResource('layout');
		$view = $layout->getView();
		$view->addHelperPath(APPLICATION_PATH.'/helpers', '');
		
		$this->_prepareCache();
        $this->_setTranslator();
        
        // Headers.
        $view->doctype('XHTML1_TRANSITIONAL');
        $view->headMeta()->appendHttpEquiv('Content-Type','text/html; charset=utf-8')
                         ->appendHttpEquiv('Content-Language', Zend_Registry::get('Zend_Locale'))
                         ->appendName('author', 'Tomas Pavlatka [http://pavlatka.cz]');

        $project = PTX_Session::getSessionValue('settings', 'project');
        if(isset($project['googleverify']) && !empty($project['googleverify'])) {
        	$view->headMeta()->appendName('google-site-verification',$project['googleverify']);
        }          
        
        $locale = Zend_Registry::get('PTX_Locale');
        if(isset($project['name'.$locale])) {
            $view->headTitle()->setSeparator(' | ')->headTitle($project['name'.$locale]);
        }
	}
	
	/**
	 * Prepare Caches (Private).
	 * 
	 * prepares cache.
	 * @return Zend_Cache
	 */
	private function _prepareCache() {
        $frontendOptions = array('lifetime' => 3600, 'automatic_serialization' => true);
        $backendOptions = array('cache_dir' => './tmp/cache/' );
        $cache = Zend_Cache::factory('Core','File',$frontendOptions,$backendOptions);
        Zend_Registry::set('ptxcache',$cache);
        Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
        
        $frontendOptions = array('lifetime' => 7200, 'automatic_serialization' => true);
        $backendOptions = array('cache_dir' => './tmp/cache/output/' );
        $output = Zend_Cache::factory('Output','File',$frontendOptions,$backendOptions);
        Zend_Registry::set('ptxcachelong',$output);
        
        $frontendOptions = array('lifetime' => 600, 'automatic_serialization' => true);
        $backendOptions = array('cache_dir' => './tmp/cache/' );
        $output = Zend_Cache::factory('Output','File',$frontendOptions,$backendOptions);
        Zend_Registry::set('ptxcacheshort',$output);
        
        Zend_Date::setOptions(array('cache' => $cache)); 
        Zend_Translate::setCache($cache);
        
        return $cache;
	}
	
    private function _prepareRouter(Zend_Controller_Front $fc){ 
        $router = $fc->getRouter(); 
        $locale = Zend_Registry::get('PTX_Locale');
        
        $langRoute = new Zend_Controller_Router_Route(':lang/',array('module'=>"default",'controller'=>'index','action'=>'index'),array('lang'=>'(en|ru)'));
        
        // Photo Category - http://domena.com/{category_url}/{page_number}
        $route = new Zend_Controller_Router_Route('/:url/:page',array('module'=>"default",'controller'=>'category','action'=>'detail','page'=>1),array('page'=>'\d+'));
        $routeLang = $langRoute->chain($route);
        $router->addRoute('photo-category-nl',$route);
        $router->addRoute('photo-category',$routeLang);
        
        // Article Category - http://domena.com/{category_url}/{page_number}
        $route = new Zend_Controller_Router_Route('/:url/:page',array('module'=>"default",'controller'=>'category','action'=>'detail','page'=>1),array('page'=>'\d+'));
        $routeLang = $langRoute->chain($route);
        $router->addRoute('article-categry-nl',$route);
        $router->addRoute('article-category',$routeLang);
        
        // Photogallery - http://domena.com/fotogalerie
        $route = new Zend_Controller_Router_Route('/fotogalerie',array('module'=>"photo",'controller'=>'index','action'=>'index'));
        $routeLang = $langRoute->chain($route);
        $router->addRoute('photogallery-nl',$route);
        $router->addRoute('photogallery',$routeLang);
        
        // Articles - http://domena.com/clanky
        $route = new Zend_Controller_Router_Route('/c',array('module'=>"article",'controller' => 'index','action' => 'index'));
        $routeLang = $langRoute->chain($route);
        $router->addRoute('articles-nl',$route);
        $router->addRoute('articles',$routeLang);
        
        // RSS - http://domena.com/rss
        $route = new Zend_Controller_Router_Route('/rss',array('module'=>"default",'controller' => 'rss','action' => 'article'));
        $routeLang = $langRoute->chain($route);
        $router->addRoute('rss-nl',$route);
        $router->addRoute('rss',$routeLang);

        // Article Tag - http://domena.com/clanky/stitek/{tag_url}/{page_number}
        $route = new Zend_Controller_Router_Route('/c/s/:url/:page',array('module'=>"default",'controller'=>'tag','action'=>'detail','page'=>1,'parent_type'=>'articles'),array('page'=>'\d+'));
        $routeLang = $langRoute->chain($route);
        $router->addRoute('article-tag-nl',$route);
        $router->addRoute('article-tag',$routeLang);
        
        // Article - http://domena.com/clanek/{article_url}
        $route = new Zend_Controller_Router_Route('c/:url',array('module'=>"article",'controller'=>'article','action'=>'detail'));
        $routeLang = $langRoute->chain($route);
        $router->addRoute('article-nl',$route);
        $router->addRoute('article',$routeLang);
        
        // Tags - http://domena.com/stitek/{tag_url}/{page_number}
        $route = new Zend_Controller_Router_Route('/s/:url/:page',array('module'=>"default",'controller' => 'tag','action' => 'list','page'=>1));
        $routeLang = $langRoute->chain($route);
        $router->addRoute('tag-nl',$route);
        $router->addRoute('tag',$routeLang);
        
        // Static Page - http://domena.com/{static_page_url}.html
        $route = new Zend_Controller_Router_Route_Regex('(.+).html',array('module'=>"default",'controller' => 'static-page','action' => 'detail'),array(1=>'url'),'%s.html');
        $routeLang = $langRoute->chain($route);
        $router->addRoute('static-page-nl',$route);
        $router->addRoute('static-page',$routeLang);
        
        $router->addRoute('homepage',$langRoute);
        
        // Admin - http://domena.com/admin
        $route = new Zend_Controller_Router_Route('/admin',array('module'=>"admin",'controller'=>'index','action'=>'index'));
        $router->addRoute('admin',$route);
        
        $router->addRoute('homepage',$langRoute);
        
        // Homepage - http://domena.com/
        $route = new Zend_Controller_Router_Route('/',array('module'=>"default",'controller'=>'index','action'=>'index'));
        $router->addRoute('homepage-nl',$route);
        
        return $router;
    }
	
	/**
	 * Set Translator.
	 */
	private function _setTranslator() {
		$locale = Zend_Registry::get('Zend_Locale');
        $translate = new Zend_Translate('gettext',APPLICATION_PATH .'/languages/cs.mo','cs');
        $translate
            ->addTranslation(APPLICATION_PATH .'/languages/en.mo','en')
            ->setLocale($locale);
        Zend_Registry::set('translate',$translate);
	}
}

