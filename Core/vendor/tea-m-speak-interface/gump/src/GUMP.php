<?php
/**
 * GUMP - A fast, extensible PHP input validation class.
 *
 * @author      Sean Nieuwoudt (http://twitter.com/SeanNieuwoudt)
 * @author      Filis Futsarov (http://twitter.com/FilisCode)
 * @copyright   Copyright (c) 2017 wixelhq.com
 * @version     1.6
 */

namespace Wixel\GUMP;

use Wixel\GUMP\Validators\Validators;
use Wixel\GUMP\Filters\Filters;

class GUMP {
    // Singleton instance of GUMP
    protected static $instance = null;

    // Validation rules for execution
    protected $validation_rules = [];

    // Filter rules for execution
    protected $filter_rules = [];

    // Instance attribute containing errors from last run
    protected $errors = [];

    /**
     * @var Validators
     */
    protected $validators = null;

    /**
     * @var Filters
     */
    protected $filters = null;

    // Contain readable field names that have been set manually
    protected static $fields = [];

    // Custom validation methods
    protected static $validation_methods = [];

    // Custom validation methods error messages and custom ones
    protected static $validation_methods_errors = [];

    // Customer filter methods
    protected static $filter_methods = [];


    // ** ------------------------- Instance Helper ---------------------------- ** //

    /**
     * Function to create and return previously created instance
     * @return GUMP
     * @throws \Exception
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    // ** ------------------------- Validation Data ------------------------------- ** //

    public $basic_tags = '<br><p><a><strong><b><i><em><img><blockquote><code><dd><dl><hr><h1><h2><h3><h4><h5><h6><label><ul><li><span><sub><sup>';

    public $en_noise_words = "about,after,all,also,an,and,another,any,are,as,at,be,because,been,before,
                                     being,between,both,but,by,came,can,come,could,did,do,each,for,from,get,
                                     got,has,had,he,have,her,here,him,himself,his,how,if,in,into,is,it,its,it's,like,
                                     make,many,me,might,more,most,much,must,my,never,now,of,on,only,or,other,
                                     our,out,over,said,same,see,should,since,some,still,such,take,than,that,
                                     the,their,them,then,there,these,they,this,those,through,to,too,under,up,
                                     very,was,way,we,well,were,what,where,which,while,who,with,would,you,your,a,
                                     b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,$,1,2,3,4,5,6,7,8,9,0,_";

    // field characters below will be replaced with a space.
    protected $fieldCharsToRemove = array('_', '-');

    protected $lang;

    // ** ------------------------- Validation Helpers ---------------------------- ** //

    /**
     * GUMP constructor.
     * @param string $lang
     * @throws \Exception
     */
    public function __construct($lang = 'en') {
        $this->setLanguage($lang);
        $this->filters = new Filters($this);
        $this->validators = new Validators($this);
    }

    /**
     * @param string $lang
     * @throws \Exception
     */
    public function setLanguage(string $lang = 'en'): void {
        if ($lang) {
            $lang_file = __DIR__.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.$lang.'.php';

            if (file_exists($lang_file)) {
                $this->lang = $lang;
            } else {
                throw new \Exception('Language with key "'.$lang.'" does not exist');
            }
        }
    }

    /**
     * Shorthand method for inline validation.
     * @param array $data The data to be validated
     * @param array $validators The GUMP validators
     * @return mixed True(boolean) or the array of error messages
     * @throws \Exception
     */
    public static function is_valid(array $data, array $validators) {
        $gump = self::getInstance();
        $gump->validation_rules($validators);
        if ($gump->run($data) === false) {
            return $gump->get_readable_errors(false);
        }

        return true;
    }

    /**
     * Shorthand method for running only the data filters.
     * @param array $data
     * @param array $filters
     * @return mixed
     * @throws \Exception
     */
    public static function filter_input(array $data, array $filters) {
        return self::getInstance()->filter($data, $filters);
    }

    /**
     * Magic method to generate the validation error messages.
     * @return string
     * @throws \Exception
     */
    public function __toString(): string {
        return strval($this->get_readable_errors(true));
    }

    /**
     * Perform XSS clean to prevent cross site scripting.
     * @static
     * @param array $data
     * @return array
     */
    public static function xss_clean(array $data): array {
        foreach ($data as $k => $v) {
            $data[$k] = filter_var($v, FILTER_SANITIZE_STRING);
        }

        return $data;
    }

