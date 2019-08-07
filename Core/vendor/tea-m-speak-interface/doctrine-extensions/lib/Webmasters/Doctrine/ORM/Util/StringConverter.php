<?php

namespace Webmasters\Doctrine\ORM\Util;

/**
 * Class StringConverter
 * @package Webmasters\Doctrine\ORM\Util
 */
class StringConverter {
    /**
     * @param $camelCase
     * @return string
     */
    public static function decamelize($camelCase): string {
        return ltrim(strtolower(preg_replace('/([A-Z])/', '_$1', $camelCase)), '_');
    }

    /**
     * @param $underscore
     * @return string
     */
    public static function camelize($underscore): string {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $underscore)));
    }

    /**
     * @param $underscore
     * @return string
     */
    public static function camelizeLcFirst($underscore) : string {
        return lcfirst(self::camelize($underscore));
    }
}
