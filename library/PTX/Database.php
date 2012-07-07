<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Oct 12, 2011
 */

class PTX_Database {
    
    /*
     * Holds actual connection.
     */
    private $_dbConnect;
    
    /*
     * Private variable holding option for class.
     */
    private $_options = array(
        'db_host'       => '',
        'db_name'       => '',
        'db_user'       => '',
        'db_password'   => '',
        'db_charset'    => 'utf-8');

    private $_selectQuery;
    
    /*
     * Translatable Columns (Private).
     * 
     * settings for translations.
     */
    private $_translatableColumns = array(
           'admin_menus' => array(
               'proceed' => true,
               'columns' => array(
                   'description'     => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL',
                   'name'            => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL',
               ),
               'verify_column' => 'name'
            ),
        'articles' => array(
            'proceed' => true,
            'columns' => array(
               'seo_keywords'    => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL',
               'seo_description' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL',
               'content'         => 'text COLLATE utf8_unicode_ci',    
               'perex'           => 'text COLLATE utf8_unicode_ci',
               'url_hash'        => 'char(32) COLLATE utf8_unicode_ci NOT NULL',
               'url'             => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL',
               'name'            => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL',
               'short_name'      => 'varchar(100) COLLATE utf8_unicode_ci NULL',
            ),
            'verify_column' => 'name'
        ),
        'banners' => array(
            'proceed' => true,
            'columns' => array(   
               'link'            => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL',
               'title'           => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL',
               'name'            => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL',
            ),
            'verify_column' => 'name'
        ),
        'categories' => array(
            'proceed' => true,
            'columns' => array(
               'seo_keywords'    => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL',
               'seo_description' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL',
               'content'         => 'text COLLATE utf8_unicode_ci',    
               'perex'           => 'text COLLATE utf8_unicode_ci',
               'url_hash'        => 'char(32) COLLATE utf8_unicode_ci NOT NULL',
               'url'             => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL',
               'name'            => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL',
            ),
            'verify_column' => 'name'
        ),
        'languages' => array(
            'proceed' => true,
            'columns' => array(
               'name'            => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL',
            ),
            'verify_column' => 'name'
        ),
        'locations' => array(
            'proceed' => true,
            'columns' => array(
               'name'            => 'varchar(100) COLLATE utf8_unicode_ci NOT NULL',
            ),
            'verify_column' => 'name'
        ),
        'menu_inputs' => array(
            'proceed' => true,
            'columns' => array(
               'link'            => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL',
               'title'           => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL',
               'name'            => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL',
            ),
            'verify_column' => 'name'
        ),
        'menu_places' => array(
            'proceed' => true,
            'columns' => array(
               'name'            => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL',
            ),
            'verify_column' => 'name'
        ),
        'photos' => array(
           'proceed' => true,
           'columns' => array(
               'seo_keywords'    => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL',
               'seo_description' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL',
               'perex'           => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL',    
               'url_hash'        => 'char(32) COLLATE utf8_unicode_ci NOT NULL',
               'url'             => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL',
               'name'            => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL',
           ),
           'verify_column' => 'name'
        ),
        'replacers' => array(
           'proceed' => true,
           'columns' => array(
               'content'    => 'text COLLATE utf8_unicode_ci',
           ),
           'verify_column' => 'content'
        ),
        'sections' => array(
           'proceed' => true,
           'columns' => array(
               'name'    => 'varchar(150) COLLATE utf8_unicode_ci NOT NULL',
           ),
           'verify_column' => 'name'
        ),
        'static_pages' => array(
           'proceed' => true,
           'columns' => array(
               'seo_keywords'    => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL',
               'seo_description' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL',
               'content'         => 'text COLLATE utf8_unicode_ci',    
               'url_hash'        => 'char(32) COLLATE utf8_unicode_ci NOT NULL',
               'url'             => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL',
               'name'            => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL',
           ),
           'verify_column' => 'name'
        ),
        'tags' => array(
           'proceed' => true,
           'columns' => array(
               'url_hash'        => 'char(32) COLLATE utf8_unicode_ci NOT NULL',
               'url'             => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL',
               'name'            => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL',
           ),
           'verify_column' => 'name'
        ),
        'widget_places' => array(
           'proceed' => true,
           'columns' => array(
               'name'            => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL',
           ),
           'verify_column' => 'name'
        ),
        'widgets' => array(
           'proceed' => true,
           'columns' => array(
               'content'         => 'text COLLATE utf8_unicode_ci',
               'name'            => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL',
           ),
           'verify_column' => 'name'
        ),
    );
    
