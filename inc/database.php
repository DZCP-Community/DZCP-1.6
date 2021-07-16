<?php
/**
 * DZCP - deV!L`z ClanPortal - Mainpage ( dzcp.de )
 * deV!L`z Clanportal ist ein Produkt von CodeKing,
 * geändert durch my-STARMEDIA und Codedesigns.
 *
 * Diese Datei ist ein Bestandteil von dzcp.de
 * Diese Version wurde speziell von Lucas Brucksch (Codedesigns) für dzcp.de entworfen bzw. verändert.
 * Eine Weitergabe dieser Datei außerhalb von dzcp.de ist nicht gestattet.
 * Sie darf nur für die Private Nutzung (nicht kommerzielle Nutzung) verwendet werden.
 *
 * Homepage: http://www.dzcp.de
 * E-Mail: info@web-customs.com
 * E-Mail: lbrucksch@codedesigns.de
 * Copyright 2017 © CodeKing, my-STARMEDIA, Codedesigns
 */

//Debugging PDO
define('pdo_disable_update_statement', false);
define('pdo_disable_insert_statement', false);
define('pdo_disable_delete_statement', false);

final class database {
    protected $dbConf = [];
    protected $instances = [];

    protected $active = false;
    protected $dbHandle = null;
    protected $lastInsertId = false;
    protected $rowCount = false;
    protected $queryCounter = 0;
    protected $active_driver = '';
    protected $connection_pooling = true;
    protected $connection_encrypting = true;
    protected $mysql_buffered_query = true;

    public function cloneConfig($active = "default",$from = "new") { 
        if (!isset($this->dbConf[$active])) {
            throw new Exception("Unexisting db-config $active");
        }
        
        $this->dbConf[$from] = $this->dbConf[$active];
    }

    public function setConfig($active = "default", array $data) {
        if(isset($data['db']) && isset($data['db_host']) && isset($data['db_host']) && isset($data['db_user']) && isset($data['db_pw'])) {
            $this->dbConf[$active] = $data;
        }
    }

    public final function getInstance($active = "default") {
        if(pdo_disable_update_statement) {
            DebugConsole::insert_error('database::update', 'PDO-Update statement is disabled!!!');
        }

        if(pdo_disable_insert_statement) {
            DebugConsole::insert_error('database::insert', 'PDO-Insert statement is disabled!!!');
        }

        if(pdo_disable_delete_statement) {
            DebugConsole::insert_error('database::delete', 'PDO-Delete statement is disabled!!!');
        }
        
        if (!isset($this->dbConf[$active])) {
            throw new Exception("Unexisting db-config $active");
        }

        if (!isset($this->instances[$active]) || $this->instances[$active] instanceOf database === false) {
            $this->instances[$active] = new database();
            $this->instances[$active]->setConfig($active,$this->dbConf[$active]);
            if($active == 'test') {
                return $this->instances[$active]->connect($active);
            } else {
                $this->instances[$active]->connect($active);
            }
        }

        return $this->instances[$active];
    }

    public final function disconnect($active = "") {
        if(empty($active)) {
            unset($this->instances[$this->active]);
        } else {
            unset($this->instances[$active]);
        }

        $this->dbHandle = null;
    }

    public function getHandle() {
        return $this->dbHandle;
    }

    public function lastInsertId(): int {
        return $this->lastInsertId;
    }

    public function rowCount(): int {
        return $this->rowCount;
    }
    
    public function rows($qry, array $params = []): int {
        if (($type = $this->getQueryType($qry)) !== "select" && 
                ($type = $this->getQueryType($qry)) !== "show") {
            DebugConsole::sql_error_Exception("Incorrect Select Query",$qry,$params);
            DebugConsole::insert_error('database::rows','Incorrect Select Query!');
            DebugConsole::insert_sql_info('database::rows',$qry,$params);
            return 0;
        }

        $this->run_query($qry, $params, $type);
        return $this->rowCount;
    }
    
    public function delete($qry, array $params = []) {
        if(pdo_disable_delete_statement) {
            return false;
        }
        
        if (($type = $this->getQueryType($qry)) !== "delete" && $type !== "drop") {
            DebugConsole::sql_error_Exception("Incorrect Delete Query",$qry,$params);
            DebugConsole::insert_error('database::delete','Incorrect Delete/Drop Query!');
            DebugConsole::insert_sql_info('database::delete',$qry,$params);
            return false;
        }

        return $this->run_query($qry, $params, $type);
    }

