<?php
class Default_Model_LibraryAcl extends Zend_Acl {
    public function __construct() {
        $this->addRole(new Zend_Acl_Role('guests'));
        $this->addRole(new Zend_Acl_Role('users'), 'guests');
        $this->addRole(new Zend_Acl_Role('admins'), 'users');
        $this->addRole(new Zend_Acl_Role('sadmins'), 'admins');		
			 
        $this->add(new Zend_Acl_Resource('admin'))
             ->add(new Zend_Acl_Resource('admin:admin-menu'), 'admin')
             ->add(new Zend_Acl_Resource('admin:category'), 'admin')
             ->add(new Zend_Acl_Resource('admin:article'), 'admin')
             ->add(new Zend_Acl_Resource('admin:ajax'), 'admin')
             ->add(new Zend_Acl_Resource('admin:banner'), 'admin')
             ->add(new Zend_Acl_Resource('admin:comment'), 'admin')
             ->add(new Zend_Acl_Resource('admin:index'), 'admin')
             ->add(new Zend_Acl_Resource('admin:language'), 'admin')
             ->add(new Zend_Acl_Resource('admin:location'), 'admin')
             ->add(new Zend_Acl_Resource('admin:log'), 'admin')
             ->add(new Zend_Acl_Resource('admin:manage'), 'admin')
             ->add(new Zend_Acl_Resource('admin:menu-input'), 'admin')
             ->add(new Zend_Acl_Resource('admin:menu-place'), 'admin')
             ->add(new Zend_Acl_Resource('admin:message'), 'admin')
             ->add(new Zend_Acl_Resource('admin:photo'), 'admin')
             ->add(new Zend_Acl_Resource('admin:replacer'), 'admin')
             ->add(new Zend_Acl_Resource('admin:section'), 'admin')
             ->add(new Zend_Acl_Resource('admin:setting'), 'admin')
             ->add(new Zend_Acl_Resource('admin:sitemap'), 'admin')
             ->add(new Zend_Acl_Resource('admin:static-page'), 'admin')
             ->add(new Zend_Acl_Resource('admin:tag'), 'admin')
             ->add(new Zend_Acl_Resource('admin:user'), 'admin')
             ->add(new Zend_Acl_Resource('admin:widget'), 'admin')
             ->add(new Zend_Acl_Resource('admin:widget-place'), 'admin');
             
        $this->add(new Zend_Acl_Resource('advert'))
             ->add(new Zend_Acl_Resource('advert:ajax'), 'advert')
             ->add(new Zend_Acl_Resource('advert:ad'), 'advert')
             ->add(new Zend_Acl_Resource('advert:index'), 'advert')
             ->add(new Zend_Acl_Resource('advert:manager'), 'advert');
             
        $this->add(new Zend_Acl_Resource('article'))
             ->add(new Zend_Acl_Resource('article:ajax'), 'article')
             ->add(new Zend_Acl_Resource('article:article'), 'article')
             ->add(new Zend_Acl_Resource('article:index'), 'article')
             ->add(new Zend_Acl_Resource('article:manager'), 'article');
        
        $this->add(new Zend_Acl_Resource('default'))
             ->add(new Zend_Acl_Resource('default:authentication'), 'default')
             ->add(new Zend_Acl_Resource('default:category'), 'default')
             ->add(new Zend_Acl_Resource('default:comment'), 'default')
             ->add(new Zend_Acl_Resource('default:error'), 'default')
             ->add(new Zend_Acl_Resource('default:form'), 'default')
             ->add(new Zend_Acl_Resource('default:index'), 'default')
             ->add(new Zend_Acl_Resource('default:manager'), 'default')
             ->add(new Zend_Acl_Resource('default:rss'), 'default')
             ->add(new Zend_Acl_Resource('default:search'), 'default')
             ->add(new Zend_Acl_Resource('default:static-page'), 'default')
             ->add(new Zend_Acl_Resource('default:tag'), 'default');
             
        $this->add(new Zend_Acl_Resource('photo'))
             ->add(new Zend_Acl_Resource('photo:index'), 'photo')
             ->add(new Zend_Acl_Resource('photo:category'), 'photo');
             
        // Guests.
        $this->allow('guests', 'advert:ajax',array('comment-form'));
        $this->allow('guests', 'advert:ad',array('add','detail','preview','revision'));
        $this->allow('guests', 'advert:index',array('index'));
        $this->allow('guests', 'advert:manager',array('sitemap'));

        $this->allow('guests', 'article:ajax',array('comment-form'));
        $this->allow('guests', 'article:article',array('detail','preview','revision'));
        $this->allow('guests', 'article:index',array('index'));
        $this->allow('guests', 'article:manager',array('language','sitemap'));
        
        $this->allow('guests', 'default:authentication', array('forgot','login'));
        $this->allow('guests', 'default:category', array('detail'));
        $this->allow('guests', 'default:comment', array('approve','delete'));
        $this->allow('guests', 'default:error', array('error'));
        $this->allow('guests', 'default:form', array('contact-form'));
		$this->allow('guests', 'default:index', array('index','picassa'));
		$this->allow('guests', 'default:manager',array('sitemap'));
		$this->allow('guests', 'default:rss',array('article'));
		$this->allow('guests', 'default:search',array('google'));
		$this->allow('guests', 'default:static-page',array('detail','preview','revision'));
		$this->allow('guests', 'default:tag', array('detail'));
		
		$this->allow('guests', 'photo:index',array('index'));
        $this->allow('guests', 'photo:category',array('list'));
		
		// Administrators.
		$this->allow('admins','admin:article',array('add','delete','delete-relative','edit','index','list','publish','relative','revision-activate','transform','unpublish'));
		$this->allow('admins','admin:ajax',array('add-to-category','article-picture','article-status','banner-status','category-picture','category-status','comment-status','fast-jump','language-status',
		                  'location-status','menu-input-status','photo-status','remove-from-category','replacer-status','save-order','section-status','static-page-picture','static-page-status',
		                  'tag-status','tag-relation-add','tag-relation-delete','widget-status'));
		$this->allow('admins','admin:banner',array('add','delete','edit','index','list','logo','position','transform'));
		$this->allow('admins','admin:category',array('add','delete','edit','import-picasa','index','list','position','transform'));
		$this->allow('admins','admin:comment',array('delete','edit','index','list','reply'));
		$this->allow('admins','admin:index',array('index'));
		$this->allow('admins','admin:location',array('add','delete','edit','index','list','position'));
		$this->allow('admins','admin:manage',array('advert','content','page','photogallery','setting'));
		$this->allow('admins','admin:menu-input',array('add','delete','edit','index','list','position','transform'));
		$this->allow('admins','admin:message',array('add','detail','forward','index','list','reply','sent','transform'));
		$this->allow('admins','admin:photo',array('add','delete','edit','index','list','organize','transform'));
		$this->allow('admins','admin:replacer',array('add','delete','edit','index','list','transform'));
        $this->allow('admins','admin:section',array('add','delete','edit','index','list','transform'));
		$this->allow('admins','admin:setting',array('clear-cache','email','index','seo'));
		$this->allow('admins','admin:sitemap',array('generate','index'));
		$this->allow('admins','admin:static-page',array('add','delete','edit','index','list','publish','revision-activate','transform','unpublish'));
		$this->allow('admins','admin:tag',array('add','delete','edit','index','list'));
		$this->allow('admins','admin:user',array('detail','edit','password'));
		$this->allow('admins','admin:widget',array('add','add-select','delete','detail','edit','index','list','position','transform'));
		$this->allow('admins','default:authentication','logout');
		
		// SuperAdministrators.
		$this->allow('sadmins','admin:admin-menu',array('add','delete','detail','edit','index','list','position'));
		$this->allow('sadmins','admin:ajax',array('admin-menu-status','widget-place-status','menu-place-status','user-status'));
		$this->allow('sadmins','admin:language',array('add','delete','edit','index','list','position','transform'));
		$this->allow('sadmins','admin:log',array('archive','index','list','transform'));
		$this->allow('sadmins','admin:manage',array('admin'));
		$this->allow('sadmins','admin:menu-place',array('add','delete','edit','index','list'));
		$this->allow('sadmins','admin:message',array('control'));
		$this->allow('sadmins','admin:user',array('add','delete','index','list','transform'));
		$this->allow('sadmins','admin:widget-place',array('add','delete','edit','index','list'));
    }
}