    /**
     * Construct.
     * 
     * constructor of the class
     * @param array $options - parameters for class
     */
    public function __construct(array $options = array()) {
        $this->_options = array_merge($this->_options,$options);
    }
    
    /**
     * Add language.
     * 
     * adds needed columns for each table for specific language.
     * @param string $code - code of language
     */
    public function addLanguage($code) {
        $logs = array();
        
        foreach($this->_translatableColumns as $table => $values) {
            $this->_selectQuery = mysql_query("DESCRIBE `{$table}`",$this->_dbConnect);
            $tableFields = array();
            while($row = @mysql_fetch_assoc($this->_selectQuery)) {
                $tableFields[] = $row;
            }            
            
            foreach($tableFields as $fieldKey => $fieldValues) {
                if($fieldValues['Field'] == $values['verify_column'].'_'.$code) {
                    $values['proceed'] = false;
                    break;
                }
            }
            if($values['proceed']) {
                $query = "ALTER TABLE `{$table}` ";
                foreach($values['columns'] as $columnName => $columnData) {
                    $query .= "ADD `{$columnName}_{$code}` {$columnData} AFTER `id`,";
                    
                    if(isset($logs[$table])) {
                        $logs[$table][] = "{$columnName}_{$code}";
                    } else {
                        $logs[$table] = array("{$columnName}_{$code}");
                    } 
                }
                
                $query = substr($query,0,-1).';';
                mysql_query($query,$this->_dbConnect);
            }
        }
        
        return (array)$logs;
    }
    
    /**
     * Remove language.
     * 
     * removes columns from database.
     * @param string $code - code of language
     * @return array with information     
     */                        
    public function removeLanguage($code) {
        $logs = array();
        foreach($this->_translatableColumns as $table => $values) {
            $this->_selectQuery = mysql_query("DESCRIBE `{$table}`",$this->_dbConnect);
            $tableFields = array();
            while($row = @mysql_fetch_assoc($this->_selectQuery)) {
                $tableFields[] = $row;
            }            
        
            $delete = array();    
            foreach($tableFields as $fieldKey => $fieldValues) {
                if(strstr($fieldValues['Field'],'_'.$code)) {
                    $delete[] = $fieldValues['Field'];
                }
            }
            
            if(!empty($delete)) {
                $query = "ALTER TABLE `{$table}` ";
                foreach($delete as $columnKey => $columnName) {
                    $query .= "DROP `{$columnName}`,";
                    
                    if(isset($logs[$table])) {
                        $logs[$table][] = $columnName;
                    } else {
                        $logs[$table] = array($columnName);
                    }                               
                }
                
                $query = substr($query,0,-1).';';
                mysql_query($query,$this->_dbConnect);
            }
        }
        
        return (array)$logs;
    }
    
    /**
     * Connect.
     * 
     * connects to the database.
     */
    public function connect() {
       
        if(!($this->_dbConnect = @mysql_connect($this->_options['db_host'],$this->_options['db_user'],$this->_options['db_password']))) {
            exit('Unable to connect to database');
        }
        if(!@mysql_select_db($this->_options['db_name'], $this->_dbConnect)) {
            exit(sprintf('Unable to connect to database %s',$this->_options['db_name']));
        }
        
        // Set charset.
        mysql_query("SET CHARACTER SET ".$this->_options['db_charset'],$this->_dbConnect);
    }
    
    /**
     * Export.
     * 
     * exports database into text
     * @param array $tables - list of the table
     * @return exported data
     */
    public function dbExport(array $tables) {
        $exportData = null;
        
        foreach($tables as $table) {
            $exportData .= $this->_createExportHeader($table);
            $exportData .= $this->_getExportData($table);
        }
        
        return $exportData;
    }
    
    /**
     * Mysql Fetch Array.
     * 
     * fetch array from database.
     */
    public function mysqlFetchArray() {
        return @mysql_fetch_array($this->_selectQuery);
    }
    
    /**
     * Mysql Fetch Assoc.
     * 
     * fetch array assoc from database.
     */
    public function mysqlFetchAssoc() {
        return @mysql_fetch_assoc($this->_selectQuery);
    }
    
    /**
     * Mysql List Tables.
     * 
     * mysql_query to find all tables in database.
     * @return true | false
     */
    public function mysqlListTables() {
        if(!$this->_selectQuery = @mysql_query("SHOW TABLES FROM ".$this->_options['db_name'],$this->_dbConnect)) {
            return false;
        } else {
            return true;    
        }
    }
    
