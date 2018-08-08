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
 * Class InvalidCountryCode.
 *
 * @since   0.3.0
 *
 * @package BrightNucleus\CountryCodes\Exception
 * @author  Alain Schlesser <alain.schlesser@gmail.com>
 */
class InvalidCountryCode extends InvalidArgumentException
{

    /**
     * Instantiate a new InvalidCountryCode exception from a specific country code.
     *
     * @since 0.3.0
     *
     * @param mixed $code Invalid country code that was passed in.
     * @return static
     */
    public static function fromCode($code)
    {
        $type        = gettype($code);
        $valueString = static::getValueString($code, ' of value ');
        $message     = "Invalid country code of type {$type}{$valueString}";

        return new static($message);
    }

}
