<?php
/**
 * @projekt DZCP - deV!L`z ClanPortal 2.0
 * @url http://www.dzcp.de
 * @author Lucas Brucksch
 * @copyright (C) 2019 Codedesigns (MIT)
 * @package DZCP\Exceptions
 * @version 1.0
 */

namespace Application\Helper;

/**
 * Namespace Imports
 */
use Webmasters\Doctrine\Bootstrap;
use Smarty_CacheResource_KeyValueStore;

/**
 * Class Smarty_CacheResource_Doctrine
 * @package Application\Controllers
 */
class Smarty_CacheResource_Doctrine extends Smarty_CacheResource_KeyValueStore {
    private $resultCache;

    /**
     * Smarty_CacheResource_Doctrine constructor.
     * @param Bootstrap $bootstrap
     */
    public function __construct(Bootstrap $bootstrap) {
        $this->resultCache = $bootstrap->getConfiguration()->getResultCacheImpl();
    }

    /**
     * Read values for a set of keys from cache
     * @param array $keys list of keys to fetch
     * @return array list of values with the given keys used as indexes
     */
    protected function read(array $keys): array {
        $lookup = [];
        foreach ($keys as $key) {
            $key_sha = sha1($key);
            $lookup[$key_sha] = $key;
        } unset($keys);

        $_res = [];
        foreach ($lookup as $key => $var) {
            if($this->resultCache->contains($var)) {
                $_res[$var] = $this->resultCache->fetch($key);
            }
        }

        return $_res;
    }

    /**
     * Save values for a set of keys to cache
     * @param array $keys list of values to save
     * @param int $expire expiration time
     * @return boolean true on success, false on failure
     */
    protected function write(array $keys, $expire = null): bool {
        foreach ($keys as $key=> $data) {
            $this->resultCache->save(sha1($key), serialize([$data]), $expire);
        }

        return true;
    }

    /**
     * Remove values from cache
     * @param array $keys list of keys to delete
     * @return boolean true on success, false on failure
     */
    protected function delete(array $keys): bool {
        $lookup = [];
        foreach ($keys as $key) {
            $key_sha = sha1($key);
            $lookup[$key_sha] = $key;
        } unset($keys);

        foreach ($lookup as $key => $var) {
            if($this->resultCache->contains($var)) {
                $this->resultCache->delete($key);
            }
        }

        return true;
    }
}