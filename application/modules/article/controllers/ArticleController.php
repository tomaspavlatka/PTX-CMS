<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 23.8.2010
**/

class Article_ArticleController extends Zend_Controller_Action {

    /**
     * Init.
     */
    public function init() {
        $response = $this->getResponse();
        
        // Widgets.
        $response->insert('widgetFooter',$this->view->render('_widget/footer.phtml'));
        $response->insert('widgetSidebar',$this->view->render('_widget/sidebar.phtml'));
        $response->insert('widgetTop',$this->view->render('_widget/top.phtml'));
        $response->insert('widgetUnder',$this->view->render('_widget/under.phtml'));
        
        // Menu
        $response->insert('menuHeader',$this->view->render('_menu/header.phtml'));
        $response->insert('menuMain',$this->view->render('_menu/main.phtml'));
        $response->insert('menuBottom',$this->view->render('_menu/bottom.phtml'));

        // projectconfig
        $this->_config = PTX_Session::getSessionValue('settings', 'project');
        $this->_locale = Zend_Registry::get('PTX_Locale');
        $this->view->locale = Zend_Registry::get('PTX_Locale');
    }

    /**
     * Detail.
     */
    public function detailAction() {
        $params = $this->_getAllParams();
        
        // Data.
        $url = $this->_getParam('url');
        $articleObj = $this->_getArticleObj($url);
        if(!($articleObj instanceof Article_Model_Article)) {
            throw new PTX_Exception(__CLASS__.": This article is not for visitors [url:{$url}]");
        } 
        $articleData = $articleObj->getData(false,true);
        
        if(!isset($articleData['status']) || $articleData['status'] != 1) {
            throw new PTX_Exception(__CLASS__.": This article is not for visitors [id:{$params['id']}][url:{$params['url']}][caturl:{$params['caturl']}]");
        } else if(empty($articleData['name_'.$this->_locale]) || empty($articleData['content_'.$this->_locale])) {
            throw new PTX_Exception(__CLASS__.": This article is not for visitors [id:{$params['id']}][url:{$params['url']}][caturl:{$params['caturl']}]");
        }
        
        // Form.
        $form = new Default_Form_Comment();
        $this->view->form = $this->_detailForm($this->getRequest(),$form,$articleObj);
        
        // Update shown.
        $articleObj->updateShown();
        
        $categoriesIds = Admin_Model_DbSelect_CategoryRelations::get4Parent('articles',$articleData['id']," = 1");
        if(!empty($categoriesIds)) {
            $select = Admin_Model_DbSelect_Categories::pureSelect();
            $select->columns(array('name_'.$this->_locale.' as name','url_'.$this->_locale.' as url'))
                ->where('status = 1')->where('id IN (?)',$categoriesIds)->where('name_'.$this->_locale.' != ""')->order('name_'.$this->_locale.' ASC');
            $stmt = $select->query();
            $categories = $stmt->fetchAll();
        } else {
            $categories = array();
        }
        $this->view->categories = $categories;
        
        // Tags.
        $relationObj = new Default_Model_TagRelations();
        $tags = $relationObj->get4Parent($articleData['id'], 'articles', 'list');
        if(is_array($tags) && count($tags) > 0) {
            $select = Default_Model_DbSelect_Tags::pureSelect();
            $select->columns(array('name_'.$this->_locale.' as name','url_'.$this->_locale.' as url'))->where('status = 1')->where('id IN (?)',$tags)
                ->where('name_'.$this->_locale.' != ""');
            $stmt = $select->query();
            $this->view->tags = $stmt->fetchAll();
        }
        
        // Comments.
        $treeObj = new Default_Model_Tree_Comment();
        $params = array(
            'parent_type' => 'articles',
            'parent_id'   => $articleData['id']);
        $this->view->comments = $treeObj->getTree(0,0," = 1",$params);
        
        // View variables.
        $this->view->articleData = $articleData;
        $this->view->msgs = PTX_Message::getMessage4User('user');
        
        // Page setting.
        $this->view->headTitle($articleData['name_'.$this->_locale] , 'PREPEND');
        $this->view->headMeta()->appendName('description', $articleData['seo_description_'.$this->_locale]);
        $this->view->headMeta()->appendName('keywords', $articleData['seo_keywords_'.$this->_locale]);
        $this->view->headMeta()->appendName('robots','index,follow');
        $this->view->headMeta()->appendName('googlebot','snippet,archive');
    }
    
    
    /**
     * Preview.
     */
    public function previewAction() {
        $params = $this->_getAllParams();
        
        // Article data.
        $articleObj = new Article_Model_Article($params['id']);
        $articleData = $articleObj->getData(false,true);
        
        if(!isset($articleData['status']) || $articleData['status'] < 0) {
            throw new PTX_Exception(__CLASS__.": This article is not for visitors [id:{$params['id']}][url:{$params['url']}][caturl:{$params['caturl']}]");
        } else if(empty($articleData['name_'.$this->_locale]) || empty($articleData['content_'.$this->_locale])) {
            throw new PTX_Exception(__CLASS__.": This article is not for visitors [id:{$params['id']}][url:{$params['url']}][caturl:{$params['caturl']}]");
        }
        
        // Category data.
        $catObj = new Default_Model_Category($articleData['category_id']);
        $catData = $catObj->getData(false,true);
    
        if(!isset($catData['status']) || $catData['status'] < 0 || empty($catData['name_'.$this->_locale])) {
            throw new PTX_Exception(__CLASS__.": This article is in category which is not for visitors [id:{$params['id']}][url:{$params['url']}][caturl:{$params['caturl']}]");
        } else if($catData['url_'.$this->_locale] != $params['caturl'] || $articleData['url_'.$this->_locale] != $params['url']) {
            $this->_redirect($this->view->ptxUrl(array('id'=>$articleData['id'],'url'=>$articleData['url_'.$this->_locale],'caturl'=>$catData['url_'.$this->_locale]),'article',true),array('code'=>301));
        }
        
        // Form.
        $form = new Default_Form_Comment();
        $this->view->form = $this->_detailForm($this->getRequest(),$form,$articleObj,$catObj);
        
        // Update shown.
        $articleObj->updateShown();
        
        // Tags.
        $relationObj = new Default_Model_TagRelations();
        $tags = $relationObj->get4Parent($articleData['id'], 'articles', 'list');
        if(is_array($tags) && count($tags) > 0) {
            $select = Default_Model_DbSelect_Tags::pureSelect();
            $select->columns(array('name_'.$this->_locale.' as name','url_'.$this->_locale.' as url'))->where('status = 1')->where('id IN (?)',$tags)
                ->where('name_'.$this->_locale.' != ""');
            $stmt = $select->query();
            $this->view->tags = $stmt->fetchAll();
        }
        
        // View variables.
        $this->view->articleData = $articleData;
        $this->view->catData = $catData;
        $this->view->msgs = PTX_Message::getMessage4User('user');
        
        // Page setting.
        $this->view->headTitle($articleData['name_'.$this->_locale] .' - '.$catData['name_'.$this->_locale], 'PREPEND');
        $this->view->headMeta()->appendName('description', $articleData['seo_description_'.$this->_locale]);
        $this->view->headMeta()->appendName('keywords', $articleData['seo_keywords_'.$this->_locale]);
        $this->view->headMeta()->appendName('robots','index,follow');
        $this->view->headMeta()->appendName('googlebot','snippet,archive');
    }
    