    /**
     * Adds a custom validation rule using a callback function.
     * @param string   $rule
     * @param callable $callback
     * @param string   $error_message
     * @return bool
     * @throws Exception
     */
    public static function add_validator(string $rule,callable $callback,string $error_message = null): bool {
        $method = 'validate_'.$rule;

        if (method_exists(__CLASS__, $method) || isset(self::$validation_methods[$rule])) {
            throw new Exception("Validator rule '$rule' already exists.");
        }

        self::$validation_methods[$rule] = $callback;
        if ($error_message) {
            self::$validation_methods_errors[$rule] = $error_message;
        }

        return true;
    }

    /**
     * Adds a custom filter using a callback function.
     * @param string   $rule
     * @param callable $callback
     * @return bool
     * @throws Exception
     */
    public static function add_filter(string $rule,callable $callback): bool {
        $method = 'filter_'.$rule;

        if (method_exists(__CLASS__, $method) || isset(self::$filter_methods[$rule])) {
            throw new Exception("Filter rule '$rule' already exists.");
        }

        self::$filter_methods[$rule] = $callback;
        return true;
    }

    /**
     * Helper method to extract an element from an array safely
     * @param mixed $key
     * @param array $array
     * @param mixed $default
     * @return mixed
     */
    public static function field($key, array $array, $default = null) {
        if(!is_array($array)) {
            return null;
        }

        if(isset($array[$key])) {
            return $array[$key];
        }

        return $default;
    }

    /**
     * Getter/Setter for the validation rules.
     * @param array $rules
     * @return array
     */
    public function validation_rules(array $rules = []): array {
        if (empty($rules)) {
            return $this->validation_rules;
        }

        $this->validation_rules = $rules;
        return [];
    }

    /**
     * Getter/Setter for the filter rules.
     * @param array $rules
     * @return array
     */
    public function filter_rules(array $rules = []): array {
        if (empty($rules)) {
            return $this->filter_rules;
        }

        $this->filter_rules = $rules;
        return [];
    }

    /**
     * Run the filtering and validation after each other.
     * @param array $data
     * @param bool  $check_fields
     * @param string $rules_delimiter
     * @param string $parameters_delimiters
     * @return array
     * @throws Exception
     */
    public function run(array $data,bool $check_fields = false,string $rules_delimiter='|',string $parameters_delimiters=','): array {
        $data = $this->filter($data, $this->filter_rules(), $rules_delimiter, $parameters_delimiters);

        $validated = $this->validate(
            $data, $this->validation_rules(),
            $rules_delimiter, $parameters_delimiters);

        if ($check_fields === true) {
            $this->check_fields($data);
        }

        if ($validated !== true) {
            return false;
        }

        return $data;
    }

    /**
     * Ensure that the field counts match the validation rule counts.
     * @param array $data
     */
    private function check_fields(array $data): void {
        $ruleset = $this->validation_rules();
        $mismatch = array_diff_key($data, $ruleset);
        $fields = array_keys($mismatch);

        foreach ($fields as $field) {
            $this->errors[] = [
                'field' => $field,
                'value' => $data[$field],
                'rule' => 'mismatch',
                'param' => null];
        }
    }

    /**
     * Sanitize the input data.
     * @param array $input
     * @param array $fields
     * @param bool $utf8_encode
     * @return array
     */
    public function sanitize(array $input, array $fields = [],bool $utf8_encode = true): array {
        $magic_quotes = (bool)get_magic_quotes_gpc();

        if (empty($fields)) {
            $fields = array_keys($input);
        }

        $return = [];
        foreach ($fields as $field) {
            if (!isset($input[$field])) {
                continue;
            } else {
                $value = $input[$field];
                if (is_array($value)) {
                    $value = $this->sanitize($value, array(), $utf8_encode);
                }
                if (is_string($value)) {
                    if ($magic_quotes === true) {
                        $value = stripslashes($value);
                    }

                    if (strpos($value, "\r") !== false) {
                        $value = trim($value);
                    }

                    if (function_exists('iconv') && function_exists('mb_detect_encoding') && $utf8_encode) {
                        $current_encoding = mb_detect_encoding($value);

                        if ($current_encoding != 'UTF-8' && $current_encoding != 'UTF-16') {
                            $value = iconv($current_encoding, 'UTF-8', $value);
                        }
                    }

                    $value = $this->filters->filter_basic_tags($value);
                }

                $return[$field] = $value;
            }
        }

        return $return;
    }

    /**
     * Return the error array from the last validation run.
     * @return array
     */
    public function errors(): array {
        return $this->errors;
    }

