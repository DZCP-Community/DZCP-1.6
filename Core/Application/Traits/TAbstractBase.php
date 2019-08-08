<?php
/**
 * @projekt DZCP - deV!L`z ClanPortal 2.0
 * @url http://www.dzcp.de
 * @author Lucas Brucksch
 * @copyright (C) 2019 Codedesigns (MIT)
 * @package DZCP\Exceptions
 * @version 1.0
 */

namespace Application\Traits;

/**
 * Trait TAbstractBase
 * @package Application\Traits
 */
trait TAbstractBase
{
    /**
     * @param string $dir
     * @param array $options [onlyDir, onlyFiles, useCache, reCache, ttlCache]
     * @return array|null
     */
    public function scandir(string $dir,array $options = []): ?array {
        $options += ['onlyDir'=>false,'onlyFiles'=>false,'useCache'=>true,'reCache'=>false,'ttlCache'=>30];
        $cache = sha1($dir.implode($options));
        $metadataCache = $this->bootstrap->getConfiguration()->getMetadataCacheImpl();
        if($metadataCache->contains($cache) && $options['useCache'] && !$options['reCache']) {
            return unserialize($metadataCache->fetch($cache));
        }

        $files = scandir($dir);
        foreach ($files as $key => $file) {
            if($file == '.' || $file == '..') {
                unset($files[$key]);
            }

            if($options['onlyDir'] && is_file($files[$key])) {
                unset($files[$key]);
            } else if($options['onlyFiles'] && is_dir($files[$key])) {
                unset($files[$key]);
            }
        }

        if(!count($files))
            return null;

        sort($files);
        if($options['useCache']) {
            $metadataCache->save($cache,serialize($files),intval($options['ttlCache']));
        }

        return $files;
    }
}