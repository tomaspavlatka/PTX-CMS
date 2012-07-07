<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 26.7.2010
**/

class Admin_LogController extends Zend_Controller_Action {
    
    /************** PUBLIC FUNCTION **************/
    /**
     * Init.
     */
    public function init() {
        $this->view->h1 = $this->view->trans('Logs');
        
        $response = $this->getResponse();
        $response->insert('menuTop',$this->view->render('_menu/top.phtml'));
        $response->insert('incTop',$this->view->render('_inc/top-row.phtml'));
        $response->insert('incLeft',$this->view->render('_inc/left.phtml'));
            
        PTX_Anchor::set($this->getRequest());
    }    
    
    /**
     * Archive.
     */
    public function archiveAction() {
    	$timeLimit = strtotime('-20 days');
    	
    	$logsObj = new Admin_Model_Logs();
    	$info = $logsObj->archive($timeLimit);
    	
    	PTX_Message::setMessage4User('admin','done',$this->view->transParam('<b>~0~ log(s)</b> have been archived.',array('~0~'=>$info['count']),false));
        $this->_redirect('/admin/log/list');
    }
    
    /**
     * Index.
     */
    public function indexAction() {
        $this->_redirect('/admin/log/list');
    }
    
    /**
     * List.     
     */
    public function listAction() {
        // Params.
        $params = array(
            'sort'   => $this->_getParam('sort','create2'),
            'input'  => $this->_getParam('input',25),
            'table'  => $this->_getParam('table',null),
            'user'   => $this->_getParam('user',null),
            'search' => $this->_getParam('search',null));
        
        // Zend_Db_Select.
        $select = Admin_Model_DbSelect_Logs::allList();
        $this->_completeSelectList($select,$params);
        
        // Paginator.
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setItemCountPerPage($params['input'])->setCurrentPageNumber($this->_getParam('page',1));
        foreach($paginator as &$row) {
            if($row['user_id'] == 999999) {
                $row['user'] = 'System';
            } else {
                $userObj = new Admin_Model_User($row['user_id']);
                $data = $userObj->getData(false,true);
                $row['user'] = (isset($data['name'])) ? $data['name'] : 'Unknown';
            }
        }
        $this->view->paginator = $paginator;
        
        // View variables.
        $this->view->msgs = PTX_Message::getMessage4User("admin");
        $this->view->sortPossibilities = $this->_getSortPossibilities();
        $this->view->tables = $this->_getTables();
        $this->view->users = $this->_getUsers();
        $this->view->params = $params;
        
        // Anchor.
        PTX_Anchor::createAnchor('admin');
        
        // Page setting.
        $this->view->h2 = $this->view->trans('List');
        $this->view->headTitle($this->view->h1.' - '.$this->view->h2, 'PREPEND');
    }
    
    /**
     * Transform. 
     */
    public function transformAction() {
        $link = '/admin/log/list';
        
        if($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            
            
            if(isset($data['sort']) && !empty($data['sort'])) {
                $link .= '/sort/'.urlencode($data['sort']);
            }
            
            if(isset($data['input']) && !empty($data['input'])) {
                $link .= '/input/'.urlencode($data['input']);
            }
            
            if(isset($data['search']) && !empty($data['search']) && $data['search'] != $this->view->trans('Search')) {
                $link .= '/search/'.urlencode($data['search']);
            }
            
            if(isset($data['table']) && !empty($data['table'])) {
                $link .= '/table/'.urlencode($data['table']);
            }
            
            if(isset($data['user']) && !empty($data['user'])) {
                $link .= '/user/'.urlencode($data['user']);
            }
        }
        
        $this->_redirect($link);
    }
    /************** PRIVATE FUNCTION **************/
    /**
     * Complete select list.
     * 
     * completes Zend_Db_Select instance
     * @param $select - select
     * @param $params - params
     */
    private function _completeSelectList(Zend_Db_Select $select,$params) {
        // Sort.
        if(!empty($params['sort'])) {
            switch($params['sort']) {
                case 'create':
                    $select->order('created asc');
                    break;
                case 'create2':
                    $select->order('created desc');
                    break;                   
            }
        }
        
        // Search.
        if(!empty($params['search'])) {
            $string = urldecode($params['search']);
            $select->where('log_table LIKE ? OR log_where LIKE ? OR log_content',"%{$string}%");
        }
        
        // Table.
        if(!empty($params['table'])) {
            $string = urldecode($params['table']);
            $select->where('log_table = ?',$params['table']);
        }
        
        // User.
        if(!empty($params['user'])) {
            $string = urldecode($params['user']);
            $select->where('user_id = ?',(int)$params['user']);
        }
    }
    
    /**
     * Get sort possibilities.
     * 
     * returns possibilities for list sorting 
     * @return possibilities for list sorting
     */
    private function _getSortPossibilities() {
        $possible = array(
            'create'   => $this->view->trans('Created 0-9'),
            'create2'  => $this->view->trans('Created 9-0'),
        );
        
        return $possible;
    }  

    /**
     * Get tables.
     * 
     * returns all tables
     * @return tables
     */
    private function _getTables() {
        $select = Admin_Model_DbSelect_Logs::pureSelect();
        $select->columns(array('DISTINCT(log_table)'))->order('log_table asc');
        $stmt = $select->query();
        $data = $stmt->fetchAll();
        
        $tables = array();
        if(is_array($data) && count($data) > 0) {
            foreach($data as $row) {
                $tables[$row['log_table']] = $row['log_table'];
            }
        }
        
        return $tables;
    }  

    /**
     * Get users.
     * 
     * returns all users
     * @return usrs
     */
    private function _getUsers() {
        $select = Admin_Model_DbSelect_Logs::pureSelect();
        $select->columns(array('DISTINCT(_logs.user_id),users.name,users.email'))->join('users','users.id = _logs.user_id',array())->order('users.name asc');
        $stmt = $select->query();
        $data = $stmt->fetchAll();
        
        $users = array();
        if(is_array($data) && count($data) > 0) {
            foreach($data as $row) {
                $users[$row['user_id']] = $row['name'];
            }
        }
        
        return $users;
    }  
}