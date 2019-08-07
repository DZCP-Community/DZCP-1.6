<?php

namespace Webmasters\Doctrine\ORM\Util;
/**
 * Class OptionsCollection
 * @package Webmasters\Doctrine\ORM\Util
 */
class OptionsCollection {
    protected $options = [];

    /**
     * OptionsCollection constructor.
     * @param $options
     */
    public function __construct($options) {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function all(): array {
        return $this->options;
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key): bool {
        $hasOption = false;
        if (isset($this->options[$key])) {
            $hasOption = true;
        }

        return $hasOption;
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value): void {
        $this->options[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed
     * @throws \Exception
     */
    public function get($key) {
        if (!$this->has($key)) {
            throw new \Exception(
                sprintf('Option "%s" missing', $key)
            );
        }

        return $this->options[$key];
    }
}
