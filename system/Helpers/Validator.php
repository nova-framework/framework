<?php
/**
 * Validator - validate and filter the input array.
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 19th, 2015
 */

namespace Nova\Helpers;

use GUMP;


class Validator extends GUMP {

    protected static $messages = array();

    protected $current_data = array();


    public function __construct($data = array())
    {
        // The Error messages
        self::$messages = array(
            'mismatch'                     => __d('system', 'There is no validation rule for :field.'),
            'validate_required'            => __d('system', 'The :field field is required.'),
            'validate_valid_email'         => __d('system', 'The :field field is required to be a valid email address.'),
            'validate_max_len'             => __d('system', 'The :field field needs to be :param or shorter in length.'),
            'validate_min_len'             => __d('system', 'The :field field needs to be :param or longer in length.'),
            'validate_exact_len'           => __d('system', 'The :field field needs to be exactly :param characters in length.'),
            'validate_alpha'               => __d('system', 'The :field field may only contain alpha characters (a-z).'),
            'validate_alpha_numeric'       => __d('system', 'The :field field may only contain alpha-numeric characters.'),
            'validate_alpha_dash'          => __d('system', 'The :field field may only contain alpha characters &amp; dashes.'),
            'validate_numeric'             => __d('system', 'The :field field may only contain numeric characters.'),
            'validate_integer'             => __d('system', 'The :field field may only contain a numeric value.'),
            'validate_boolean'             => __d('system', 'The :field field may only contain a true or false value.'),
            'validate_float'               => __d('system', 'The :field field may only contain a float value.'),
            'validate_valid_url'           => __d('system', 'The :field field is required to be a valid URL.'),
            'validate_url_exists'          => __d('system', 'The :field URL does not exist.'),
            'validate_valid_ip'            => __d('system', 'The :field field needs to contain a valid IP address.'),
            'validate_valid_cc'            => __d('system', 'The :field field needs to contain a valid credit card number.'),
            'validate_valid_name'          => __d('system', 'The :field field needs to contain a valid human name.'),
            'validate_contains'            => __d('system', 'The :field field needs to contain one of these values: :param.'),
            'validate_contains_list'       => __d('system', 'The :field field needs contain a value from its drop down list.'),
            'validate_doesnt_contain_list' => __d('system', 'The :field field contains a value that is not accepted.'),
            'validate_street_address'      => __d('system', 'The :field field needs to be a valid street address.'),
            'validate_date'                => __d('system', 'The :field field needs to be a valid date.'),
            'validate_min_numeric'         => __d('system', 'The :field field needs to be a numeric value, equal to, or higher than :param.'),
            'validate_max_numeric'         => __d('system', 'The :field field needs to be a numeric value, equal to, or lower than :param.'),
            'validate_starts'              => __d('system', 'The :field field needs to start with :param.'),
            'validate_extension'           => __d('system', 'The :field field can have the following extensions :param.'),
            'validate_required_file'       => __d('system', 'The :field field is required.'),
            'validate_equalsfield'         => __d('system', 'The :field field does not equal :param field.'),
            'validate_min_age'             => __d('system', 'The :field field needs to have an age greater than or equal to :param.'),
        );

        //
        if(empty($data)) {
            return;
        }

        $validation_rules = array();
        $filter_rules = array();

        foreach($data as $field => $config) {
            if(array_key_exists('label', $config)) {
                self::$fields[$field] = $config['label'];
            }

            if(array_key_exists('rules', $config)) {
                $validation_rules[$field] = $config['rules'];
            }

            if(array_key_exists('filter', $config)) {
                $filter_rules[$field] = $config['filter'];
            }
        }

        if(! empty($validation_rules)) {
            $this->validation_rules($validation_rules);
        }

        if(! empty($filter_rules)) {
            $this->filter_rules($filter_rules);
        }
    }

    public function run(array $data, $check_fields = false)
    {
        $data = $this->filter($data, $this->filter_rules());

        $validated = $this->validate($data, $this->validation_rules());

        if ($check_fields === true) {
            $this->check_fields($data);
        }

        if ($validated !== true) {
            return false;
        }

        //
        $return = array();

        $fields = array_keys($this->validation_rules());

        foreach($fields as $field) {
            if(! str_starts_with($field, 'confirm_') && array_key_exists($field, $data)) {
                $return[$field] = $data[$field];
            }
        }

        return $return;
    }

