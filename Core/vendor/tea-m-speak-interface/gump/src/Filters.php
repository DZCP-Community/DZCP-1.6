<?php
/**
 * GUMP - A fast, extensible PHP input validation class.
 *
 * @author      Sean Nieuwoudt (http://twitter.com/SeanNieuwoudt)
 * @author      Filis Futsarov (http://twitter.com/FilisCode)
 * @copyright   Copyright (c) 2017 wixelhq.com
 * @version     1.6
 */

namespace Wixel\GUMP\Filters;

use Wixel\GUMP\GUMP;

class Filters {
    /**
     * @var GUMP
     */
    private $gump;

    /**
     * Filters constructor.
     * @param GUMP $gump
     */
    public function __construct(GUMP $gump) { $this->gump = $gump; }

    /**
     * Replace noise words in a string (http://tax.cchgroup.com/help/Avoiding_noise_words_in_your_search.htm).
     * @usage: '<index>' => 'noise_words'
     * @param string $value
     * @param array  $params
     * @return string
     */
    public function filter_noise_words(string $value,array $params = null): string {
        $value = preg_replace('/\s\s+/u', chr(32), $value);
        $value = " $value ";
        $words = explode(',', $this->gump->en_noise_words);

        foreach ($words as $word) {
            $word = trim($word);
            $word = " $word "; // Normalize
            if (stripos($value, $word) !== false) {
                $value = str_ireplace($word, chr(32), $value);
            }
        }

        return trim($value);
    }

    /**
     * Remove all known punctuation from a string.
     * @usage: '<index>' => 'rmpunctuataion'
     * @param string $value
     * @param array  $params
     * @return string
     */
    public function filter_rmpunctuation(string $value,array $params = null): string {
        return preg_replace("/(?![.=$'€%-])\p{P}/u", '', $value);
    }

    /**
     * Sanitize the string by removing any script tags.
     * @usage: '<index>' => 'sanitize_string'
     * @param string $value
     * @param array  $params
     * @return string
     */
    public function filter_sanitize_string(string $value,array $params = null): string {
        return filter_var($value, FILTER_SANITIZE_STRING);
    }

    /**
     * Sanitize the string by urlencoding characters.
     * @usage: '<index>' => 'urlencode'
     * @param string $value
     * @param array  $params
     * @return string
     */
    public function filter_urlencode(string $value,array $params = null): string {
        return filter_var($value, FILTER_SANITIZE_ENCODED);
    }

    /**
     * Sanitize the string by converting HTML characters to their HTML entities.
     * @usage: '<index>' => 'htmlencode'
     * @param string $value
     * @param array  $params
     * @return string
     */
    public function filter_htmlencode(string $value,array $params = null): string {
        return filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    /**
     * Sanitize the string by removing illegal characters from emails.
     * @usage: '<index>' => 'sanitize_email'
     * @param string $value
     * @param array  $params
     * @return string
     */
    public function filter_sanitize_email(string $value,array $params = null): string {
        return filter_var($value, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitize the string by removing illegal characters from numbers.
     * @param string $value
     * @param array $params
     * @return int
     */
    public function filter_sanitize_numbers(string $value,array $params = null): int {
        return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Sanitize the string by removing illegal characters from float numbers.
     * @param string $value
     * @param array  $params
     * @return string
     */
    public function filter_sanitize_floats(string $value,array $params = null): string {
        return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * Filter out all HTML tags except the defined basic tags.
     * @param string $value
     * @param array  $params
     * @return string
     */
    public function filter_basic_tags(string $value,array $params = null): string {
        return strip_tags($value, $this->gump->basic_tags);
    }

    /**
     * Convert the provided numeric value to a whole number.
     * @param string $value
     * @param array $params
     * @return int
     */
    public function filter_whole_number(string $value,array $params = null): int {
        return intval($value);
    }

    /**
     * Convert MS Word special characters to web safe characters.
     * [“, ”, ‘, ’, –, …] => [", ", ', ', -, ...]
     * @param string $value
     * @param array  $params
     * @return string
     */
    public function filter_ms_word_characters(string $value,array $params = null): string {
        $word_open_double  = '“';
        $word_close_double = '”';
        $web_safe_double   = '"';

        $value = str_replace([$word_open_double, $word_close_double], $web_safe_double, $value);

        $word_open_single  = '‘';
        $word_close_single = '’';
        $web_safe_single   = "'";

        $value = str_replace([$word_open_single, $word_close_single], $web_safe_single, $value);

        $word_em     = '–';
        $web_safe_em = '-';

        $value = str_replace($word_em, $web_safe_em, $value);

        $word_ellipsis = '…';
        $web_ellipsis  = '...';

        $value = str_replace($word_ellipsis, $web_ellipsis, $value);

        return $value;
    }

    /**
     * Converts to lowercase.
     * @param string $value
     * @param array  $params
     * @return string
     */
    public function filter_lower_case(string $value,array $params = null): string {
        return strtolower($value);
    }

    /**
     * Converts to uppercase.
     * @param string $value
     * @param array  $params
     * @return string
     */
    public function filter_upper_case(string $value,array $params = null): string {
        return strtoupper($value);
    }

    /**
     * Converts value to url-web-slugs.
     * Credit:
     * https://stackoverflow.com/questions/40641973/php-to-convert-string-to-slug
     * http://cubiq.org/the-perfect-php-clean-url-generator
     * @param string $value
     * @param array  $params
     * @return string
     */
    public function filter_slug(string $value,array $params = null): string {
        $delimiter = '-';
        return strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $value))))), $delimiter));
    }

    /**
     * Make a string's first character uppercase.
     * @param string $value
     * @param array  $params
     * @return string
     */
    public function filter_ucfirst(string $value,array $params = null): string {
        return ucfirst($value);
    }
}