    /**
     * Create export header (Private).
     * 
     * creates header for export
     * @param $table - name of a table
     * @return header
     */
    private function _createExportHeader($table) {
        // Fields.
        $fields = array();
        $query = mysql_query("DESCRIBE `{$table}`",$this->_dbConnect);
        while($field = mysql_fetch_assoc($query,$this->_dbConnect)) {
              $fields[] = $field;
        }
        
        // Indexes.
        $indexes = array();
        $query = mysql_query("SHOW INDEXES FROM `{$table}`",$this->_dbConnect);
        while($index = mysql_fetch_assoc($query,$this->_dbConnect)) {
            if(isset($indexes[$index['Key_name']])) {
                $indexes[$index['Key_name']][] = $index;
            } else {
                $indexes[$index['Key_name']] = array($index);
            }
        }
        
        // Table status.
        $query = mysql_query("SHOW TABLE STATUS WHERE `Name` = '{$table}'",$this->_dbConnect);
        $status = mysql_fetch_assoc($query,$this->_dbConnect);
        
        // Table header.
        $header  = "DROP TABLE IF EXISTS `{$table}`;\n";
        $header .= "CREATE TABLE `{$table}` (\n";
        foreach($fields as $key => $values) {
            $header .= "\t`".$values['Field']."` ".$values['Type'];
            if($values['Null'] == 'NO') {
                $header .= " not null";
            } else {
                $header .= " null";
            }
            
            // TODO: What about MySQL constants ?
            if(!empty($values['Default'])) {
                if($values['Default'] == 'CURRENT_TIMESTAMP') {
                    $header .= " default ".$values['Default'];
                } else {
                    $header .= " default '".$values['Default']."'";
                }
            }
            if(!empty($values['Extra'])) {
                $header .= " ".$values['Extra'];
            }
            
            $header .= ",\n";
        }
        
        $countIndexes = count($indexes);
        $counter = 1;
        foreach($indexes as $indexKey => $indexValeus) {
            if($indexKey == 'PRIMARY') {
                $header .= "\tPRIMARY KEY (";
                    $keyString = null;
                    foreach($indexValeus as $indKey => $indValues) {
                        $keyString .= "`".$indValues['Column_name']."`,";
                    }
                    $header .= substr($keyString,0,-1);
            } else {
                $header .= "\tKEY `".$indexKey."` (";
                    $keyString = null;
                    foreach($indexValeus as $indKey => $indValues) {
                        
                        $keyString .= "`".$indValues['Column_name']."`";
                        
                        if(!empty($indValues['Sub_part'])) {
                            $keyString .= ' ('.$indValues['Sub_part'].')';
                        }
                        $keyString .= ",";
                    }
                    $header .= substr($keyString,0,-1);
            }
            
            if($counter++ < $countIndexes) {
                $header .= "),\n";
            } else {
                $header .= ")\n";
            }
        }
        
        $header .= ") ";
        
        // Additional information.
        $charsetExplode = explode('_',$status['Collation']);
        $status['Charset'] = (isset($charsetExplode[0])) ? $charsetExplode[0] : null;
        $additionalInfo = array('Engine' => 'ENGINE','Auto_increment' => 'AUTO_INCREMENT', 'Charset' => 'CHARSET', 'Collation'  => 'COLLATE');
        foreach($additionalInfo as $key => $name) {
            if(isset($status[$key]) && !empty($status[$key])) {
                $header .= $name .'='.$status[$key]." ";
            } 
        }
        $header .= ";\n\n";
        
        return (string)$header;
    }

    /**
     * Get Export Data (Private).
     * 
     * exports records from database.
     * @param $table - name of a table
     * @return export of records
     */
    private function _getExportData($table) {
        $d = null;
        $data = mysql_query("SELECT * FROM `{$table}` WHERE 1", $this->_dbConnect);
        
        $counter = 1;
        $insertData = null;
        $rowCounter = mysql_num_rows($data,$this->_dbConnect);
        if($rowCounter > 0) {
            while($row = mysql_fetch_assoc($data,$this->_dbConnect)) {
                if($counter == 1) {
                    $insertData .= "INSERT INTO `{$table}` ";
                        $columns = array_keys($row);
                        $columnNames = null;
                        foreach($columns as $key => $column) {
                            $columnsNames .= "`{$column}`,";
                        }
                    $insertData .= "(".substr($columnsNames,0,-1).") VALUES \n";
                } 
                
                $insertData .= "(";
                $rowData = null;
                foreach($row as $column => $value) {
                    $rowData .= "'".mysql_escape_string($value)."',";
                }
                $insertData .= substr($rowData,0,-1);
                
                if($counter < $rowCounter) {
                    $insertData .= "),\n";
                } else {
                    $insertData .= ");\n\n";
                }
                
                // Increase counter;
                $counter++;
            }
        }
        
        // Return data.
        return (string)$insertData;
    }
}