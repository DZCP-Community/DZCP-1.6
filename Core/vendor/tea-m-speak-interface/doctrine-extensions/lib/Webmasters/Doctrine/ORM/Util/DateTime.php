<?php

namespace Webmasters\Doctrine\ORM\Util;

/**
 * Class DateTime
 * @package Webmasters\Doctrine\ORM\Util
 */
class DateTime {
    protected $raw = null;
    protected $datetime = null;
    protected $errors = [];

    /**
     * DateTime constructor.
     * @param $value
     * @throws \Exception
     */
    public function __construct($value) {
        if (is_string($value)) {
            $this->raw = $value;
            $this->convert2Object();
        } elseif ($value instanceof \DateTime) {
            $this->datetime = $value;
        } elseif ($value instanceof DateTime) {
            $this->raw = $value->getRaw();
            $this->datetime = $value->getDateTime();
            $this->errors = $value->getErrors();
        }
    }

    /**
     * @return string
     */
    public function getRaw(): string {
        return $this->raw;
    }

    /**
     * @return \DateTime|DateTime
     */
    public function getDateTime() {
        return $this->datetime;
    }

    /**
     * @return array
     */
    public function getErrors(): array {
        return $this->errors;
    }

    /**
     * @param $format
     * @return string|null
     */
    public function format($format): ?string {
        $result = $this->raw;
        if ($this->isValid()) {
            $result = $this->datetime->format($format);
        }

        return $result;
    }

    /**
     * @param $modification
     */
    public function modify($modification): void {
        if ($this->isValid()) {
            $this->datetime->modify($modification);
        }
    }

    /**
     * @param $datetime
     * @return bool|\DateInterval
     */
    public function diff($datetime) {
        $result = false;
        if ($this->isValid() && $datetime->isValid()) {
            $result = $this->datetime->diff($datetime->getDateTime());
        }

        return $result;
    }

    public function isValid()
    {
        return (
            !empty($this->datetime) &&
            ($this->datetime instanceof \DateTime) &&
            !$this->hasRollOver()
        );
    }

    /**
     * @return bool
     */
    public function hasRollOver(): bool {
        return (isset($this->errors['warnings']) &&
                isset($this->errors['warnings'][11]));
    }

    /**
     * @param $datetime
     * @param string $format
     * @return bool
     */
    public function isValidClosingDate($datetime, $format = '%r%a'): bool {
        $diff = $this->diff($datetime);

        $result = false;
        if ($diff !== false) {
            $check = intval($diff->format($format));
            if ($check >= 0) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * @throws \Exception
     */
    protected function convert2Object(): void {
        if ($this->isValidDate($this->raw)) {
            $this->datetime = new \DateTime($this->raw);
            $this->errors = \DateTime::getLastErrors();
        }
    }

    /**
     * @param $str
     * @return bool
     */
    protected function isValidDate($str): bool {
        $stamp = strtotime($str);

        if ($stamp === false) {
            return false;
        } elseif (checkdate(date('m', $stamp), date('d', $stamp), date('Y', $stamp))) {
            return true;
        }

        return false;
    }
}
