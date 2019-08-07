<?php

namespace Webmasters\Doctrine;

use Doctrine\Common\Cache\Cache;
use Webmasters\Doctrine\ORM\Util\OptionsCollection;

interface IConfiguration {
    public function getAutogenerateProxyClasses(): bool;
    public function setAutogenerateProxyClasses(bool $var = true): void;
    public function getBaseDir(): string;
    public function setBaseDir(string $dir = ""): void;
    public function getDebugMode(): bool;
    public function setDebugMode(bool $var = true): void;
    public function getEntityManagerClass(): string;
    public function setEntityManagerClass(string $class = ""): void;
    public function getEntityDir(): string;
    public function setEntityDir(string $dir = ""): void;
    public function getGedmoExt(): array;
    public function setGedmoExt(array $ext = []): void;
    public function getProxyDir(): string;
    public function setProxyDir(string $dir = ""): void;
    public function getVendorDir(): string;
    public function setVendorDir(string $dir = ""): void;
    public function getMetadataCacheImpl(): ?Cache;
    public function setMetadataCacheImpl(Cache $cache = null): void;
    public function getQueryCacheImpl(): ?Cache;
    public function setQueryCacheImpl(Cache $cache = null): void;
    public function getResultCacheImpl(): ?Cache;
    public function setResultCacheImpl(Cache $cache = null): void;
    public function getCustomConfig(): OptionsCollection;
    public function setCustomConfig(OptionsCollection $config): void;
    public function setCustomConfigArray(array $config): void;
}