    public function update($qry, array $params = []) {
        if(pdo_disable_update_statement) {
            return false;
        }
        
        if (($type = $this->getQueryType($qry)) !== "update") {
            DebugConsole::sql_error_Exception("Incorrect Update Query",$qry,$params);
            DebugConsole::insert_error('database::update','Incorrect Update Query!');
            DebugConsole::insert_sql_info('database::update',$qry,$params);
            return false;
        }

        return $this->run_query($qry, $params, $type);
    }

    public function insert($qry, array $params = []) {
        if(pdo_disable_insert_statement) {
            return false;
        }
        
        if (($type = $this->getQueryType($qry)) !== "insert") {
            DebugConsole::sql_error_Exception("Incorrect Insert Query",$qry,$params);
            DebugConsole::insert_error('database::insert','Incorrect Insert Query!');
            DebugConsole::insert_sql_info('database::insert',$qry,$params);
            return false;
        }

        return $this->run_query($qry, $params, $type);
    }

    public function select($qry, array $params = []) {
        if (($type = $this->getQueryType($qry)) !== "select") {
            DebugConsole::sql_error_Exception("Incorrect Select Query",$qry,$params);
            DebugConsole::insert_error('database::select','Incorrect Select Query!');
            DebugConsole::insert_sql_info('database::select',$qry,$params);
            return [];
        }

        if ($stmnt = $this->run_query($qry, $params, $type)) {
            return $stmnt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return [];
        }
    }

    public function fetch($qry, array $params = [], $field = null) {
        if (($type = $this->getQueryType($qry)) !== "select") {
            DebugConsole::sql_error_Exception("Incorrect Select Query",$qry,$params);
            DebugConsole::insert_error('database::selectSingle','Incorrect Select Query!');
            DebugConsole::insert_sql_info('database::selectSingle',$qry,$params);
            return false;
        }

        if ($stmnt = $this->run_query($qry, $params, $type)) {
            $res = $stmnt->fetch(PDO::FETCH_ASSOC);
            return (!$field || is_null($field) ? $res : $res[$field]);
        } else {
            return false;
        }
    }
    
    public function show($qry) {
        if (($type = $this->getQueryType($qry)) !== "show") {
            DebugConsole::sql_error_Exception("Incorrect Show Query",$qry, []);
            DebugConsole::insert_error('database::show','Incorrect Show Query!');
            DebugConsole::insert_sql_info('database::show',$qry, []);
            return [];
        }

        if ($stmnt = $this->run_query($qry, [], $type)) {
            return $stmnt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return [];
        }
    }
    
    public function create($qry) {
        if (($type = $this->getQueryType($qry)) !== "create") {
            DebugConsole::sql_error_Exception("Incorrect Create Query",$qry, []);
            DebugConsole::insert_error('database::show','Incorrect Create Query!');
            DebugConsole::insert_sql_info('database::create',$qry, []);
            return [];
        }

        return $this->run_query($qry, [], $type);
    }
    
    public function optimize($qry) {
        if (($type = $this->getQueryType($qry)) !== "optimize") {
            DebugConsole::sql_error_Exception("Incorrect Optimize Query",$qry, []);
            DebugConsole::insert_error('database::select','Incorrect Optimize Query!');
            DebugConsole::insert_sql_info('database::optimize',$qry, []);
            return [];
        }

        return $this->run_query($qry, [], $type);
    }

    public final function query($qry) {
        $qry = $this->rep_prefix($qry); // replace sql prefix
        $this->lastInsertId = false;
        $this->rowCount = false;
        $this->rowCount = $this->dbHandle->exec($qry);
        $this->queryCounter++;
    }

    public function getQueryCounter() {
        return $this->queryCounter;
    }

    public function quote($str) {
        return $this->dbHandle->quote($str);
    }
    
    public function getConfig($key='db_host',$active='default') {
        $dbConf = $this->dbConf[$active];
        return $dbConf[$key];
    }

    /************************
     * Protected
     ************************/

