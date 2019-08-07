<?php

namespace Webmasters\Doctrine;

use Doctrine\Common\Annotations\{CachedReader, AnnotationReader};
use Doctrine\ORM\Mapping\Driver\DriverChain;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM as ORM;

interface IBootstrap {
    public static function getInstance(Configuration $configuration): Bootstrap;
    public function getConfiguration(): Configuration;
    public function setConfiguration(Configuration $configuration): void;
    public function setCustomOption(string $key,$var): void;
    public function getCustomOption(string $key);
    public function isDebug(): bool;
    public function getAnnotationReader(): AnnotationReader;
    public function getCachedAnnotationReader(): CachedReader;
    public function getDriverChain(): DriverChain;
    public function getOrmConfiguration(): ORM\Configuration;
    public function getListener($ext, $name);
    public function getEventManager(): EventManager;
    public function getEntityManager(): EntityManager;
}