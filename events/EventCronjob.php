<?php
/**
 * DZCP - deV!L`z ClanPortal - Server ( api.dzcp.de )
 * deV!L`z Clanportal ist ein Produkt von CodeKing,
 * geändert durch my-STARMEDIA und Codedesigns.
 *
 * DZCP - deV!L`z ClanPortal - Server
 * Homepage: https://www.dzcp.de
 * E-Mail: lbrucksch@hammermaps.de
 * Author Lucas Brucksch
 * Copyright 2021 © Codedesigns
 */

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Nette\Database as NETTE;

/**
 * Class EventCronjob
 */
class EventCronjob extends BaseEventAbstract {

    /**
     * @var string
     */
    private string $url;

    /**
     * @var bool
     */
    private bool $compress;

    /**
     * @var array
     */
    private array $data;

    /**
     * EventCronjob constructor.
     * @param BaseSystem $baseSystem
     */
    public function __construct(BaseSystem $baseSystem)
    {
        if (php_sapi_name() != "cli") {
           echo 'Cronjob called only from Server!';
           exit();
        }

        set_time_limit(300);

        try {
            parent::__construct($baseSystem);
        } catch (Exception $e) {
            exit();
        }

        $this->useCert();

        $this->url = 'https://download.dzcp.de/';

        $this->compress = true;

        $this->data = [];
    }

    public function __run(): void
    {
        parent::__run();

        if($this->isRedirect())
            return;

        $sql = $this->getBaseSystem()->getDatabase()->fetchAll('SELECT * FROM `dzcp_server_cronjob` WHERE `enabled` = 1;');
        foreach ($sql as $get) {
            if($get['next_call'] == -1 || $get['next_call'] <= (time()-$get['last_call'])) {
                $function = $get['call'];
                $this->data = empty($get['data']) || is_null($get['data']) ? [] : json_decode($get['data'],true);
                if(method_exists($this,$function)) {
                    $this->$function();
                    $this->getBaseSystem()->getDatabase()->query('UPDATE `dzcp_server_cronjob` SET `last_call` = ?, `data` = ? WHERE `id` = ?;',
                        time(),json_encode($this->data),intval($get['id']));
                }
            }
        }

        //Output Logs
        $this->getBaseSystem()->getCronjobLogger()->close();
        echo file_get_contents(LOG_PATH.$this->getBaseSystem()->getCronjobLogFile());
        @unlink(LOG_PATH.$this->getBaseSystem()->getCronjobLogFile());
    }

    /**
     * Called on all 48h
     */
    private function clearServerDir() {
        $i=0;
        $files = $this->getBaseSystem()->scanDirectory(SCRIPT_PATH);
        foreach ($files as $file) {
            if (strpos($file, 'Resource id #') !== false) {
                if($this->getBaseSystem()->getFilesystem()->exists(SCRIPT_PATH.'/'.$file)) {
                    $this->getBaseSystem()->getFilesystem()->remove(SCRIPT_PATH.'/'.$file);
                    $i++;
                }
            }
        }

        $files = $this->getBaseSystem()->scanDirectory(HOME_DIR);
        foreach ($files as $file) {
            if (strpos($file, 'Resource id #') !== false) {
                if($this->getBaseSystem()->getFilesystem()->exists(HOME_DIR.'/'.$file)) {
                    $this->getBaseSystem()->getFilesystem()->remove(HOME_DIR.'/'.$file);
                    $i++;
                }
            }
        }

        $this->getBaseSystem()->getCronjobLogger()->info('Cleanup: "'.$i.'" Files deleted');
    }

    /**
     * Called on all 48h
     */
    private function dumpServerDatabase() {
        $tables = $this->getBaseSystem()->getDatabase()->query('show tables')->fetchAll(); $i = 0;
        foreach ($tables as $table) {
            $backup_file = HOME_DIR.'/sql_dump/api/'.$table->offsetGet('Tables_in_dzcpad_db1').'.sql';
            if($this->getBaseSystem()->getFilesystem()->exists($backup_file.'.zip')) {
                @unlink($backup_file);
            }
            $this->getBaseSystem()->makeMySQLDump($backup_file,[$table->offsetGet('Tables_in_dzcpad_db1')]);
            $this->getBaseSystem()->zipFiles($backup_file.'.zip',
                [$table->offsetGet('Tables_in_dzcpad_db1').'.sql' => $backup_file]);

            if($this->getBaseSystem()->getFilesystem()->exists($backup_file)) {
                @unlink($backup_file);
            }

            $i++;
        }

        $this->getBaseSystem()->getCronjobLogger()->info('Backup SQL-Database: "'.$i.'" Files created & zipped');
    }