    public function sanitize(array $data, $fields = array(), $utf8_encode = true)
    {
        return parent::sanitize($data, ! empty($fields) ? $fields : array_keys($this->validation_rules), $utf8_encode);
    }

    public function filter(array $data, $filter_rules = array())
    {
        $this->current_data = parent::filter($data, ! empty($filter_rules) ? $filter_rules : $this->filter_rules);

        return $this->current_data;
    }

    public function validate(array $input = array(), $ruleset = array())
    {
        $data = parent::validate(
                    !empty($input)   ? $input   : $this->current_data,
                    !empty($ruleset) ? $ruleset : $this->validation_rules
                );

        if(is_array($data)) {
            foreach($data as $index => $error) {
                $rule = $error['rule'];
                $field = $error['field'];

                // Let's fetch explicit field names if they exist
                if (array_key_exists($field, self::$fields)) {
                    $field = self::$fields[$field];
                }
                else {
                    $field = ucwords(str_replace(array('_', '-'), chr(32), $field));
                }

                if(array_key_exists($rule, self::$messages)) {
                    $param = $error['param'];

                    if(is_array($param)) {
                        $param = implode(', ', $param);
                    }
                    else if(is_string($param)) {
                        if(array_key_exists($param, self::$fields)) {
                            $param = self::$fields[$param];
                        }
                    }

                    $message = str_replace(':field', '<strong>'.$field.'</strong>', self::$messages[$rule]);

                    $message = str_replace(':param', '<strong>'.$param.'</strong>', $message);
                }
                else {
                    $message = "The $field field is invalid";
                }

                $data[$index]['message'] = $message;
            }

            $this->errors = $data;
        }

        return $data;
    }

    public function getData($allFields = false)
    {
        if($allFields) {
            return $this->current_data;
        }

        //
        $return = array();

        $fields = array_keys($this->validation_rules());

        foreach($fields as $field) {
            if(! str_starts_with($field, 'confirm_') && array_key_exists($field, $this->current_data)) {
                $return[$field] = $this->current_data[$field];
            }
        }

        return $return;
    }

    public function getErrors()
    {
        if(empty($this->errors)) {
            return null;
        }

        $errors = array();

        foreach($this->errors as $index => $error) {
            $errors[] = $error['message'];
        }

        return '<div>'.implode('</div><div>', $errors).'</div>';
    }

    public function xssClean(array $data)
    {
        $retval = array();

        $fields = array_keys($this->validation_rules);

        foreach($fields as $field) {
            if(array_key_exists($field, $data)) {
                $retval[$field] = filter_var($data[$field], FILTER_SANITIZE_STRING);
            }
        }

        return $retval;
    }

    public function setMessages(array $messages)
    {
        if(!empty($messages)) {
            self::$messages = array_merge(self::$messages, $messages);
        }
    }

    //
    // Override some GUMP methods.

    public function field_names(array $fields)
    {
        foreach ($fields as $field => $readable_name) {
            self::$fields[$field] = $readable_name;
        }
    }

    public function get_readable_errors($convert_to_string = false, $field_class = 'gump-field', $error_class = 'gump-error-message')
    {
        $errors = parent::get_readable_errors(false);

        if (! $convert_to_string) {
            return $errors;
        }

        //
        $buffer = '';

        foreach ($errors as $error) {
            $buffer .= "<div class=\"$error_class\">".$error.".</div>";
        }

        return $buffer;
    }

    //
    // Additional filters and validation methods

    protected function filter_sanitize_url($value, $params = null)
    {
        return filter_var($value, FILTER_SANITIZE_URL);
    }

    protected function validate_equalsfield($field, $input, $param = null)
    {
        if ((!isset($input[$field]) || empty($input[$field])) && (!isset($input[$param]) || empty($input[$param]))) {
            return null;
        }

        if ($input[$field] == $input[$param]) {
          return null;
        }

        return array(
            'field' => $field,
            'value' => $input[$field],
            'rule' => __FUNCTION__,
            'param' => $param,
        );
    }

}
