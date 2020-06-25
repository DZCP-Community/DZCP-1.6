<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 *
 * Hinweis: Diese Datei bitte nicht bearbeiten!
 */

class api {
    /**
     * @var array
     */
    private $api_input;

    /**
     * @var array
     */
    private $api_output;

    /**
     * @var string
     */
    private $api_output_stream;

    /**
     * @var array
     */
    private $api_curl_errors;

    /**
     * @var string
     */
    private $api_server;

    /**
     * @var string
     */
    private $api_version;

    /**
     * @var string
     */
    private $api_language;

    /**
     * @var bool
     */
    private $api_compress;

    /**
     * api constructor.
     * @param string $address
     */
    function __construct(string $address = 'api.dzcp.de')
    {
        global $cache;

        $this->api_server = $address;
        $this->api_input = [];
        $this->api_output = [];
        $this->api_output_stream = '';
        $this->api_curl_errors = [];
        $this->api_version = '0.0.0';
        $this->api_language = 'en';
        $this->api_compress = false;

        /** @var bool */
        $this->api_input['event'] = 'api';
        $this->api_input['call'] = 'version';

        $this->call(0.5);
        $this->varying();

        $CachedString = $cache->getItem('api_version');
        if (is_null($CachedString->get())) {
            if (!$this->api_output['error'] && $this->api_output['code'] == 200) {
                $this->api_version = $this->api_output['results']['version'];
                $CachedString->set($this->api_version)->expiresAfter(300);
                $cache->save($CachedString);
            }
        } else {
            $this->api_version = $CachedString->get();
        }
    }

    /**
     * @return string
     */
    public function getApiVersion()
    {
        return $this->api_version;
    }

    /**
     * @param bool $use_cache
     * @param int $ttl
     * @return array|mixed
     */
    public function getNews(bool $use_cache = true, int $ttl = 120) {
        global $cache;
        if (show_api_debug)
            DebugConsole::insert_info('api.php', 'Call getNews()');

        $this->api_output = [];
        $this->api_output['news'] = '';
        $this->api_output['error'] = true;
        $this->api_output['error_msg'] = 'no content from server';

        //CALL
        $this->api_input = [];
        $this->api_input['event'] = 'news';
        $this->api_input['old_news'] = true; //Use Old News Style

        if ($use_cache) {
            try {
                $CachedString = $cache->getItem('api_dzcp_news');
                if (is_null($CachedString->get())) {
                    $this->call();
                    $this->varying();
                    if (!$this->api_output['error']) {
                        $CachedString->set(serialize($this->api_output))->expiresAfter($ttl);
                        $cache->save($CachedString);
                    }
                } else {
                    $this->api_output = unserialize($CachedString->get());
                }
            } catch (\phpFastCache\Exceptions\phpFastCacheInvalidArgumentException $e) {}
        } else { // No Cache
            $this->call();
            $this->varying();
        }

        return $this->api_output;
    }

    /**
     * @param array $addons
     * @param bool $use_cache
     * @param int $ttl
     * @return array|mixed
     */
    public function getAddonVersions(array $addons, bool $use_cache = true, int $ttl = 30) {
        global $cache;
        if (show_api_debug)
            DebugConsole::insert_info('api.php', 'Call getAddonVersions()');

        $this->api_compress = true;

        $this->api_output = [];
        $this->api_output['data'] = json_encode($addons);
        $this->api_output['error'] = true;
        $this->api_output['error_msg'] = 'no content from server';

        //CALL
        $this->api_input = [];
        $this->api_input['event'] = 'addons';
        $this->api_input['call'] = 'checkAddons';
        $this->api_input['data'] = bin2hex(gzcompress($this->api_output['data']));

        if ($use_cache) {
            try {
                $CachedString = $cache->getItem('api_dzcp_addons');
                if (is_null($CachedString->get())) {
                    $this->call();
                    $this->varying();

                    if (!$this->api_output['error']) {
                        $CachedString->set(serialize($this->api_output))->expiresAfter($ttl);
                        $cache->save($CachedString);
                    }
                } else {
                    $this->api_output = unserialize($CachedString->get());
                }
            } catch (\phpFastCache\Exceptions\phpFastCacheInvalidArgumentException $e) {}
        } else { // No Cache
            $this->call();
            $this->varying();
        }

        return $this->api_output;
    }