    /**
     * Called on all 2h
     */
    private function resetDemoDatabase() {
        $database = new NETTE\Connection(SQL_DEMO_DSN, SQL_DEMO_USERNAME, SQL_DEMO_PASSWORD);
        $database->beginTransaction();
        $database->query(file_get_contents(HOME_DIR.'/sql_dump/demo/dzcpad_db3.sql'));
        $database->commit();
        $database->disconnect();

        $this->getBaseSystem()->getCronjobLogger()->info('Restore SQL-Database: DZCP-Demo');
    }

    /**
     * Called on all 8h
     */
    private function updateDownloadInfos() {
        $cert = $this->getBaseSystem()->getDatabase()->fetch('SELECT `indent` FROM `dzcp_server_certs` WHERE `id` = 3;')->offsetGet('indent');
        $sql = $this->getBaseSystem()->getDatabase()->fetchAll('SELECT * FROM `dzcp_server_downloads_paths` ORDER BY `path`;');
        foreach ($sql as $get) {
            $data_files = $this->getBaseSystem()->getExternalContents($this->url.$get->path.'fileinfo.php',['cert'=>$cert,'compress'=>$this->compress]);
            if($this->compress) {
                $data_files = gzuncompress(hex2bin($data_files));
            }

            $data_files = json_decode($data_files,true);
            if(!array_key_exists('error',$data_files)) {
                foreach ($data_files as $file => $data) {
                    $files = (array)$this->getBaseSystem()->getDatabase()->fetch('SELECT `id` FROM `dzcp_server_downloads_files` WHERE `file` = ?;',$file);
                    if($files && count($files)) {
                        $this->getBaseSystem()->getDatabase()->query('UPDATE `dzcp_server_downloads_files` SET `crc` = ?, `updated` = ? WHERE `id` = ?;',
                            $data['hash'],intval($data['filemtime']),intval($files['id']));

                        //Stats
                        $stats = (array)$this->getBaseSystem()->getDatabase()->fetch('SELECT `id` FROM `dzcp_server_downloads_stats` WHERE `fileID` = ?;',$files['id']);
                        if($stats && count($stats)) {
                            $this->getBaseSystem()->getDatabase()->query('UPDATE `dzcp_server_downloads_stats` SET `size` = ? WHERE `fileID` = ?;',
                                intval($data['filesize']),intval($stats['id']));
                        } else {
                            $this->getBaseSystem()->getDatabase()->query('INSERT INTO `dzcp_server_downloads_stats` SET `fileID` = ?, `downloads` = 0, `traffic` = 0, `size` = ?;',
                                intval($stats['id']),intval($data['filesize']));
                        }
                    }
                }

                $this->getBaseSystem()->getCronjobLogger()->info('Dir: ['.$get->path.'] "'.count($data_files).'" Files Updated');
            } else {
                $this->getBaseSystem()->getCronjobLogger()->error('Error:',$data_files['msg']);
            }
        }
    }

    /**
     * Delete old DL-Keys
     */
    private function cleanupDownloadKeys() {
        //Cleanup download keys
        $sql = $this->getBaseSystem()->getDatabase()->fetchAll('SELECT `id` FROM `dzcp_server_downloads_keys` WHERE `time` <= ? AND `static` = 0;',time()); $i=0;
        foreach ($sql as $get) {
            $i++;
            $this->getBaseSystem()->getDatabase()->query('DELETE FROM `dzcp_server_downloads_keys` WHERE `id` = ?;',$get->id);
        }

        $this->getBaseSystem()->getCronjobLogger()->info('"'.$i.'" Download-Keys deleted',(array)$sql);
    }

    /**
     * Roolup LOGS for Server
     */
    private function rollServerLogs() {
        $files = $this->getBaseSystem()->scanDirectory(LOG_PATH);
        foreach ($files as $file) {
            $pfad = pathinfo(LOG_PATH.'/'.$file);
            if (in_array(strtolower($pfad["extension"]), array('log'))) {
                if($this->getBaseSystem()->getFilesystem()->exists(LOG_PATH.'/'.$file.'.zip'))
                    @unlink(LOG_PATH.'/'.$file.'.zip');

                $this->getBaseSystem()->zipFiles(LOG_PATH.'/'.$file.'.zip',[$file => LOG_PATH.'/'.$file]);
                @unlink(LOG_PATH.'/'.$file);
            }
        }
    }

    /**
     * Roolup LOGS for Accounting
     */
    private function rollAccountingLogs() {
        if($this->data['date'] != date("d_m_Y")) {
            $this->data['date'] = date("d_m_Y");
        }
    }
}