    /**
     * Perform data validation against the provided ruleset.
     * If any rule's parameter contains either '|' or ',', the corresponding default separator can be changed
     * @param mixed $input
     * @param array $ruleset
     * @param string $rules_delimiter
     * @param string $parameters_delimiter
     * @return mixed
     * @throws Exception
     */
    public function validate(array $input, array $ruleset,string $rules_delimiter='|',string $parameters_delimiter=',') {
        $this->errors = [];

        foreach ($ruleset as $field => $rules) {
            $rules = explode($rules_delimiter, $rules);
            $look_for = ['required_file', 'required'];

            if (count(array_intersect($look_for, $rules)) > 0 || (isset($input[$field]))) {
                if (isset($input[$field])) {
                    if (is_array($input[$field]) && in_array('required_file', $ruleset)) {
                        $input_array = $input[$field];
                    } else {
                        $input_array = [$input[$field]];
                    }
                } else {
                    $input_array = [''];
                }

                foreach ($input_array as $value) {
                    $input[$field] = $value;
                    foreach ($rules as $rule) {
                        $method = null;
                        $param = null;

                        // Check if we have rule parameters
                        if (strstr($rule, $parameters_delimiter) !== false) {
                            $rules  = explode(',', $rule, 2);
                            $rule   = array_shift($rules);
                            $param  = implode(',', $rules);
                            $method = 'validate_'.$rule;

                            // If there is a reference to a field
                            if (preg_match('/(?:(?:^|;)_([a-z_]+))/', $param, $matches)) {

                                // If provided parameter is a field
                                if (isset($input[$matches[1]])) {
                                    $param = str_replace('_'.$matches[1], $input[$matches[1]], $param);
                                }
                            }
                        } else {
                            $method = 'validate_'.$rule;
                        }

                        if (is_callable(array($this, $method))) { $result = $this->validators->$method($field, $input, $param);
                            if (is_array($result)) {
                                if (array_search($result['field'], array_column($this->errors, 'field')) === false) {
                                    $this->errors[] = $result;
                                }
                            }
                        } elseif(isset(self::$validation_methods[$rule])) {
                            $result = call_user_func(self::$validation_methods[$rule], $field, $input, $param);
                            if($result === false) {
                                if (array_search($result['field'], array_column($this->errors, 'field')) === false) {
                                    $this->errors[] = ['field' => $field, 'value' => $input[$field], 'rule' => $rule, 'param' => $param];
                                }
                            }
                        } else {
                            throw new Exception("Validator method '$method' does not exist.");
                        }
                    }
                }
            }
        }

        return (count($this->errors) > 0) ? $this->errors : true;
    }

    /**
     * Set a readable name for a specified field names.
     * @param string $field
     * @param string $readable_name
     */
    public static function set_field_name(string $field,string $readable_name): void {
        self::$fields[$field] = $readable_name;
    }

    /**
     * Set readable name for specified fields in an array.
     * Usage:
     * GUMP::set_field_names(array(
     *  "name" => "My Lovely Name",
     *  "username" => "My Beloved Username",
     * ));
     *
     * @param array $array
     */
    public static function set_field_names(array $array): void {
        foreach ($array as $field => $readable_name) {
            self::set_field_name($field, $readable_name);
        }
    }

    /**
     * Set a custom error message for a validation rule.
     * @param string $rule
     * @param string $message
     * @throws \Exception
     */
    public static function set_error_message(string $rule,string $message): void {
        self::$validation_methods_errors[$rule] = $message;
    }

    /**
     * Set custom error messages for validation rules in an array.
     * Usage:
     * GUMP::set_error_messages(array(
     *  "validate_required"     => "{field} is required",
     *  "validate_valid_email"  => "{field} must be a valid email",
     * ));
     *
     * @param array $array
     * @throws \Exception
     */
    public static function set_error_messages(array $array): void {
        foreach ($array as $rule => $message) {
            self::set_error_message($rule, $message);
        }
    }

    /**
     * Get error messages.
     *
     * @return array
     */
    protected function get_messages(): array {
        $lang_file = __DIR__.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.$this->lang.'.php';
        $messages = require $lang_file;

        if ($validation_methods_errors = self::$validation_methods_errors) {
            $messages = array_merge($messages, $validation_methods_errors);
        }

        return $messages;
    }

