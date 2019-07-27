<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

//-> Speichert Rückgaben der MySQL Datenbank zwischen um SQL-Queries einzusparen
use Phpfastcache\CacheManager;

final class dbc_index
{
    private static $index = array();

    /**
     * @param $index_key
     * @param $data
     */
    public static final function setIndex($index_key, $data)
    {
        global $cache, $config_cache;
        if (self::MemSetIndex()) {
            if (show_dbc_debug)
                DebugConsole::insert_info('dbc_index::setIndex()', 'Set index: "' . $index_key . '" to cache');

            if ($config_cache['dbc']) {
                $data_cache = null;
                try {
                    $data_cache = $cache->getItem('dbc_' . $index_key);
                } catch (\Phpfastcache\Exceptions\phpFastCacheInvalidArgumentException $e) {
                }
                $data_cache->set(serialize($data))->expiresAfter(1.5);
                $cache->save($data_cache);
            }
        }

        if (show_dbc_debug)
            DebugConsole::insert_info('dbc_index::setIndex()', 'Set index: "' . $index_key . '"');

        self::$index[$index_key] = $data;
    }

    /**
     * @param string $index_key
     * @return bool|mixed
     */
    public static final function getIndex(string $index_key)
    {
        if (!self::issetIndex($index_key))
            return false;

        if (show_dbc_debug)
            DebugConsole::insert_info('dbc_index::getIndex()', 'Get full index: "' . $index_key . '"');

        return self::$index[$index_key];
    }

    /**
     * @param string $index_key
     * @param string $key
     * @return bool
     */
    public static final function getIndexKey(string $index_key, string $key)
    {
        if (!self::issetIndex($index_key))
            return false;

        $data = self::$index[$index_key];
        if (empty($data) || !array_key_exists($key, $data))
            return false;

        return $data[$key];
    }

    /**
     * @param string $index_key
     * @return bool
     */
    public static final function issetIndex(string $index_key)
    {
        global $cache;
        if (isset(self::$index[$index_key])) return true;
        if (self::MemSetIndex()) {
            $data = null;
            try {
                $data = $cache->getItem('dbc_' . $index_key);
            } catch (\Phpfastcache\Exceptions\phpFastCacheInvalidArgumentException $e) {
            }

            if (!is_null($data->get())) {
                if (show_dbc_debug)
                    DebugConsole::insert_loaded('dbc_index::issetIndex()', 'Load index: "' . $index_key . '" from cache');

                self::$index[$index_key] = unserialize($data->get());
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public static final function MemSetIndex()
    {
        global $config_cache, $cache;
        if (!$config_cache['dbc'] || CacheManager::$fallback) {
            return false;
        }

        switch ($cache->getDriverName()) {
            case 'Files':
            case 'Zenddisk':
            case 'Sqlite':
                return false;
        }

        return true;
    }
}