    /**
     * Revision
     * 
     * shows revision of text page
     */
    public function revisionAction() {
        // Revision.
        $revisionId = $this->_getParam('id');
        $revisionObj = new Admin_Model_ContentHistory($revisionId);
        $revisionData = $revisionObj->getData();

        // Article.
        $articleObj = new Article_Model_Article($revisionData->content_id);
        $articleData = $articleObj->getData();
        
        // Category.
        $catObj = new Default_Model_Category($articleData['category_id']);
        $catData = $catObj->getData();
        
        // View variables.
        $this->view->articleData = $articleData;
        $this->view->revisionData = $revisionData;
        $this->view->catData = $catData;
        
        // Page META data.
        $this->view->headTitle($articleData->name, 'PREPEND');
        $this->view->headMeta()->appendName('description', $articleData->seo_description);
        $this->view->headMeta()->appendName('keywords', $articleData->seo_keywords);
        $this->view->headMeta()->appendName('robots','noindex,nofollow');
        $this->view->headMeta()->appendName('googlebot','nosnippet,noarchive');
    }

    /**
     * Detail form.
     * 
     * manages form
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Form $form
     * @param Article_Model_Article $articleObj
     * @return Zend_Form
     */
    private function _detailForm(Zend_Controller_Request_Http $request, Zend_Form $form, Article_Model_Article $articleObj) {
        
        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                $formData = $form->getValues();
                $articleData = $articleObj->getData(false,true);
                $approvalCode = PTX_Password::randomInt(7);
                
                // akismet test
                $akismetObj = new PTX_Akismet();
                $akismetJudge = $akismetObj->isSpam($formData);
                
                // save new comment
                $commentsObj = new Default_Model_Comments();
                $params = array(
                    'parent_type'   => 'articles',
                    'parent_id'     => $articleData['id'],
                    'approval_code' => $approvalCode,
                    'akismet'       => $akismetJudge);
                $commentId = $commentsObj->save($formData,$params);
                
                // send email to administrator
                $mailerObj = new Default_Model_Mailer('utf-8');
                $params = array(
                    'comment_id' => $commentId,
                    'locale'     => $this->_locale,
                    'akismet'    => $akismetJudge,
                    'approval_code' => $approvalCode,
                    'akismet_text'  => ($akismetJudge) ? $this->view->trans('IS SPAM') : $this->view->trans('Not Spam'),
                    'article_data'  => $articleData);
                $mailerObj->comment($formData,$params);
                
                PTX_Message::setMessage4User('user','done',$this->view->trans('Thank you for your comment. According to rules of page - all comments must be approved by administrator.'));
                $this->_redirect($this->view->ptxUrl(array('url'=>$articleData['url_'.$this->_locale],),'article',true),array('code'=>301));
            }
        } 
        
        return $form;
    }
    
    /**
     * Get data 4 relatives.
     * 
     * returns data 4 relatives.
     * @param $relatives - relatives
     * @return data 4 link
     */
    private function _getData4Relatives($relatives) {
        if($relatives instanceof Zend_Db_Table_Rowset) {
            $relatives = $relatives->toArray();
            $idsArray = PTX_Page::getIdsArray($relatives, 'relative_id');
            $idsArray = array_unique($idsArray);
            
            if(is_array($idsArray) && count($idsArray) > 0) {
                $select = Article_Model_DbSelect_Articles::pureSelect();
                $select
                    ->columns(array('id','category_id','name','url'))
                    ->where('status = 1')
                    ->where('id IN (?)',$idsArray)
                    ->order('published desc');
                $stmt = $select->query();
                $data = $stmt->fetchAll();
            
                if(is_array($data) && count($data) > 0) {
                    $fullData = array();
                    $categoryArray = $this->_getCategoryArray();
                        
                    foreach($data as $row) {
                        if(array_key_exists($row['category_id'],$categoryArray)) {
                            $row['category-url'] = $categoryArray[$row['category_id']];
                            $fullData[] = $row;
                        }
                    }
                    
                    return $fullData;
                }
            }
        }
        
        return array();
    }
    
    /**
     * Get article obj
     * 
     * return instace of article class according to url
     * @param $url - url 
     * @return article obj
     */
    private function _getArticleObj($url) {
        $hash = md5(trim($url));
        $articlesObj = new Article_Model_Articles();
        $articleData = $articlesObj->findByUrlHash($hash," = 1");

        if(isset($articleData['id'])) {
            return new Article_Model_Article($articleData['id']);
        } else {
            $select = Default_Model_DbSelect_UrlBackups::pureSelect();
            $select->columns(array('DISTINCT(parent_id)'))->where("parent_type = 'articles'")->where('url_hash = ?',$hash)->where('locale = ?',$this->_locale)->order('id desc');
            $stmt = $select->query();
            $data = $stmt->fetchAll();
            
            if(is_array($data) && count($data) > 0) {
                foreach($data as $row) {
                    $articleObj = new Article_Model_Articles($row['parent_id']);
                    $articleData = $articleObj->getData(false,true);
                    
                    if(isset($articleData['status']) && $articleData['status'] == 1 && !empty($articleData['name_'.$this->_locale])) {
                        $this->_redirect($this->view->baseUrl().$this->view->ptxUrl(array('url'=>$articleData['url_'.$this->_locale]),'article',true),array('code'=>301));
                    }
                }
            }
        }
    }
    
    /**
     * Get category arrah.
     * 
     * return category array (id_category => url, ....);
     * @return category array (id_category => url, ....);
     */
    private function _getCategoryArray() {
        $select = Default_Model_DbSelect_Categories::pureSelect();
        $select
            ->columns(array('id','url'))
            ->where('status = 1')
            ->order('id asc');
        $stmt = $select->query();
        $data = $stmt->fetchAll();

        $categoryArray = array();
        foreach($data as $row) {
            $categoryArray[$row['id']] = $row['url'];
        }
        
        return $categoryArray;
    }
}