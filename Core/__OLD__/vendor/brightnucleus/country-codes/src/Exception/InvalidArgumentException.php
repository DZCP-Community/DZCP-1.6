<?php
/**
 * Bright Nucleus CountryCodes Component.
 *
 * @package   BrightNucleus\CountryCodes
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   MIT
 * @link      http://www.brightnucleus.com/
 * @copyright 2016 Alain Schlesser, Bright Nucleus
 */

namespace BrightNucleus\CountryCodes\Exception;

use BrightNucleus\Exception\InvalidArgumentException as BNInvalidArgumentException;

/**
 * Class InvalidArgumentException.
 *
 * @since   0.3.0
 *
 * @package BrightNucleus\CountryCodes\Exception
 * @author  Alain Schlesser <alain.schlesser@gmail.com>
 */
class InvalidArgumentException extends BNInvalidArgumentException implements CountryCodesException
{

    /**
     * Get an approximate value of the invalid argument that was passed in.
     *
     * @since 0.3.0
     *
     * @param mixed  $variable Variable to deduce the value of.
     * @param string $prefix   Prefix string to prepend to value.
     * @param string $suffix   Suffix string to append to value.
     * @return string
     */
    public static function getValueString($variable, $prefix = '', $suffix = '')
    {
        switch (gettype($variable)) {
            case 'boolean':
                return $variable ? "{$prefix}true{$suffix}" : "{$prefix}false{$suffix}";
            case 'integer':
            case 'double':
            case 'string':
                return $prefix . ((string)$variable) . $suffix;
            case 'array':
            case 'object':
            case 'NULL':
            case 'resource':
            case 'resource (closed)':
            case 'unknown type':
                return '';
        }
    }
}
