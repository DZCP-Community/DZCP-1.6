<?php
/**
 * GUMP - A fast, extensible PHP input validation class.
 *
 * @author      Sean Nieuwoudt (http://twitter.com/SeanNieuwoudt)
 * @author      Filis Futsarov (http://twitter.com/FilisCode)
 * @copyright   Copyright (c) 2017 wixelhq.com
 * @version     1.6
 */

namespace Wixel\GUMP\Validators;

use Wixel\GUMP\GUMP;

class Validators {
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
     * Verify that a value is contained within the pre-defined value set.
     * @usage: '<index>' => 'contains,value value value'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_contains(string $field, array $input, string $param = null): ?array {
        if (!isset($input[$field])) return null;

        $param = trim(strtolower($param));
        $value = trim(strtolower($input[$field]));
        if (preg_match_all('#\'(.+?)\'#', $param, $matches, PREG_PATTERN_ORDER))
            $param = $matches[1];
        else
            $param = explode(chr(32), $param);

        if (in_array($value, $param)) // valid, return nothing
            return null;

        return ['field' => $field, 'value' => $value, 'rule' => __FUNCTION__, 'param' => $param];
    }

    /**
     * Verify that a value is contained within the pre-defined value set.
     * OUTPUT: will NOT show the list of values.
     * @usage: '<index>' => 'contains_list,value;value;value'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_contains_list(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        $param = trim(strtolower($param));
        $value = trim(strtolower($input[$field]));
        $param = explode(';', $param);
        if (in_array($value, $param)) // valid, return nothing
            return null;

        return ['field' => $field, 'value' => $value, 'rule' => __FUNCTION__, 'param' => $param];
    }

    /**
     * Verify that a value is NOT contained within the pre-defined value set.
     * OUTPUT: will NOT show the list of values.
     * @usage: '<index>' => 'doesnt_contain_list,value;value;value'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_doesnt_contain_list(string $field, array $input, ?string $param = null): ?array  {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        $param = trim(strtolower($param));
        $value = trim(strtolower($input[$field]));
        $param = explode(';', $param);
        if (!in_array($value, $param)) // valid, return nothing
            return null;

        return ['field' => $field, 'value' => $value, 'rule' => __FUNCTION__, 'param' => $param];
    }

    /**
     * Check if the specified key is present and not empty.
     * @usage: '<index>' => 'required'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_required(string $field, array $input, ?string $param = null): ?array {
        if (isset($input[$field]) && ($input[$field] === false || $input[$field] === 0 || $input[$field] === 0.0 || $input[$field] === '0' || !empty($input[$field])))
            return null;

        return ['field' => $field, 'value' => null, 'rule' => __FUNCTION__, 'param' => $param];
    }

    /**
     * Determine if the provided email is valid.
     * @usage: '<index>' => 'valid_email'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_valid_email(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (!filter_var($input[$field], FILTER_VALIDATE_EMAIL)) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Determine if the provided value length is less or equal to a specific value.
     * @usage: '<index>' => 'max_len,240'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_max_len(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field])) return null;

        if (function_exists('mb_strlen'))
            if (mb_strlen($input[$field]) <= (int) $param) {
                return null;
        } else {
            if (strlen($input[$field]) <= (int) $param)
                return null;
        }

        return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
    }

    /**
     * Determine if the provided value length is more or equal to a specific value.
     * @usage: '<index>' => 'min_len,4'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_min_len(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (function_exists('mb_strlen'))
            if (mb_strlen($input[$field]) >= (int) $param) {
                return null;
        } else {
            if (strlen($input[$field]) >= (int) $param)
                return null;
        }

        return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
    }

    /**
     * Determine if the provided value length matches a specific value.
     * @usage: '<index>' => 'exact_len,5'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_exact_len(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (function_exists('mb_strlen')) {
            if (mb_strlen($input[$field]) == (int) $param) {
                return null;
            }
        } else {
            if (strlen($input[$field]) == (int) $param) {
                return null;
            }
        }

        return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
    }

    /**
     * Determine if the provided value contains only alpha characters.
     * @usage: '<index>' => 'alpha'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_alpha(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (!preg_match('/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i', $input[$field]) !== false) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Determine if the provided value contains only alpha-numeric characters.
     * @usage: '<index>' => 'alpha_numeric'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_alpha_numeric(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (!preg_match('/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i', $input[$field]) !== false) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Determine if the provided value contains only alpha characters with dashed and underscores.
     * @usage: '<index>' => 'alpha_dash'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_alpha_dash(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (!preg_match('/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ_-])+$/i', $input[$field]) !== false) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Determine if the provided value contains only alpha numeric characters with spaces.
     * @usage: '<index>' => 'alpha_numeric_space'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_alpha_numeric_space(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (!preg_match("/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s])+$/i", $input[$field]) !== false) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Determine if the provided value contains only alpha numeric characters with spaces.
     * @usage: '<index>' => 'alpha_space'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_alpha_space(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (!preg_match("/^([0-9a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s])+$/i", $input[$field]) !== false) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Determine if the provided value is a valid number or numeric string.
     * @usage: '<index>' => 'numeric'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_numeric(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (!is_numeric($input[$field])) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Determine if the provided value is a valid integer.
     * @usage: '<index>' => 'integer'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_integer(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (filter_var($input[$field], FILTER_VALIDATE_INT) === false) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Determine if the provided value is a PHP accepted boolean.
     * @usage: '<index>' => 'boolean'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_boolean(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        $booleans = array('1','true',true,1,'0','false',false,0,'yes','no','on','off');
        if (in_array($input[$field], $booleans, true )) return null;

        return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
    }

    /**
     * Determine if the provided value is a valid float.
     * @usage: '<index>' => 'float'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_float(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (filter_var($input[$field], FILTER_VALIDATE_FLOAT) === false) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Determine if the provided value is a valid URL.
     * @usage: '<index>' => 'valid_url'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_valid_url(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (!filter_var($input[$field], FILTER_VALIDATE_URL)) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Determine if a URL exists & is accessible.
     * @usage: '<index>' => 'url_exists'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_url_exists(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        $url = parse_url(strtolower($input[$field]));

        if (isset($url['host'])) {
            $url = $url['host'];
        }

        if (function_exists('checkdnsrr')  && function_exists('idn_to_ascii')) {
            if (checkdnsrr(idn_to_ascii($url), 'A') === false) {
                return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
            }
        } else {
            if (gethostbyname($url) == $url) {
                return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
            }
        }
    }

    /**
     * Determine if the provided value is a valid IP address.
     * @usage: '<index>' => 'valid_ip'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_valid_ip(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (!filter_var($input[$field], FILTER_VALIDATE_IP) !== false) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Determine if the provided value is a valid IPv4 address.
     * @usage: '<index>' => 'valid_ipv4'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     * @see http://pastebin.com/UvUPPYK0
     */
    public function validate_valid_ipv4(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (!filter_var($input[$field], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Determine if the provided value is a valid IPv6 address.
     * @usage: '<index>' => 'valid_ipv6'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_valid_ipv6(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (!filter_var($input[$field], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Determine if the input is a valid credit card number.
     * @usage: '<index>' => 'valid_cc'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_valid_cc(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        $number = preg_replace('/\D/', '', $input[$field]);

        if (function_exists('mb_strlen')) {
            $number_length = mb_strlen($number);
        } else {
            $number_length = strlen($number);
        }


        /**
         * Bail out if $number_length is 0.
         * This can be the case if a user has entered only alphabets
         *
         * @since 1.5
         */
        if( $number_length == 0 ) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }


        $parity = $number_length % 2;

        $total = 0;

        for ($i = 0; $i < $number_length; ++$i) {
            $digit = $number[$i];

            if ($i % 2 == $parity) {
                $digit *= 2;

                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $total += $digit;
        }

        if ($total % 10 == 0) {
            return null; // Valid
        }

        return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
    }

    /**
     * Determine if the input is a valid human name [Credits to http://github.com/ben-s].
     * @usage: '<index>' => 'valid_name'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_valid_name(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (!preg_match("/^([a-z \p{L} '-])+$/i", $input[$field]) !== false) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Determine if the provided input is likely to be a street address using weak detection.
     * @usage: '<index>' => 'street_address'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_street_address(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        // Theory: 1 number, 1 or more spaces, 1 or more words
        $hasLetter = preg_match('/[a-zA-Z]/', $input[$field]);
        $hasDigit = preg_match('/\d/', $input[$field]);
        $hasSpace = preg_match('/\s/', $input[$field]);

        $passes = $hasLetter && $hasDigit && $hasSpace;

        if (!$passes) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Determine if the provided value is a valid IBAN.
     * @usage: '<index>' => 'iban'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_iban(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        static $character = array(
            'A' => 10, 'C' => 12, 'D' => 13, 'E' => 14, 'F' => 15, 'G' => 16,
            'H' => 17, 'I' => 18, 'J' => 19, 'K' => 20, 'L' => 21, 'M' => 22,
            'N' => 23, 'O' => 24, 'P' => 25, 'Q' => 26, 'R' => 27, 'S' => 28,
            'T' => 29, 'U' => 30, 'V' => 31, 'W' => 32, 'X' => 33, 'Y' => 34,
            'Z' => 35, 'B' => 11
        );

        if (!preg_match("/\A[A-Z]{2}\d{2} ?[A-Z\d]{4}( ?\d{4}){1,} ?\d{1,4}\z/", $input[$field])) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }

        $iban = str_replace(' ', '', $input[$field]);
        $iban = substr($iban, 4).substr($iban, 0, 4);
        $iban = strtr($iban, $character);

        if (bcmod($iban, 97) != 1) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Determine if the provided input is a valid date (ISO 8601)
     * or specify a custom format.
     * @usage: '<index>' => 'date'
     * @param string $field
     * @param array $input date ('Y-m-d') or datetime ('Y-m-d H:i:s')
     * @param string|null $param Custom date format
     * @return array|null
     */
    public function validate_date(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        // Default
        if (!$param)
        {
            $cdate1 = date('Y-m-d', strtotime($input[$field]));
            $cdate2 = date('Y-m-d H:i:s', strtotime($input[$field]));

            if ($cdate1 != $input[$field] && $cdate2 != $input[$field])
            {
                return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
            }
        } else {
            $date = \DateTime::createFromFormat($param, $input[$field]);

            if ($date === false || $input[$field] != date($param, $date->getTimestamp()))
            {
                return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
            }
        }
    }

    /**
     * Determine if the provided input meets age requirement (ISO 8601).
     * @usage: '<index>' => 'min_age,13'
     * @param string $field
     * @param array $input date ('Y-m-d') or datetime ('Y-m-d H:i:s')
     * @param string|null $param int
     * @return array|null
     */
    public function validate_min_age(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        $cdate1 = new DateTime(date('Y-m-d', strtotime($input[$field])));
        $today = new DateTime(date('d-m-Y'));

        $interval = $cdate1->diff($today);
        $age = $interval->y;

        if ($age <= $param) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Determine if the provided numeric value is lower or equal to a specific value.
     * @usage: '<index>' => 'max_numeric,50'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_max_numeric(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (is_numeric($input[$field]) && is_numeric($param) && ($input[$field] <= $param)) {
            return null;
        }

        return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
    }

    /**
     * Determine if the provided numeric value is higher or equal to a specific value.
     * @usage: '<index>' => 'min_numeric,1'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_min_numeric(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (is_numeric($input[$field]) && is_numeric($param) && ($input[$field] >= $param)) {
            return null;
        }

        return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
    }

    /**
     * Determine if the provided value starts with param.
     * @usage: '<index>' => 'starts,Z'
     * @param string $field
     * @param array  $input
     * @return array|null
     */
    public function validate_starts(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (strpos($input[$field], $param) !== 0) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Checks if a file was uploaded.
     * @usage: '<index>' => 'required_file'
     * @param  string $field
     * @param  array $input
     * @return array|null
     */
    public function validate_required_file(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field])) {
            return null;
        }

        if (is_array($input[$field]) && $input[$field]['error'] !== 4) {
            return null;
        }

        return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
    }

    /**
     * Check the uploaded file for extension for now
     * checks only the ext should add mime type check.
     * @usage: '<index>' => 'extension,png;jpg;gif
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_extension(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field])) return null;

        if (is_array($input[$field]) && $input[$field]['error'] !== 4) {
            $param = trim(strtolower($param));
            $allowed_extensions = explode(';', $param);

            $path_info = pathinfo($input[$field]['name']);
            $extension = isset($path_info['extension']) ? $path_info['extension'] : false;

            if ($extension && in_array(strtolower($extension), $allowed_extensions)) return null;

            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Determine if the provided field value equals current field value.
     * @usage: '<index>' => 'equalsfield,Z'
     * @param string $field
     * @param array $input
     * @param string|null $param field to compare with
     * @return array|null
     */
    public function validate_equalsfield(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;
        if ($input[$field] == $input[$param]) return null;

        return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
    }

    /**
     * Determine if the provided field value is a valid GUID (v4)
     * @usage: '<index>' => 'guidv4'
     * @param string $field
     * @param array $input
     * @param string|null $param field to compare with
     * @return array|null
     */
    public function validate_guidv4(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;
        if (preg_match("/\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/", $input[$field])) return null;

        return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
    }

    /**
     * Trims whitespace only when the value is a scalar.
     * @param mixed $value
     * @return array|null
     */
    private function trimScalar($value): ?array {
        if (is_scalar($value)) {
            $value = trim($value);
        }

        return $value;
    }

    /**
     * Determine if the provided value is a valid phone number.
     * @usage: '<index>' => 'phone_number'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     * Examples:
     *  555-555-5555: valid
     *  555.555.5555: valid
     *  5555425555: valid
     *  555 555 5555: valid
     *  1(519) 555-4444: valid
     *  1 (519) 555-4422: valid
     *  1-555-555-5555: valid
     *  1-(555)-555-5555: valid
     */
    public function validate_phone_number(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        $regex = '/^(\d[\s\-\.]?)?[\(\[\s\-\.]{0,2}?\d{3}[\)\]\s\-\.]{0,2}?\d{3}[\s\-\.]?\d{4}$/i';
        if (!preg_match($regex, $input[$field])) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Custom regex validator.
     * @usage: '<index>' => 'regex,/your-regex-expression/'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_regex(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        $regex = $param;
        if (!preg_match($regex, $input[$field])) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * JSON validator.
     * @usage: '<index>' => 'valid_json_string'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_valid_json_string(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (!is_string($input[$field]) || !is_object(json_decode($input[$field]))) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Check if an input is an array and if the size is more or equal to a specific value.
     * @usage: '<index>' => 'valid_array_size_greater,1'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_valid_array_size_greater(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (!is_array($input[$field]) || sizeof($input[$field]) < (int)$param) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Check if an input is an array and if the size is less or equal to a specific value.
     * @usage: '<index>' => 'valid_array_size_lesser,1'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_valid_array_size_lesser(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (!is_array($input[$field]) || sizeof($input[$field]) > (int)$param) {
            return ['field' => $field, 'value' => $input[$field], 'rule'  => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Check if an input is an array and if the size is equal to a specific value.
     * @usage: '<index>' => 'valid_array_size_equal,1'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_valid_array_size_equal(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (!is_array($input[$field]) || sizeof($input[$field]) == (int)$param) {
            return ['field' => $field, 'value' => $input[$field], 'rule'  => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Determine if the input is a valid person name in Persian/Dari or Arabic mainly in Afghanistan and Iran.
     * @usage: '<index>' => 'valid_persian_name'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_valid_persian_name(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (!preg_match("/^([ا آ أ إ ب پ ت ث ج چ ح خ د ذ ر ز ژ س ش ص ض ط ظ ع غ ف ق ک ك گ ل م ن و ؤ ه ة ی ي ئ ء ّ َ ِ ُ ً ٍ ٌ ْ\x{200B}-\x{200D}])+$/u", $input[$field]) !== false) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Determine if the input is a valid person name in English, Persian/Dari/Pashtu or Arabic mainly in Afghanistan and Iran.
     * @usage: '<index>' => 'valid_eng_per_pas_name'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_valid_eng_per_pas_name(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (!preg_match("/^([A-Za-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïñðòóôõöùúûüýÿ'\- ا آ أ إ ب پ ت ټ ث څ ج چ ح ځ خ د ډ ذ ر ړ ز ږ ژ س ش ښ ص ض ط ظ ع غ ف ق ک ګ ك گ ل م ن ڼ و ؤ ه ة ی ي ې ۍ ئ ؋ ء ّ َ ِ ُ ً ٍ ٌ ْ \x{200B}-\x{200D} \s])+$/u", $input[$field]) !== false) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Determine if the input is valid digits in Persian/Dari, Pashtu or Arabic format.
     * @usage: '<index>' => 'valid_persian_digit'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_valid_persian_digit(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (!preg_match("/^([۰۱۲۳۴۵۶۷۸۹٠١٢٣٤٥٦٧٨٩])+$/u", $input[$field]) !== false) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }


    /**
     * Determine if the input is a valid text in Persian/Dari or Arabic mainly in Afghanistan and Iran.
     * @usage: '<index>' => 'valid_persian_text'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_valid_persian_text(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (!preg_match("/^([ا آ أ إ ب پ ت ث ج چ ح خ د ذ ر ز ژ س ش ص ض ط ظ ع غ ف ق ک ك گ ل م ن و ؤ ه ة ی ي ئ ء ّ َ ِ ُ ً ٍ ٌ \. \/ \\ = \- \| \{ \} \[ \] ؛ : « » ؟ > < \+ \( \) \* ، × ٪ ٫ ٬ ! ۰۱۲۳۴۵۶۷۸۹٠١٢٣٤٥٦٧٨٩\x{200B}-\x{200D} \x{FEFF} \x{22} \x{27} \x{60} \x{B4} \x{2018} \x{2019} \x{201C} \x{201D} \s])+$/u", $input[$field]) !== false) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Determine if the input is a valid text in Pashtu mainly in Afghanistan.
     * @usage: '<index>' => 'valid_pashtu_text'
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_valid_pashtu_text(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field]) || empty($input[$field])) return null;

        if (!preg_match("/^([ا آ أ ب پ ت ټ ث څ ج چ ح ځ خ د ډ ذ ر ړ ز ږ ژ س ش ښ ص ض ط ظ ع غ ف ق ک ګ ل م ن ڼ و ؤ ه ة ی ې ۍ ي ئ ء ْ ٌ ٍ ً ُ ِ َ ّ ؋ \. \/ \\ = \- \| \{ \} \[ \] ؛ : « » ؟ > < \+ \( \) \* ، × ٪ ٫ ٬ ! ۰۱۲۳۴۵۶۷۸۹٠١٢٣٤٥٦٧٨٩ \x{200B}-\x{200D} \x{FEFF} \x{22} \x{27} \x{60} \x{B4} \x{2018} \x{2019} \x{201C} \x{201D} \s])+$/u", $input[$field]) !== false) {
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Determine if the provided value is a valid twitter handle.
     * @access protected
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_valid_twitter(string $field, array $input, ?string $param = null): ?array {
        if(!isset($input[$field]) || empty($input[$field])) return null;

        $json_twitter = $this->gump->getExternalContents("http://twitter.com/users/username_available?username=".$input[$field]);
        $twitter_response = json_decode($json_twitter);
        if($twitter_response->reason != "taken"){
            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }

    /**
     * Check the uploaded file for filesize
     * checks only the ext should add mime type check.
     * @usage: '<index>' => 'file_size,500kb
     * @param string $field
     * @param array $input
     * @param string|null $param
     * @return array|null
     */
    public function validate_file_size(string $field, array $input, ?string $param = null): ?array {
        if (!isset($input[$field])) return null;

        if (is_array($input[$field]) && $input[$field]['error'] !== 4) {
            $max_filesize = str_replace('kb', '', trim(strtolower($param))) * 1024;
            $filesize = $input[$field]['size'];
            if ( ($filesize > 0) && ($filesize < $max_filesize) )
                return null;

            return ['field' => $field, 'value' => $input[$field], 'rule' => __FUNCTION__, 'param' => $param];
        }
    }
}