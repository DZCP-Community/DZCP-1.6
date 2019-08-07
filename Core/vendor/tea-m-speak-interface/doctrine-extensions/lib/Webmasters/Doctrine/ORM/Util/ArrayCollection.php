<?php

namespace Webmasters\Doctrine\ORM\Util;

/**
 * Class ArrayCollection
 * @package Webmasters\Doctrine\ORM\Util
 */
class ArrayCollection {
    /**
     * @param $collection
     * @return array
     */
    public static function getUniques($collection): array {
        $array = $collection->toArray();
        return array_unique($array);
    }

    /**
     * @param $collection
     * @return array
     */
    public static function getDuplicates($collection): array {
        $array = $collection->toArray();
        return array_unique(array_diff_assoc($array, array_unique($array)));
    }
}
