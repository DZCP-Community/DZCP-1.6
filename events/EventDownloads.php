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
use Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException as PhpfastcacheInvalidArgumentExceptionAlias;

/**
 * Class EventDownloads
 */
class EventDownloads extends BaseEventAbstract {
    /**
     * @var bool
     */
    private bool $addons;

    /**
     * EventDownloads constructor.
     * @param BaseSystem $baseSystem
     * @throws Exception
     */
    public function __construct(BaseSystem $baseSystem)
    {
        try {
            parent::__construct($baseSystem);
        } catch (Exception $e) {
            exit();
        }

        $this->useCert();

        $this->setEventCacheTime(10);

        $this->getLogger()->pushHandler(new StreamHandler(LOG_PATH.'/'.__CLASS__.'.log',
            DEBUG ? Logger::DEBUG : Logger::WARNING));

        //Is addons.dzcp.de
        $this->addons = false;
        if(array_key_exists('addons',$this->getBaseSystem()->getInput())) {
            $this->addons = boolval($this->getBaseSystem()->getInput()['addons']);
        }

        //Use Cert
        /*
        switch (strtolower($this->getEventCall())) {
            case 'downloads':
            case 'download':
            case 'downloadkey':
            case 'downloadcategorys':
            case 'downloadbinary':
            case 'downloadcategory':
            case 'downloadsubcategory':
                $this->useCert(true);
                break;
        }
        */
    }

    public function __run(): void
    {
        parent::__run();

        if($this->isRedirect())
            return;

        $function = $this->getEventCall();
        if(method_exists($this,$function)) {
            $this->$function();
        }
    }

    /**
     * https://api.dzcp.de/?event=downloads&call=downloadCategorys
     * https://api.dzcp.de/?event=downloads&call=downloadCategorys&addons=1
     * @throws PhpfastcacheInvalidArgumentExceptionAlias
     */
    private function DownloadCategorys() {
        /** @var TYPE_NAME $category */
        $category = [];
        $CachedServer = $this->getBaseSystem()->getCacheInstance()->getItem(md5('dl_categorys'.$this->isAddons() ? '_addons' : ''));
        if (!$this->isCached($CachedServer)) {
            $sql = $this->getBaseSystem()->getDatabase()->fetchAll('SELECT * FROM `dzcp_server_downloads_category` WHERE `addons` = \'-1\' OR `addons` = ? ORDER BY `name`;',
            [intval($this->isAddons())]);
            foreach ($sql as $get) {
                //sub categorys
                /** @var TYPE_NAME $sub_categorys */
                $sub_categorys = [];
                $qry = $this->getBaseSystem()->getDatabase()->fetchAll("SELECT * FROM `dzcp_server_downloads_sub_category` WHERE `kid` = ? ORDER BY `name` ASC;",
                    $get['id']);

                foreach($qry as $get_subkats) {
                    $sub_categorys[] = [
                        'id' => $get_subkats->id,
                        'name' => $get_subkats->name
                    ];
                }

                $category[] = [
                    'id' => $get->id,
                    'name' => $get->name,
                    'sub_categorys' => $sub_categorys
                ];
            }

            if(count($category) >= 1 && !empty($category)) {
                $CachedServer->set($category)->expiresAfter($this->getEventCacheTime());
                $this->getBaseSystem()->getCacheInstance()->save($CachedServer);
            }
        } else {
            $category = $CachedServer->get();
        }

        $this->setContent(['results' => [
            'categorys' => $category
        ]]);
    }

