<?php

namespace Traits;

use Webmasters\Doctrine\ORM\Util\StringConverter;

/**
 * Trait ArrayMappable
 * @package Traits
 */
trait ArrayMappable {
    /**
     * @param array $data
     * @param bool $camelize
     */
    public function mapFromArray(array $data, $camelize = true): void {
        if ($data) {
            foreach ($data as $key => $value) {
                if ($camelize) {
                    $setterName = 'set' . StringConverter::camelize($key);
                } else {
                    $setterName = 'set' . ucfirst($key);
                }

                if (method_exists($this, $setterName)) {
                    $this->$setterName($value);
                }
            }
        }
    }

    /**
     * @param bool $withId
     * @param bool $decamelize
     * @return array
     */
    public function mapToArray($withId = true, $decamelize = true): array {
        $attributes = get_object_vars($this);

        $result = [];
        foreach ($attributes as $key => $value) {
            if ($decamelize) {
                $key = StringConverter::decamelize($key);
            }

            $result[$key] = $value;
        }

        if ($withId === false) {
            unset($result['id']);
        }

        return $result;
    }
}
