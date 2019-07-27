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

/**
 * Class InvalidCountryName.
 *
 * @since   0.3.0
 *
 * @package BrightNucleus\CountryCodes\Exception
 * @author  Alain Schlesser <alain.schlesser@gmail.com>
 */
class InvalidCountryName extends InvalidArgumentException
{

    /**
     * Instantiate a new InvalidCountryName exception from a specific country name.
     *
     * @since 0.3.0
     *
     * @param mixed $name Invalid country name that was passed in.
     * @return static
     */
    public static function fromName($name)
    {
        $type        = gettype($name);
        $valueString = static::getValueString($name, ' of value ');
        $message     = "Invalid country name of type {$type}{$valueString}";

        return new static($message);
    }
}