    /**
     * Process the validation errors and return human readable error messages.
     * @param bool $convert_to_string = false
     * @param string $field_class
     * @param string $error_class
     * @return array
     * @throws \Exception
     */
    public function get_readable_errors(bool $convert_to_string = false,string $field_class = 'gump-field',string $error_class = 'gump-error-message'): array {
        if (empty($this->errors)) {
            return ($convert_to_string) ? null : [];
        }

        $resp = [];

        // Error messages
        $messages = $this->get_messages();

        foreach ($this->errors as $e) {
            $field = ucwords(str_replace($this->fieldCharsToRemove, chr(32), $e['field']));
            $param = $e['param'];

            // Let's fetch explicitly if the field names exist
            if (array_key_exists($e['field'], self::$fields)) {
                $field = self::$fields[$e['field']];

                // If param is a field (i.e. equalsfield validator)
                if (array_key_exists($param, self::$fields)) {
                    $param = self::$fields[$e['param']];
                }
            }

            // Messages
            if (isset($messages[$e['rule']])) {
                if (is_array($param)) {
                    $param = implode(', ', $param);
                }
                $message = str_replace('{param}', $param, str_replace('{field}', '<span class="'.$field_class.'">'.$field.'</span>', $messages[$e['rule']]));
                $resp[] = $message;
            } else {
                throw new \Exception ('Rule "'.$e['rule'].'" does not have an error message');
            }
        }

        if (!$convert_to_string) {
            return $resp;
        } else {
            $buffer = '';
            foreach ($resp as $s) {
                $buffer .= "<span class=\"$error_class\">$s</span>";
            }
            return $buffer;
        }
    }

    /**
     * Process the validation errors and return an array of errors with field names as keys.
     * @param $convert_to_string
     * @return array | null (if empty)
     * @throws \Exception
     */
    public function get_errors_array($convert_to_string = null) {
        if (empty($this->errors))
            return ($convert_to_string) ? null : [];

        $resp = [];

        // Error messages
        $messages = $this->get_messages();

        foreach ($this->errors as $e) {
            $field = ucwords(str_replace(['_', '-'], chr(32), $e['field']));
            $param = $e['param'];

            // Let's fetch explicitly if the field names exist
            if (array_key_exists($e['field'], self::$fields)) {
                $field = self::$fields[$e['field']];

                // If param is a field (i.e. equalsfield validator)
                if (array_key_exists($param, self::$fields)) {
                    $param = self::$fields[$e['param']];
                }
            }

            // Messages
            if (isset($messages[$e['rule']])) {
                // Show first validation error and don't allow to be overwritten
                if (!isset($resp[$e['field']])) {
                    if (is_array($param)) {
                        $param = implode(', ', $param);
                    }
                    $message = str_replace('{param}', $param, str_replace('{field}', $field, $messages[$e['rule']]));
                    $resp[$e['field']] = $message;
                }
            } else {
                throw new \Exception ('Rule "'.$e['rule'].'" does not have an error message');
            }
        }

        return $resp;
    }

    /**
     * Filter the input data according to the specified filter set.
     * If any filter's parameter contains either '|' or ',', the corresponding default separator can be changed
     * @param mixed $input
     * @param array $filterset
     * @param string $filters_delimeter
     * @param string $parameters_delimiter
     * @throws Exception
     * @return mixed
     * @throws Exception
     */
    public function filter(array $input, array $filterset,string $filters_delimeter='|',string $parameters_delimiter=',') {
        foreach ($filterset as $field => $filters) {
            if (!array_key_exists($field, $input)) {
                continue;
            }

            $filters = explode($filters_delimeter, $filters);
            foreach ($filters as $filter) {
                $params = null;

                if (strstr($filter, $parameters_delimiter) !== false) {
                    $filter = explode($parameters_delimiter, $filter);
                    $params = array_slice($filter, 1, count($filter) - 1);
                    $filter = $filter[0];
                }

                if (is_array($input[$field])) {
                    $input_array = &$input[$field];
                } else {
                    $input_array = [&$input[$field]];
                }

                foreach ($input_array as &$value) {
                    if (is_callable([$this->filters, 'filter_'.$filter])) {
                        $method = 'filter_'.$filter;
                        $value = $this->filters->$method($value, $params);
                    } elseif (function_exists($filter)) {
                        $value = $filter($value);
                    } elseif (isset(self::$filter_methods[$filter])) {
                        $value = call_user_func(self::$filter_methods[$filter], $value, $params);
                    } else {
                        throw new Exception("Filter method '$filter' does not exist.");
                    }
                }
            }
        }

        return $input;
    }

    /**
     * @param string $url
     * @param int $timeout
     * @return string
     */
    public function getExternalContents(string $url, int $timeout = 15): ?string {
        if(function_exists('curl_init')) {
            $handle = curl_init();
            curl_setopt($handle, CURLOPT_URL, $url);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_HEADER, true);
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($handle, CURLOPT_TIMEOUT, $timeout);
            $output = curl_exec($handle);
            $responseCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
            curl_close($handle);

            if ($responseCode == "200")
                return strval($output);
        } else {
            $arrContextOptions= [
                "ssl"=> ["verify_peer"=>false, "verify_peer_name"=>false], "timeout" => $timeout,
                "crypto_method" => STREAM_CRYPTO_METHOD_TLS_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT];
            $output = file_get_contents($url,false, stream_context_create($arrContextOptions));
            if (!empty($output))
                return strval($output);
        }

        return null;
    }
}