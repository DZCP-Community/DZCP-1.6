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

    function __construct($address='api.dzcp.de') {
        $this->api_server = $address;

        //Autoupdate only in administration
        if(api_autoupdate && defined('_Admin'))
            $this->check_api_version();
    }

    public function get_dzcp_version($use_cache=true,$ttl=30) {
        global $cache;
        if(show_api_debug)
            DebugConsole::insert_info('api.php','Call get_dzcp_version()');

        $this->api_output['version'] = _version;
        $this->api_output['release'] = _release;
        $this->api_output['build'] = _build;
        $this->api_output['error'] = true;
        $this->api_output['error_msg'] = 'no content from server';

        //CALL
        $this->api_input['event'] = 'version';
        $this->api_input['version'] = _version;
        $this->api_input['edition'] = _edition;
        $this->api_input['build'] = _build;
        $this->api_input['release'] = _release;

        if($use_cache) {
            $CachedString = $cache->getItem('api_dzcp_version');
            if(is_null($CachedString->get())) {
                $this->call();
                $this->varying();
                if(!$this->api_output['error']) {
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

    private function check_api_version() {
        global $cache;

        $this->api_output['version'] = $this->api_version;
        $this->api_output['url'] = '';
        $this->api_output['error'] = true;
        $this->api_output['error_msg'] = 'no content from server';

        //CALL
        $this->api_input['event'] = 'api_version';
        $this->api_input['version'] = $this->api_version;

        $CachedString = $cache->getItem('api_version');
        if(is_null($CachedString->get())) {
            $this->call();
            $this->varying();
            if(!$this->api_output['error']) {
                $CachedString->set(serialize($this->api_output))->expiresAfter(api_autoupdate_interval);
                $cache->save($CachedString);
            }
        } else {
            $this->api_output = unserialize($CachedString->get());
        }

        if(!$this->api_output['error'] && array_key_exists('version',$this->api_output)) {
            if((int)str_replace('.','',$this->api_output['version']) >
                (int)str_replace('.','',$this->api_version)) {
                ignore_user_abort(true);
                set_time_limit(600);
                $api_file = get_external_contents('https://raw.githubusercontent.com/DZCP-Community/DZCP-1.6/final/inc/api.php',false,true);
                if(!empty($api_file) && $api_file != false && strpos($api_file,'class api') !== false) {
                    if(file_exists(basePath.'/inc/api.php.old')) {
                        @unlink(basePath.'/inc/api.php.old'); //Remove old Backups
                    }

                    if(rename(basePath.'/inc/api.php',basePath.'/inc/api.php.old')) {
                        if(!file_put_contents(basePath.'/inc/api.php',$api_file)) {
                            rename(basePath.'/inc/api.php.old',basePath.'/inc/api.php');
                        }
                    }
                } unset($api_file);

                ignore_user_abort(false);
                set_time_limit(30);
            }
        }
    }

    /**
     *
     */
    private function varying() {
        if(show_api_debug)
            DebugConsole::insert_info('api.php','Call varying');

        if(!empty($this->api_callback) && $this->api_callback != false) {
            if ($this->call_varying === hash('crc32', $this->api_callback)) {
                $this->api_callback = json_decode($this->api_callback, true);

                if(show_api_debug)
                    DebugConsole::insert_info('api.php','api_callback = <pre>'.print_r($this->api_callback).'</pre>');

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
        if(!array_key_exists('type',$this->api_input))
            $this->api_input['type'] = 'json';

        $this->api_input['language'] = language_short_tag();

        if(show_api_debug)
            DebugConsole::insert_info('api.php','api_input = <pre>'.print_r($this->api_input).'</pre>');

        $this->api_callback = get_external_contents('https://'.$this->api_server,$this->api_input);

        if(show_api_debug)
            DebugConsole::insert_info('api.php','api_callback = <pre>'.print_r($this->api_callback).'</pre>');

        if(!empty($this->api_callback) && $this->api_callback != false && strpos($this->api_callback,'not found') === false) {
            $this->call_varying = explode('[hash]',$this->api_callback);
            if(count($this->call_varying) == 2) {
                $this->api_callback = trim($this->call_varying[0]);
                $this->call_varying = trim($this->call_varying[1]);
            }
        }
    }
}