    /**
     * Erstellt das PDO Objekt mit vorhandener Konfiguration
     * @namespace system\database
     * @category PDO Database
     * @param string $active = "default"
     * @throws PDOException
     * @return array
     */
    protected final function connect($active = "default") {
        if (!isset($this->dbConf[$active])) {
            die("PDO: No supported connection scheme!");
        }

        $dbConf = $this->dbConf[$active];
        try {
            if (!$dsn = $this->dsn($active)) {
                die("PDO: Driver is missing!");
            }

            if($dbConf['persistent']) {
                $db = new PDO($dsn, stripslashes($dbConf['db_user']), stripslashes($dbConf['db_pw']), [PDO::ATTR_PERSISTENT => true]);
            } else {
                $db = new PDO($dsn, stripslashes($dbConf['db_user']), stripslashes($dbConf['db_pw']));
            }

            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->query("set character set utf8");
            $db->query("set names utf8");

            $this->dbHandle = $db;
            $this->active = $active; //mark as active
            if($active == 'test') {
                return ['status'=>true,'msg'=>'','code'=>0];
            }
        } catch (PDOException $ex) {
            if($active == 'test') {
                return ['status'=>false,'msg'=>$ex->getMessage(),'code'=>$ex->getCode()];
            } else {
                die("PDO: Connection Exception: " . $ex->getMessage());
            }
        }

        return ['status'=>false,'msg'=>'','code'=>0];
    }
    
    public final function rep_prefix($qry){
        // replace sql prefix
        if(strpos($qry,"{prefix_")!==false) {
            $qry = preg_replace_callback("#\{prefix_(.*?)\}#",function($tb) { 
                return str_ireplace($tb[0],$this->dbConf[$this->active]['prefix'].$tb[1],$tb[0]); 
            },$qry);
        }

        if(strpos($qry,"{engine}")!==false) {
            switch ($this->dbConf[$this->active]['db_engine']) {
                case 1: $replace = 'ENGINE=MyISAM '; break; //MyISAM Engine
                case 2: $replace = 'ENGINE=InnoDB '; break; //InnoDB Engine
                case 3: $replace = 'ENGINE=Aria '; break; //Aria Engine
                default: $replace = ''; break;
            }
            $qry = str_ireplace('{engine}', $replace, $qry);
        }

        return $qry;
    }
    
    protected final function run_query($qry, array $params, $type) {
        if (in_array($type, ["insert", "select", "update", "delete","show","optimize","create","drop"]) === false) {
           die("PDO: Unsupported Query Type!<p>".$qry);
        }

        $qry = $this->rep_prefix($qry); // replace sql prefix

        //Debug
        if(show_pdo_delete_debug || show_pdo_delete_debug || show_pdo_delete_debug || show_pdo_delete_debug) {
            DebugConsole::insert_sql_info('database::run_query('.$type.')',$qry,$params);
        }

        $this->lastInsertId = false;
        $this->rowCount = false;
        
        if(count($params)) {
            $stmnt = $this->active_driver == 'mysql' ? 
                    $this->dbHandle->prepare($qry, [PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => $this->mysql_buffered_query]) :
                    $this->dbHandle->prepare($qry);
        }

        try {
            $success = (count($params) !== 0) ? 
                $stmnt->execute($params) : 
               ($stmnt = $this->dbHandle->query($qry));
            $this->queryCounter++;

            if (!$success) {
                return false;
            }

            if ($type === "insert") {
                $this->lastInsertId = $this->dbHandle->lastInsertId();
            }

            $this->rowCount = $stmnt->rowCount();
            return ($type === "select" || $type === "show") ? $stmnt : true;
        } catch (PDOException $ex) {
            die("PDO: Exception: " . $ex->getMessage()."<br><br>SQL-Query:<br>".$qry.(count($params) ? "<br><br>Input params:".  var_export($params,true) : ''));
        }
    }

    protected final function check_driver($use_driver) {
        foreach(PDO::getAvailableDrivers() as $driver) {
            if ($use_driver == $driver) {
                return true;
            }
        }

        return false;
    }

    protected final function dsn($active) {
        $dbConf = $this->dbConf[$active];
        if (!$this->check_driver($dbConf['driver'])) {
            return false;
        }

        $this->active_driver = $dbConf['driver'];
        $dsn= sprintf('%s:', $dbConf['driver']);
        switch($dbConf['driver']) {
            case 'mysql':
            case 'pgsql':
                $dsn .= sprintf('host=%s;dbname=%s', $dbConf['db_host'], $dbConf['db']);
                break;
            case 'sqlsrv':
                $dsn .= sprintf('Server=%s;1433;Database=%s', $dbConf['db_host'], $dbConf['db']);
                if ($this->connection_pooling) {
                    $dsn .= ';ConnectionPooling=1';
                }
                
                if($this->connection_encrypting) {
                    $dsn .= ';Encrypt=1';
                }
                break;
        }

        return $dsn;
    }

    protected function getQueryType($qry) {
        list($type, ) = explode(" ", strtolower($qry), 2);
        return $type;
    }
}