    /**
     * @param bool $use_cache
     * @param int $ttl
     * @param bool $reload
     * @return array|mixed
     */
    public function getDzcpVersion(bool $use_cache = true, int $ttl = 60,bool $reload  = false) {
        global $cache;
        if (show_api_debug)
            DebugConsole::insert_info('api.php', 'Call getDzcpVersion()');

        $this->api_output = [];
        $this->api_output['version'] = _version;
        $this->api_output['release'] = _release;
        $this->api_output['build'] = _build;
        $this->api_output['error'] = true;
        $this->api_output['error_msg'] = 'no content from server';

        //CALL
        $this->api_input = [];
        $this->api_input['event'] = 'version';
        $this->api_input['version'] = _version;
        $this->api_input['edition'] = _edition;
        $this->api_input['build'] = _build;
        $this->api_input['release'] = _release;

        if ($use_cache) {
            try {
                $CachedString = $cache->getItem('api_dzcp_version');
                if (is_null($CachedString->get()) || $reload) {
                    $this->call();
                    $this->varying();
                    if (!$this->api_output['error']) {
                        $CachedString->set(serialize($this->api_output))->expiresAfter($ttl);
                        $cache->save($CachedString);
                    }
                } else {
                    $this->api_output = unserialize($CachedString->get());
                }
            } catch (\phpFastCache\Exceptions\phpFastCacheInvalidArgumentException $e) {}
        } else { // No Cache
            $this->call();
            $this->varying();
        }

        return $this->api_output;
    }

    /**
     * @param string $address
     * @param bool $use_cache
     * @param int $ttl
     * @return array|mixed
     */
    public function getGeoLocation(string $address, bool $use_cache = true, int $ttl = 30) {
        global $cache;
        if (show_api_debug)
            DebugConsole::insert_info('api.php', 'Call getGeoLocation()');

        $this->api_output = [];
        $this->api_output['results'] = [];
        $this->api_output['status'] = 'ZERO_RESULTS';
        $this->api_output['error'] = true;
        $this->api_output['error_msg'] = 'no content from server';

        //CALL
        $this->api_input = [];
        $this->api_input['event'] = 'proxy';
        $this->api_input['call'] = 'getGeocode';
        $this->api_input['address'] = $address;

        if ($use_cache) {
            try {
                $CachedString = $cache->getItem('geolocation_'.md5($address));
                if (is_null($CachedString->get())) {
                    $this->call();
                    $this->varying();
                    if (!$this->api_output['error']) {
                        $CachedString->set(serialize($this->api_output))->expiresAfter($ttl);
                        $cache->save($CachedString);
                    }
                } else {
                    $this->api_output = unserialize($CachedString->get());
                }
            } catch (\phpFastCache\Exceptions\phpFastCacheInvalidArgumentException $e) {}
        } else { // No Cache
            $this->call();
            $this->varying();
        }

        return $this->api_output;
    }

    /**
     * @param string $version1
     * @param string $operator
     * @param string $version2
     * @return mixed
     */
    public static function versionCompare(string $version1,string $operator,string $version2) {
        $_fv = (int)(trim(str_replace('.', '', $version1)));
        $_sv = (int)(trim(str_replace('.', '', $version2)));

        if (strlen($_fv) > strlen($_sv)) {
            $_sv = str_pad ($_sv,strlen($_fv),0);
        }

        if (strlen($_fv) < strlen($_sv)) {
            $_fv = str_pad($_fv,strlen($_sv),0);
        }

        return version_compare((string)$_fv,(string)$_sv,$operator);
    }

    private function varying() {
        if (show_api_debug)
            DebugConsole::insert_info('api.php', 'Call varying');

        //Uncompress
        if(array_key_exists('compress',$this->api_output['results'])) {
            unset($this->api_output['results']['compress']);
            foreach ($this->api_output['results'] as $key => $result) {
                $this->api_output['results'][$key] = json_decode(gzuncompress(hex2bin($result)),true);
            }
        }

        $this->api_output['results'] = (array)$this->api_output['results'] ;
        $this->api_output['code'] = intval($this->api_output['code']);
        $this->api_output['error'] = boolval($this->api_output['error']);
        $this->api_output['status'] = strval($this->api_output['status']);
    }

    private function call(float $timeout = 10) {
        if (!fsockopen_support() && !fsockopen_support_bypass) {
            return;
        }

        $this->api_input += ['event' => 'null'];
        $this->api_input += ['format' => 'json'];
        $this->api_input += ['language' => $this->api_language];
        $this->api_input += ['compress' => $this->api_compress];

        if (show_api_debug)
            DebugConsole::insert_info('api.php', 'apiInput: <pre>' . var_export($this->api_input,true) . '</pre>');

        //Call to Server
        $this->api_output_stream = get_external_contents('https://'.$this->api_server,$this->api_input,false,$timeout);

        if(!$this->api_output_stream || empty($this->api_output_stream)) {
            $this->api_output['results'] = [];
            $this->api_output['code'] = 500;
            $this->api_output['error'] = true;
            $this->api_output['status'] = 'server response is empty';
            return false;
        }

        $this->api_output = json_decode($this->api_output_stream,true);

        DebugConsole::insert_info('api.php', 'apiOutput: <pre>' . var_export($this->api_output,true) . '</pre>');

        if(json_last_error()) {
            $this->api_output['results'] = [];
            $this->api_output['code'] = 500;
            $this->api_output['error'] = true;
            $this->api_output['status'] = json_last_error();
            return false;
        }

        if($this->api_output['crc32'] != crc32(serialize($this->api_output['results']))) {
            $this->api_output['error'] = true;
            $this->api_output['status'] = 'crc32 checksum is not identical';
            return false;
        } unset($this->api_output['crc32']);

        return true;
    }
}