    /**
     * https://api.dzcp.de/?event=downloads&call=downloadCategory&id=1
     * https://api.dzcp.de/?event=downloads&call=downloadCategory&id=1&addons=1
     * @throws PhpfastcacheInvalidArgumentExceptionAlias
     */
    private function DownloadCategory(): void {
        //Filter Input
        $this->getBaseSystem()->getGump()->validation_rules(['id' => 'required|numeric|min_len,1']);
        $this->getBaseSystem()->getGump()->filter_rules(['id' => 'sanitize_numbers']);

        $input = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput());
        if($input !== false) {
            $CachedServer = $this->getBaseSystem()->getCacheInstance()->getItem(md5('dl_category_'.$input['id'].$this->isAddons() ? '_addons' : ''));
            if (!$this->isCached($CachedServer)) {
                $download_category = (array)$this->getBaseSystem()->getDatabase()->fetch(
                    'SELECT `name` FROM `dzcp_server_downloads_category` WHERE `id` = ? AND (`addons` = -1 OR `addons` = ?);',
                    intval($input['id']),intval($this->isAddons()));

                if(count($download_category) >= 1 && !empty($download_category)) {
                    $CachedServer->set($download_category)->expiresAfter($this->getEventCacheTime());
                    $this->getBaseSystem()->getCacheInstance()->save($CachedServer);
                }
            } else {
                $download_category = $CachedServer->get();
            }

            //Error FIX
            if(!array_key_exists('name',$download_category)) {
                $this->getBaseSystem()->getSystemLogger()->critical('DownloadCategory ERROR',
                    $download_category);

                $download_category = (array)$this->getBaseSystem()->getDatabase()->fetch(
                    'SELECT `name` FROM `dzcp_server_downloads_category` WHERE `id` = ? AND (`addons` = -1 OR `addons` = ?);',
                    intval($input['id']),intval($this->isAddons()));
            }

            $this->setContent(['results' => ['name' => $download_category['name']]]);
        }
    }

    /**
     * https://api.dzcp.de/?event=downloads&call=downloadSubCategory&id=1
     * https://api.dzcp.de/?event=downloads&call=downloadSubCategory&id=1&addons=1
     * @throws PhpfastcacheInvalidArgumentExceptionAlias
     */
    private function DownloadSubCategory(): void {
        //Filter Input
        $this->getBaseSystem()->getGump()->validation_rules(['id' => 'required|numeric|min_len,1']);
        $this->getBaseSystem()->getGump()->filter_rules(['id' => 'sanitize_numbers']);

        $input = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput());
        if($input !== false) {
            $CachedServer = $this->getBaseSystem()->getCacheInstance()->getItem(md5('dl_sub_category_'.
                $input['id'].($this->isAddons() ? '_addons' : '')));

            if (!$this->isCached($CachedServer)) {
                $download_sub_category = $this->getBaseSystem()->getDatabase()->fetch(
                    'SELECT `name` FROM `dzcp_server_downloads_sub_category` WHERE (`addons` = \'-1\' OR `addons` = ?) AND `id` = ?;',
                    intval($this->isAddons()) ,$input['id']);

                if(count($download_sub_category) >= 1 && !empty($download_sub_category)) {
                    $CachedServer->set($download_sub_category)->expiresAfter($this->getEventCacheTime());
                    $this->getBaseSystem()->getCacheInstance()->save($CachedServer);
                }
            } else {
                $download_sub_category = $CachedServer->get();
            }

            $this->setContent(['results' => ['name' => $download_sub_category->name]]);
        }
    }

    /**
     * https://api.dzcp.de/?event=downloads&call=downloadKey&id=1
     */
    private function DownloadKey(): void {
        if(array_key_exists('id',$this->getBaseSystem()->getInput())) {
            if (!empty($this->getBaseSystem()->getInput()['id'])) {
                $key = sha1($this->getBaseSystem()->mkPWD(16));

                $this->getBaseSystem()->getDatabase()->query('INSERT INTO `dzcp_server_downloads_keys` SET `key` = ?, `fileID` = ?, `time` = ?;',
                    $key, (int)$this->getBaseSystem()->getInput()['id'], (time() + 15120));

                $this->setContent(['results' => [
                    'key' => $key,
                    'server' => DOWNLOAD_SERVER_URL,
                ]]);
            }
        }
    }

    /**
     * https://api.dzcp.de/?event=downloads&call=downloadBinary&key=94927a467e25ea7e79790b79f77c2792b83918bb
     * https://api.dzcp.de/?event=downloads&call=downloadBinary&key=94927a467e25ea7e79790b79f77c2792b83918bb&stats_update=1
     */
    private function DownloadBinary(): void {
        if(array_key_exists('key',$this->getBaseSystem()->getInput())) {
            if(!empty($this->getBaseSystem()->getInput()['key'])) {
                $key = $this->getBaseSystem()->getDatabase()->fetch('SELECT `fileID` FROM `dzcp_server_downloads_keys` WHERE `key` = ?;',
                    strtolower($this->getBaseSystem()->getInput()['key']));
                if(!empty($key) || !is_null($key)) {
                    $download_info = $this->getBaseSystem()->getDatabase()->fetch("SELECT `fileID`,`pathID` FROM `dzcp_server_downloads` WHERE `id` = ?;",
                        $key->offsetGet('fileID'));

                    $download_file = $this->getBaseSystem()->getDatabase()->fetch("SELECT `file`,`speed` FROM `dzcp_server_downloads_files` WHERE `id` = ?;",
                        $download_info->offsetGet('fileID'));

                    $download_path = $this->getBaseSystem()->getDatabase()->fetch("SELECT `path` FROM `dzcp_server_downloads_paths` WHERE `id` = ?;",
                        $download_info->offsetGet('pathID'));

                    if(array_key_exists('stats_update',$this->getBaseSystem()->getInput()) &&
                        $this->getBaseSystem()->getInput()['stats_update']) {

                        $download_size = $this->getBaseSystem()->getDatabase()->fetch("SELECT `size`,`downloads` FROM `dzcp_server_downloads_stats` WHERE `fileID` = ?;",
                            $download_info->offsetGet('fileID'));

                        $download_size = ((intval($download_size->offsetGet('downloads'))+1) * intval($download_size->offsetGet('size')));
                        $this->getBaseSystem()->getDatabase()->query('UPDATE `dzcp_server_downloads_stats` SET `downloads` = (`downloads`+1), `traffic` = ? WHERE `fileID` = ?;',
                            $download_size ,$key->offsetGet('fileID'));
                    }

                    $this->setContent(['results' => [
                        'download' => true,
                        'file' => array_merge((array)$download_file,(array)$download_path),
                    ]]);
                }
            }
        }
    }

    /**
     * https://api.dzcp.de/?event=downloads&call=download&id=1&intern=0&addons=1
     * @throws phpFastCacheInvalidArgumentException
     */
    private function Download(): void {
        $intern = '';
        if(array_key_exists('intern',$this->getBaseSystem()->getInput())) {
            if(!empty($this->getBaseSystem()->getInput()['intern'])) {
                if($this->getBaseSystem()->getInput()['intern'] < 1) {
                    $intern = ' AND `intern` = 0';
                } else {
                    $intern = ' AND `intern` < '.((int)$this->getBaseSystem()->getInput()['intern']+1);
                }
            }
        }

        if(!array_key_exists('language',$this->getBaseSystem()->getInput())) {
            $input = $this->getBaseSystem()->getInput();
            $input['language'] = 'de';
            $this->getBaseSystem()->setInput($input);
        }

        $CachedServer = $this->getBaseSystem()->getCacheInstance()->getItem('dl_download_'.
            strval($this->getBaseSystem()->getInput()['id']).'_'.strval($this->getBaseSystem()->getInput()['language']).
        $this->isAddons() ? '_addons' : $intern); //Bugy
        if (!$this->isCached($CachedServer)) {
            $download_info = $this->getBaseSystem()->getDatabase()->fetch("SELECT `fileID`,`catID` FROM `dzcp_server_downloads` WHERE `id` = ? ".
                $intern." AND `enabled` = 1 AND (`addons` = ? OR `addons` = -1);",
                (int)$this->getBaseSystem()->getInput()['id'],intval($this->isAddons()));

            if(!$download_info) {
                $this->setContent(['results' => ['download' => 'intern']]);
                return;
            }

            $download_file = (array)$this->getBaseSystem()->getDatabase()->fetch("SELECT * FROM `dzcp_server_downloads_files` WHERE `id` = ?;",
                $download_info->fileID);

            $download_category = $this->getBaseSystem()->getDatabase()->fetch("SELECT `name` FROM `dzcp_server_downloads_category` WHERE `id` = ?;",
                $download_info->catID);

            $download_file['category'] = $download_category->offsetGet('name');

            $download_stats = (array)$this->getBaseSystem()->getDatabase()->fetch("SELECT * FROM `dzcp_server_downloads_stats` WHERE `fileID` = ?;",
                $download_info->fileID);

            //Eng support
            $download_file['forum_url'] = str_replace('%',
                (strtolower($this->getBaseSystem()->getInput()['language']) == 'de' ? $download_file['forum_url_id'] : 1),
                $download_file['forum_url']);

            unset($download_file['forum_url_id']);

            $download = ['file'=>$download_file,'stats'=>$download_stats];

            if(count($download_file) >= 4) {
                $CachedServer->set($download)->expiresAfter($this->getEventCacheTime());
                $this->getBaseSystem()->getCacheInstance()->save($CachedServer);
            }
        } else {
            $download = $CachedServer->get();
        }

        $this->setContent(['results' => [
            'download' => $download
        ]]);
    }

    /**
     * https://api.dzcp.de/?event=downloads&call=downlads&orderby=id&limit=5&desc=asc
     * @throws phpFastCacheInvalidArgumentException
     */
    public function Downloads() {
        $orderby = ' ORDER BY files.`name`';
        if(array_key_exists('orderby',$this->getBaseSystem()->getInput())) {
            if(!empty($this->getBaseSystem()->getInput()['orderby'])) {
                $orderby = " ORDER BY files.`".strtolower($this->getBaseSystem()->getInput()['orderby'])."`";
            }
        }

        $limit = '';
        if(array_key_exists('limit',$this->getBaseSystem()->getInput())) {
            if(!empty($this->getBaseSystem()->getInput()['limit']) && (int)$this->getBaseSystem()->getInput()['limit'] >= 1)
                $limit = " LIMIT ".strval($this->getBaseSystem()->getInput()['limit']);
        }

        $desc = ' ASC';
        if(array_key_exists('desc',$this->getBaseSystem()->getInput())) {
            if(boolval($this->getBaseSystem()->getInput()['desc']) == true) {
                $desc = " DESC";
            }
        }

        $where = '';
        if(array_key_exists('where',$this->getBaseSystem()->getInput())) {
            if(!empty($this->getBaseSystem()->getInput()['where']) && is_array($this->getBaseSystem()->getInput()['where'])) {
                foreach ($this->getBaseSystem()->getInput()['where'] as $key => $var) {
                    if (is_bool($var))
                        $var = $var ? 1 : 0;
                    else if (!is_int($var))
                        $var = "'" . $var . "'";

                    $where .= ' AND downloads.`' . $key . '` = ' . $var;
                }
            }
        }

        $CachedServer = $this->getBaseSystem()->getCacheInstance()->getItem(md5('dl_downloads_'.$where.$orderby.$desc.$limit));
        if (!$this->isCached($CachedServer)) {
            $downloads = [];
            $sql = $this->getBaseSystem()->getDatabase()->fetchAll('SELECT files.`id`,files.`name`,files.`description`,files.`file`,files.`time`,downloads.`catID` '.
                'FROM dzcp_server_downloads AS downloads LEFT JOIN dzcp_server_downloads_files AS files '.
                'ON (downloads.`fileID` = files.`id`) WHERE downloads.`enabled` = 1'.$where.$orderby.$desc.$limit.';');
            foreach ($sql as $get) {
                //Stats
                $stats = (array)$this->getBaseSystem()->getDatabase()->fetch('SELECT * FROM `dzcp_server_downloads_stats` WHERE `fileID` = ?;',$get['id']);
                if(!$stats || !count($stats)) {
                    //Create Stats
                    $this->getBaseSystem()->getDatabase()->query('INSERT INTO `dzcp_server_downloads_stats` SET `fileID` = ?, `downloads` = 0, `traffic` = 0;',$get['id']);
                    $stats = (array)$this->getBaseSystem()->getDatabase()->fetch('SELECT * FROM `dzcp_server_downloads_stats` WHERE `id` = ?;',
                        $this->getBaseSystem()->getDatabase()->getInsertId());
                }

                $downloads[] = array_merge((array)$get,['stats'=>$stats]);
            }

            if(count($downloads) >= 1) {
                $CachedServer->set($downloads)->expiresAfter($this->getEventCacheTime());
                $this->getBaseSystem()->getCacheInstance()->save($CachedServer);
            }
        } else {
            $downloads = $CachedServer->get();
        }

        $this->setContent(['results' => ['downloads' => $downloads]]);
    }

    /**
     * @return bool
     */
    public function isAddons(): bool
    {
        return $this->addons;
    }
}