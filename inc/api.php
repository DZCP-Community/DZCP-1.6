<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 *
 * Hinweis: Diese Datei bitte nicht bearbeiten!
 */

class api {
    private $api_server = null;
    private $call_varying = null;
    private $api_callback = null;
    private $api_input = array();
    private $api_output = array();
    private $api_version = '0.0.1';

    /**
     * api constructor.
     * @param string $address
     */
    function __construct(string $address = 'api.dzcp.de') {
        $this->api_server = $address;

        //Autoupdate only in administration
        if (api_autoupdate && defined('_Admin'))
            $this->check_api_version();
    }

    /**
     * @param array $addons
     * @param bool $use_cache
     * @param int $ttl
     * @return array|mixed
     * @throws \phpFastCache\Exceptions\phpFastCacheInvalidArgumentException
     */
    public function get_addon_versions(array $addons, bool $use_cache = true, int $ttl = 30) {
        global $cache;
        if (show_api_debug)
            DebugConsole::insert_info('api.php', 'Call get_addon_versions()');

        $this->api_output = [];
        $this->api_output['data'] = json_encode($addons);
        $this->api_output['error'] = true;
        $this->api_output['error_msg'] = 'no content from server';

        //CALL
        $this->api_input = [];
        $this->api_input['event'] = 'addons';
        $this->api_input['data'] = $this->api_output['data'];

        if ($use_cache) {
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
        } else { // No Cache
            $this->call();
            $this->varying();
        }

        return $this->api_output;
    }

    /**
     * @param bool $use_cache
     * @param int $ttl
     * @return array|mixed
     * @throws \phpFastCache\Exceptions\phpFastCacheInvalidArgumentException
     */
    public function get_dzcp_version(bool $use_cache = true, int $ttl = 30) {
        global $cache;
        if (show_api_debug)
            DebugConsole::insert_info('api.php', 'Call get_dzcp_version()');

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
            $CachedString = $cache->getItem('api_dzcp_version');
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
        } else { // No Cache
            $this->call();
            $this->varying();
        }

        return $this->api_output;
    }

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

    private function check_api_version() {
        global $cache;

        $this->api_output = [];
        $this->api_output['version'] = $this->api_version;
        $this->api_output['url'] = '';
        $this->api_output['error'] = true;
        $this->api_output['error_msg'] = 'no content from server';

        //CALL
        $this->api_input = [];
        $this->api_input['event'] = 'api_version';
        $this->api_input['version'] = $this->api_version;

        $CachedString = $cache->getItem('api_version');
        if (is_null($CachedString->get())) {
            $this->call();
            $this->varying();
            if (!$this->api_output['error']) {
                $CachedString->set(serialize($this->api_output))->expiresAfter(api_autoupdate_interval);
                $cache->save($CachedString);
            }
        } else {
            $this->api_output = unserialize($CachedString->get());
        }

        if (!$this->api_output['error'] && array_key_exists('version', $this->api_output)) {
            if ((int)str_replace('.', '', $this->api_output['version']) >
                (int)str_replace('.', '', $this->api_version)) {
                ignore_user_abort(true);
                set_time_limit(600);
                $api_file = get_external_contents(re('https://raw.githubusercontent.com/DZCP-Community/DZCP-1.6/final/inc/api.php', true), false, true);
                if (!empty($api_file) && $api_file != false && strpos($api_file, 'class api') !== false) {
                    if (file_exists(basePath . '/inc/api.php.old')) {
                        @unlink(basePath . '/inc/api.php.old'); //Remove old Backups
                    }

                    if (rename(basePath . '/inc/api.php', basePath . '/inc/api.php.old')) {
                        if (!file_put_contents(basePath . '/inc/api.php', $api_file)) {
                            rename(basePath . '/inc/api.php.old', basePath . '/inc/api.php');
                        }
                    }
                }
                unset($api_file);

                ignore_user_abort(false);
                set_time_limit(30);
            }
        }
    }

    private function varying() {
        if (show_api_debug)
            DebugConsole::insert_info('api.php', 'Call varying');

        if (!empty($this->api_callback) && $this->api_callback != false) {
            if ($this->call_varying === hash('crc32', $this->api_callback)) {
                $this->api_callback = json_decode($this->api_callback, true);

                if (show_api_debug)
                    DebugConsole::insert_info('api.php', 'api_callback = <pre>' . print_r($this->api_callback) . '</pre>');

                if (is_array($this->api_callback) && array_key_exists('error', $this->api_callback)) {
                    $this->api_output = $this->api_callback;
                    $this->api_output['error_msg'] = '';
                    $this->api_callback = null;
                } else {
                    $this->api_output['error'] = true;
                    $this->api_output['error_msg'] = 'api_callback data is not a array';
                }
            } else {
                $this->api_output['error'] = true;
                $this->api_output['error_msg'] = 'crc32 hash is not identical';
            }
        }
    }

    private function call() {
        if (!allow_url_fopen_support()) {
            return;
        }

        if (!array_key_exists('type', $this->api_input))
            $this->api_input['type'] = 'json';

        $this->api_input['language'] = language_short_tag();

        if (show_api_debug)
            DebugConsole::insert_info('api.php', 'api_input = <pre>' . print_r($this->api_input) . '</pre>');

        $this->api_callback = get_external_contents(re('https://' . $this->api_server, true), $this->api_input);

        if (show_api_debug)
            DebugConsole::insert_info('api.php', 'api_callback = <pre>' . print_r($this->api_callback) . '</pre>');

        if (!empty($this->api_callback) && $this->api_callback != false && strpos($this->api_callback, 'not found') === false) {
            $this->call_varying = explode('[hash]', $this->api_callback);
            if (count($this->call_varying) == 2) {
                $this->api_callback = trim($this->call_varying[0]);
                $this->call_varying = trim($this->call_varying[1]);
            }
        }
    }
}