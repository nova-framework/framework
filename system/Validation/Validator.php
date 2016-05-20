<?php
/**
 * Validator - A Class for data validation.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Validation;

use Helpers\Inflector;
use Support\MessageBag;
use Validation\Translator;
use Validation\DatabasePresenceVerifier;

use Closure;
use DateTime;


class Validator
{
    /**
     * The Translator instance.
     *
     * @var \Translation\Translator
     */
    protected $translator;

    /**
     * The Database Presence Verifier implementation.
     *
     * @var \Validation\DatabasePresenceVerifier
     */
    protected $presenceVerifier;

    /**
     * The failed validation rules.
     *
     * @var array
     */
    protected $failedRules = array();

    /**
     * The message bag instance.
     *
     * @var \Support\MessageBag
     */
    protected $messages;

    /**
     * The data under validation.
     *
     * @var array
     */
    protected $data;

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    protected $rules;

    /**
     * The array of custom error messages.
     *
     * @var array
     */
    protected $customMessages = array();

    /**
     * The array of fallback error messages.
     *
     * @var array
     */
    protected $fallbackMessages = array();

    /**
     * The array of custom attribute names.
     *
     * @var array
     */
    protected $customAttributes = array();

    /**
     * All of the custom validator extensions.
     *
     * @var array
     */
    protected $extensions = array();

    /**
     * All of the custom replacer extensions.
     *
     * @var array
     */
    protected $replacers = array();

    /**
     * The size related validation rules.
     *
     * @var array
     */
    protected $sizeRules = array('Size', 'Between', 'Min', 'Max');

    /**
     * The numeric related validation rules.
     *
     * @var array
     */
    protected $numericRules = array('Numeric', 'Integer');

    /**
     * The validation rules that imply the field is required.
     *
     * @var array
     */
    protected $implicitRules = array(
        'Required', 'RequiredWith', 'RequiredWithAll', 'RequiredWithout', 'RequiredWithoutAll', 'RequiredIf', 'Accepted'
    );

    /**
     * Create a new Validator instance.
     *
     * @param  \Translation\Translator  $translator
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array $customAttributes
     * @return void
     */
    public function __construct(Translator $translator, $data, $rules, $messages = array(), $customAttributes = array())
    {
        $this->translator = $translator;

        $this->customMessages = $messages;

        $this->data = $data;

        $this->rules = $this->explodeRules($rules);

        $this->customAttributes = $customAttributes;
    }

    /**
     * Explode the rules into an array of rules.
     *
     * @param  string|array  $rules
     * @return array
     */
    protected function explodeRules($rules)
    {
        foreach ($rules as $key => &$rule) {
            $rule = (is_string($rule)) ? explode('|', $rule) : $rule;
        }

        return $rules;
    }

    /**
     * Add conditions to a given field based on a Closure.
     *
     * @param  string  $attribute
     * @param  string|array  $rules
     * @param  callable  $callback
     * @return void
     */
    public function sometimes($attribute, $rules, $callback)
    {
        $payload = $this->data;

        if (call_user_func($callback, $payload)) {
            foreach ((array) $attribute as $key) {
                $this->mergeRules($key, $rules);
            }
        }
    }

    /**
     * Merge additional rules into a given attribute.
     *
     * @param  string  $attribute
     * @param  string|array  $rules
     * @return void
     */
    public function mergeRules($attribute, $rules)
    {
        $current = array_get($this->rules, $attribute, array());

        $merge = head($this->explodeRules(array($rules)));

        $this->rules[$attribute] = array_merge($current, $merge);
    }

    /**
     * Determine if the data passes the Validation rules.
     *
     * @return bool
     */
    public function passes()
    {
        $this->messages = new MessageBag();

        foreach ($this->rules as $attribute => $rules) {
            foreach ($rules as $rule) {
                $this->validate($attribute, $rule);
            }
        }

        return (count($this->messages->all()) === 0);
    }

    /**
     * Determine if the data fails the Validation rules.
     *
     * @return bool
     */
    public function fails()
    {
        return ! $this->passes();
    }

    /**
     * Validate a given attribute against a rule.
     *
     * @param  string  $attribute
     * @param  string  $rule
     * @return void
     */
    protected function validate($attribute, $rule)
    {
        if (trim($rule) == '') return;

        list($rule, $parameters) = $this->parseRule($rule);

        $value = $this->getValue($attribute);

        $validatable = $this->isValidatable($rule, $attribute, $value);

        $method = "validate{$rule}";

        if ($validatable && ! $this->$method($attribute, $value, $parameters, $this))
        {
            $this->addFailure($attribute, $rule, $parameters);
        }
    }

    /**
     * Get the value of a given attribute.
     *
     * @param  string  $attribute
     * @return mixed
     */
    protected function getValue($attribute)
    {
        if (! is_null($value = array_get($this->data, $attribute))) {
            return $value;
        }
    }

    /**
     * Determine if the attribute is validatable.
     *
     * @param  string  $rule
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function isValidatable($rule, $attribute, $value)
    {
        return $this->presentOrRuleIsImplicit($rule, $attribute, $value) &&
               $this->passesOptionalCheck($attribute);
    }

    /**
     * Determine if the field is present, or the rule implies required.
     *
     * @param  string  $rule
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function presentOrRuleIsImplicit($rule, $attribute, $value)
    {
        return $this->validateRequired($attribute, $value) || $this->isImplicit($rule);
    }

    /**
     * Determine if the attribute passes any optional check.
     *
     * @param  string  $attribute
     * @return bool
     */
    protected function passesOptionalCheck($attribute)
    {
        if ($this->hasRule($attribute, array('Sometimes'))) {
            return array_key_exists($attribute, $this->data);
        } else {
            return true;
        }
    }

    /**
     * Determine if a given rule implies the attribute is required.
     *
     * @param  string  $rule
     * @return bool
     */
    protected function isImplicit($rule)
    {
        return in_array($rule, $this->implicitRules);
    }

    /**
     * Add a failed rule and error message to the collection.
     *
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return void
     */
    protected function addFailure($attribute, $rule, $parameters)
    {
        $this->addError($attribute, $rule, $parameters);

        $this->failedRules[$attribute][$rule] = $parameters;
    }

    /**
     * Add an error message to the validator's collection of messages.
     *
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return void
     */
    protected function addError($attribute, $rule, $parameters)
    {
        $message = $this->getMessage($attribute, $rule);

        $message = $this->doReplacements($message, $attribute, $rule, $parameters);

        $this->messages->add($attribute, $message);
    }

    /**
     * "Validate" optional attributes.
     *
     * Always returns true, just lets us put sometimes in rules.
     *
     * @return bool
     */
    protected function validateSometimes()
    {
        return true;
    }

    /**
     * Validate that a required attribute exists.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validateRequired($attribute, $value)
    {
        if (is_null($value)) {
            return false;
        } elseif (is_string($value) && (trim($value) === '')) {
            return false;
        }

        return true;
    }

    /**
     * Validate the given attribute is filled if it is present.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validateFilled($attribute, $value)
    {
        if (array_key_exists($attribute, $this->data)) {
            return $this->validateRequired($attribute, $value);
        } else {
            return true;
        }
    }

    /**
     * Determine if any of the given attributes fail the required test.
     *
     * @param  array  $attributes
     * @return bool
     */
    protected function anyFailingRequired(array $attributes)
    {
        foreach ($attributes as $key) {
            if ( ! $this->validateRequired($key, $this->getValue($key))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if all of the given attributes fail the required test.
     *
     * @param  array  $attributes
     * @return bool
     */
    protected function allFailingRequired(array $attributes)
    {
        foreach ($attributes as $key) {
            if ($this->validateRequired($key, $this->getValue($key))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate that an attribute exists when any other attribute exists.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  mixed   $parameters
     * @return bool
     */
    protected function validateRequiredWith($attribute, $value, $parameters)
    {
        if ( ! $this->allFailingRequired($parameters)) {
            return $this->validateRequired($attribute, $value);
        }

        return true;
    }

    /**
     * Validate that an attribute exists when all other attributes exists.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  mixed   $parameters
     * @return bool
     */
    protected function validateRequiredWithAll($attribute, $value, $parameters)
    {
        if ( ! $this->anyFailingRequired($parameters)) {
            return $this->validateRequired($attribute, $value);
        }

        return true;
    }

    /**
     * Validate that an attribute exists when another attribute does not.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  mixed   $parameters
     * @return bool
     */
    protected function validateRequiredWithout($attribute, $value, $parameters)
    {
        if ($this->anyFailingRequired($parameters)) {
            return $this->validateRequired($attribute, $value);
        }

        return true;
    }

    /**
     * Validate that an attribute exists when all other attributes do not.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  mixed   $parameters
     * @return bool
     */
    protected function validateRequiredWithoutAll($attribute, $value, $parameters)
    {
        if ($this->allFailingRequired($parameters)) {
            return $this->validateRequired($attribute, $value);
        }

        return true;
    }

    /**
     * Validate that an attribute exists when another attribute has a given value.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  mixed   $parameters
     * @return bool
     */
    protected function validateRequiredIf($attribute, $value, $parameters)
    {
        $this->requireParameterCount(2, $parameters, 'required_if');

        if ($parameters[1] == array_get($this->data, $parameters[0])) {
            return $this->validateRequired($attribute, $value);
        }

        return true;
    }

    /**
     * Get the number of attributes in a list that are present.
     *
     * @param  array  $attributes
     * @return int
     */
    protected function getPresentCount($attributes)
    {
        $count = 0;

        foreach ($attributes as $key) {
            if (array_get($this->data, $key)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Validate that an attribute has a matching confirmation.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validateConfirmed($attribute, $value)
    {
        return $this->validateSame($attribute, $value, array($attribute.'_confirmation'));
    }

    /**
     * Validate that two attributes match.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validateSame($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'same');

        $other = array_get($this->data, $parameters[0]);

        return (isset($other) && $value == $other);
    }

    /**
     * Validate that an attribute is different from another attribute.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validateDifferent($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'different');

        $other = $parameters[0];

        return isset($this->data[$other]) && $value != $this->data[$other];
    }

    /**
     * Validate that an attribute was "accepted".
     *
     * This validation rule implies the attribute is "required".
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validateAccepted($attribute, $value)
    {
        $acceptable = array('yes', 'on', '1', 1, true, 'true');

        return ($this->validateRequired($attribute, $value) && in_array($value, $acceptable, true));
    }

    /**
     * Validate that an attribute is an array.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validateArray($attribute, $value)
    {
        return is_array($value);
    }

    /**
     * Validate that an attribute is numeric.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validateNumeric($attribute, $value)
    {
        return is_numeric($value);
    }

    /**
     * Validate that an attribute is an integer.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validateInteger($attribute, $value)
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Validate that an attribute has a given number of digits.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validateDigits($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'digits');

        return $this->validateNumeric($attribute, $value)
            && strlen((string) $value) == $parameters[0];
    }

    /**
     * Validate that an attribute is between a given number of digits.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validateDigitsBetween($attribute, $value, $parameters)
    {
        $this->requireParameterCount(2, $parameters, 'digits_between');

        $length = strlen((string) $value);

        return ($length >= $parameters[0]) && ($length <= $parameters[1]);
    }

    /**
     * Validate the size of an attribute.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validateSize($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'size');

        return ($this->getSize($attribute, $value) == $parameters[0]);
    }

    /**
     * Validate the size of an attribute is between a set of values.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validateBetween($attribute, $value, $parameters)
    {
        $this->requireParameterCount(2, $parameters, 'between');

        $size = $this->getSize($attribute, $value);

        return ($size >= $parameters[0]) && ($size <= $parameters[1]);
    }

    /**
     * Validate the size of an attribute is greater than a minimum value.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validateMin($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'min');

        return ($this->getSize($attribute, $value) >= $parameters[0]);
    }

    /**
     * Validate the size of an attribute is less than a maximum value.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validateMax($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'max');

        return ($this->getSize($attribute, $value) <= $parameters[0]);
    }

    /**
     * Get the size of an attribute.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return mixed
     */
    protected function getSize($attribute, $value)
    {
        $hasNumeric = $this->hasRule($attribute, $this->numericRules);

        if (is_numeric($value) && $hasNumeric) {
            return array_get($this->data, $attribute);
        } elseif (is_array($value)) {
            return count($value);
        } else {
            return $this->getStringSize($value);
        }
    }

    /**
     * Get the size of a string.
     *
     * @param  string  $value
     * @return int
     */
    protected function getStringSize($value)
    {
        if (function_exists('mb_strlen')) return mb_strlen($value);

        return strlen($value);
    }

    /**
     * Validate an attribute is contained within a list of values.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validateIn($attribute, $value, $parameters)
    {
        return in_array((string) $value, $parameters);
    }

    /**
     * Validate an attribute is not contained within a list of values.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validateNotIn($attribute, $value, $parameters)
    {
        return ! in_array((string) $value, $parameters);
    }

    /**
     * Validate the uniqueness of an attribute value on a given database table.
     *
     * If a database column is not specified, the attribute will be used.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validateUnique($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'unique');

        $table = $parameters[0];

        $column = isset($parameters[1]) ? $parameters[1] : $attribute;

        list($idColumn, $id) = array(null, null);

        if (isset($parameters[2])) {
            list($idColumn, $id) = $this->getUniqueIds($parameters);

            if (strtolower($id) == 'null') $id = null;
        }

        $verifier = $this->getPresenceVerifier();

        $extra = $this->getUniqueExtra($parameters);

        return $verifier->getCount(

            $table, $column, $value, $id, $idColumn, $extra

        ) == 0;
    }

    /**
     * Get the excluded ID column and value for the unique rule.
     *
     * @param  array  $parameters
     * @return array
     */
    protected function getUniqueIds($parameters)
    {
        $idColumn = isset($parameters[3]) ? $parameters[3] : 'id';

        return array($idColumn, $parameters[2]);
    }

    /**
     * Get the extra conditions for a unique rule.
     *
     * @param  array  $parameters
     * @return array
     */
    protected function getUniqueExtra($parameters)
    {
        if (isset($parameters[4])) {
            return $this->getExtraConditions(array_slice($parameters, 4));
        } else {
            return array();
        }
    }

    /**
     * Validate the existence of an attribute value in a database table.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validateExists($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'exists');

        $table = $parameters[0];

        $column = isset($parameters[1]) ? $parameters[1] : $attribute;

        $expected = (is_array($value)) ? count($value) : 1;

        return $this->getExistCount($table, $column, $value, $parameters) >= $expected;
    }

    /**
     * Get the number of records that exist in storage.
     *
     * @param  string  $table
     * @param  string  $column
     * @param  mixed   $value
     * @param  array   $parameters
     * @return int
     */
    protected function getExistCount($table, $column, $value, $parameters)
    {
        $verifier = $this->getPresenceVerifier();

        $extra = $this->getExtraExistConditions($parameters);

        if (is_array($value)) {
            return $this->getMultiCount($table, $column, $value, $extra);
        } else {
            return $verifier->getCount($table, $column, $value, null, null, $extra);
        }
    }

    /**
     * Get the extra exist conditions.
     *
     * @param  array  $parameters
     * @return array
     */
    protected function getExtraExistConditions(array $parameters)
    {
        return $this->getExtraConditions(array_values(array_slice($parameters, 2)));
    }

    /**
     * Get the extra conditions for a unique / exists rule.
     *
     * @param  array  $segments
     * @return array
     */
    protected function getExtraConditions(array $segments)
    {
        $extra = array();

        $count = count($segments);

        for ($i = 0; $i < $count; $i = $i + 2) {
            $extra[$segments[$i]] = $segments[$i + 1];
        }

        return $extra;
    }

    /**
     * Validate that an attribute is a valid IP.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validateIp($attribute, $value)
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Validate that an attribute is a valid e-mail address.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validateEmail($attribute, $value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate that an attribute is a valid URL.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validateUrl($attribute, $value)
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validate that an attribute is an active URL.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validateActiveUrl($attribute, $value)
    {
        $url = str_replace(array('http://', 'https://', 'ftp://'), '', strtolower($value));

        return checkdnsrr($url);
    }

    /**
     * Validate the MIME type of a file is an image MIME type.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validateImage($attribute, $value)
    {
        return $this->validateMimes($attribute, $value, array('jpeg', 'png', 'gif', 'bmp'));
    }

    /**
     * Validate that an attribute contains only alphabetic characters.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validateAlpha($attribute, $value)
    {
        return preg_match('/^\pL+$/u', $value);
    }

    /**
     * Validate that an attribute contains only alpha-numeric characters.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validateAlphaNum($attribute, $value)
    {
        return preg_match('/^[\pL\pN]+$/u', $value);
    }

    /**
     * Validate that an attribute contains only alpha-numeric characters, dashes, and underscores.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validateAlphaDash($attribute, $value)
    {
        return preg_match('/^[\pL\pN_-]+$/u', $value);
    }

    /**
     * Validate that an attribute passes a regular expression check.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validateRegex($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'regex');

        return preg_match($parameters[0], $value);
    }

    /**
     * Validate that an attribute is a valid date.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validateDate($attribute, $value)
    {
        if ($value instanceof DateTime) return true;

        if (strtotime($value) === false) return false;

        $date = date_parse($value);

        return checkdate($date['month'], $date['day'], $date['year']);
    }

    /**
     * Validate that an attribute matches a date format.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validateDateFormat($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'date_format');

        $parsed = date_parse_from_format($parameters[0], $value);

        return $parsed['error_count'] === 0 && $parsed['warning_count'] === 0;
    }

    /**
     * Validate the date is before a given date.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validateBefore($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'before');

        if ( ! ($date = strtotime($parameters[0]))) {
            return strtotime($value) < strtotime($this->getValue($parameters[0]));
        } else {
            return strtotime($value) < $date;
        }
    }

    /**
     * Validate the date is after a given date.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validateAfter($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'after');

        if ( ! ($date = strtotime($parameters[0]))) {
            return strtotime($value) > strtotime($this->getValue($parameters[0]));
        } else {
            return strtotime($value) > $date;
        }
    }

    /**
     * Validate that an attribute is a valid timezone.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validateTimezone($attribute, $value)
    {
        try {
            new \DateTimeZone($value);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation message for an attribute and rule.
     *
     * @param  string  $attribute
     * @param  string  $rule
     * @return string
     */
    protected function getMessage($attribute, $rule)
    {
        $lowerRule = Inflector::tableize($rule);

        $inlineMessage = $this->getInlineMessage($attribute, $lowerRule);

        if ( ! is_null($inlineMessage)) {
            return $inlineMessage;
        }

        $customKey = "validation.custom.{$attribute}.{$lowerRule}";

        $customMessage = $this->translator->trans($customKey);

        if ($customMessage !== $customKey) {
            return $customMessage;
        } elseif (in_array($rule, $this->sizeRules)) {
            return $this->getSizeMessage($attribute, $rule);
        }

        $key = "validation.{$lowerRule}";

        if ($key != ($value = $this->translator->trans($key))) {
            return $value;
        }

        return $this->getInlineMessage(
            $attribute, $lowerRule, $this->fallbackMessages
        ) ?: $key;
    }

    /**
     * Get the inline message for a rule if it exists.
     *
     * @param  string  $attribute
     * @param  string  $lowerRule
     * @param  array   $source
     * @return string
     */
    protected function getInlineMessage($attribute, $lowerRule, $source = null)
    {
        $source = $source ?: $this->customMessages;

        $keys = array("{$attribute}.{$lowerRule}", $lowerRule);

        foreach ($keys as $key) {
            if (isset($source[$key])) return $source[$key];
        }
    }

    /**
     * Get the proper error message for an attribute and size rule.
     *
     * @param  string  $attribute
     * @param  string  $rule
     * @return string
     */
    protected function getSizeMessage($attribute, $rule)
    {
        $lowerRule = Inflector::tableize($rule);

        $type = $this->getAttributeType($attribute);

        $key = "validation.{$lowerRule}.{$type}";

        return $this->translator->trans($key);
    }

    /**
     * Get the data type of the given attribute.
     *
     * @param  string  $attribute
     * @return string
     */
    protected function getAttributeType($attribute)
    {
        if ($this->hasRule($attribute, $this->numericRules)) {
            return 'numeric';
        } elseif ($this->hasRule($attribute, array('Array'))) {
            return 'array';
        }

        return 'string';
    }

    /**
     * Replace all error message place-holders with actual values.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function doReplacements($message, $attribute, $rule, $parameters)
    {
        $message = str_replace(':attribute', $this->getAttribute($attribute), $message);

        $rule = Inflector::tableize($rule);

        if (isset($this->replacers[$rule])) {
            $message = $this->callReplacer($message, $attribute, $rule, $parameters);
        } elseif (method_exists($this, $replacer = "replace{$rule}")) {
            $message = $this->$replacer($message, $attribute, $rule, $parameters);
        }

        return $message;
    }

    /**
     * Transform an array of attributes to their displayable form.
     *
     * @param  array  $values
     * @return array
     */
    protected function getAttributeList(array $values)
    {
        $attributes = array();

        foreach ($values as $key => $value) {
            $attributes[$key] = $this->getAttribute($value);
        }

        return $attributes;
    }

    /**
     * Get the displayable name of the attribute.
     *
     * @param  string  $attribute
     * @return string
     */
    protected function getAttribute($attribute)
    {
        if (isset($this->customAttributes[$attribute])) {
            return $this->customAttributes[$attribute];
        }

        $key = "validation.attributes.{$attribute}";

        if (($line = $this->translator->trans($key)) !== $key) {
            return $line;
        } else {
            return str_replace('_', ' ', $attribute);
        }
    }

    /**
     * Replace all place-holders for the between rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replaceBetween($message, $attribute, $rule, $parameters)
    {
        return str_replace(array(':min', ':max'), $parameters, $message);
    }

    /**
     * Replace all place-holders for the digits rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replaceDigits($message, $attribute, $rule, $parameters)
    {
        return str_replace(':digits', $parameters[0], $message);
    }

    /**
     * Replace all place-holders for the digits (between) rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replaceDigitsBetween($message, $attribute, $rule, $parameters)
    {
        return str_replace(array(':min', ':max'), $parameters, $message);
    }

    /**
     * Replace all place-holders for the size rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replaceSize($message, $attribute, $rule, $parameters)
    {
        return str_replace(':size', $parameters[0], $message);
    }

    /**
     * Replace all place-holders for the min rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replaceMin($message, $attribute, $rule, $parameters)
    {
        return str_replace(':min', $parameters[0], $message);
    }

    /**
     * Replace all place-holders for the max rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replaceMax($message, $attribute, $rule, $parameters)
    {
        return str_replace(':max', $parameters[0], $message);
    }

    /**
     * Replace all place-holders for the in rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replaceIn($message, $attribute, $rule, $parameters)
    {
        return str_replace(':values', implode(', ', $parameters), $message);
    }

    /**
     * Replace all place-holders for the not_in rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replaceNotIn($message, $attribute, $rule, $parameters)
    {
        return str_replace(':values', implode(', ', $parameters), $message);
    }

    /**
     * Replace all place-holders for the mimes rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replaceMimes($message, $attribute, $rule, $parameters)
    {
        return str_replace(':values', implode(', ', $parameters), $message);
    }

    /**
     * Replace all place-holders for the required_with rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replaceRequiredWith($message, $attribute, $rule, $parameters)
    {
        $parameters = $this->getAttributeList($parameters);

        return str_replace(':values', implode(' / ', $parameters), $message);
    }

    /**
     * Replace all place-holders for the required_without rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replaceRequiredWithout($message, $attribute, $rule, $parameters)
    {
        $parameters = $this->getAttributeList($parameters);

        return str_replace(':values', implode(' / ', $parameters), $message);
    }

    /**
     * Replace all place-holders for the required_without_all rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replaceRequiredWithoutAll($message, $attribute, $rule, $parameters)
    {
        $parameters = $this->getAttributeList($parameters);

        return str_replace(':values', implode(' / ', $parameters), $message);
    }

    /**
     * Replace all place-holders for the required_if rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replaceRequiredIf($message, $attribute, $rule, $parameters)
    {
        $parameters[0] = $this->getAttribute($parameters[0]);

        return str_replace(array(':other', ':value'), $parameters, $message);
    }

    /**
     * Replace all place-holders for the same rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replaceSame($message, $attribute, $rule, $parameters)
    {
        return str_replace(':other', $this->getAttribute($parameters[0]), $message);
    }

    /**
     * Replace all place-holders for the different rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replaceDifferent($message, $attribute, $rule, $parameters)
    {
        return str_replace(':other', $this->getAttribute($parameters[0]), $message);
    }

    /**
     * Replace all place-holders for the date_format rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replaceDateFormat($message, $attribute, $rule, $parameters)
    {
        return str_replace(':format', $parameters[0], $message);
    }

    /**
     * Replace all place-holders for the before rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replaceBefore($message, $attribute, $rule, $parameters)
    {
        if ( ! ($date = strtotime($parameters[0]))) {
            return str_replace(':date', $this->getAttribute($parameters[0]), $message);
        } else {
            return str_replace(':date', $parameters[0], $message);
        }
    }

    /**
     * Replace all place-holders for the after rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replaceAfter($message, $attribute, $rule, $parameters)
    {
        if ( ! ($date = strtotime($parameters[0]))) {
            return str_replace(':date', $this->getAttribute($parameters[0]), $message);
        } else {
            return str_replace(':date', $parameters[0], $message);
        }
    }

    /**
     * Determine if the given attribute has a rule in the given set.
     *
     * @param  string  $attribute
     * @param  array   $rules
     * @return bool
     */
    protected function hasRule($attribute, $rules)
    {
        $rules = (array) $rules;

        foreach ($this->rules[$attribute] as $rule) {
            list($rule, $parameters) = $this->parseRule($rule);

            if (in_array($rule, $rules)) return true;
        }

        return false;
    }

    /**
     * Extract the rule name and parameters from a rule.
     *
     * @param  string  $rule
     * @return array
     */
    protected function parseRule($rule)
    {
        $parameters = array();

        if (strpos($rule, ':') !== false) {
            list($rule, $parameter) = explode(':', $rule, 2);

            $parameters = $this->parseParameters($rule, $parameter);
        }

        $rule = Inflector::classify($rule);

        return array($rule, $parameters);
    }

    /**
     * Parse a parameter list.
     *
     * @param  string  $rule
     * @param  string  $parameter
     * @return array
     */
    protected function parseParameters($rule, $parameter)
    {
        if (strtolower($rule) == 'regex') return array($parameter);

        return str_getcsv($parameter);
    }

    /**
     * Get the array of custom validator extensions.
     *
     * @return array
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Register an array of custom validator extensions.
     *
     * @param  array  $extensions
     * @return void
     */
    public function addExtensions(array $extensions)
    {
        if ($extensions) {
            $keys = array_map(array('\Helpers\Inflector', 'tableize'), array_keys($extensions));

            $extensions = array_combine($keys, array_values($extensions));
        }

        $this->extensions = array_merge($this->extensions, $extensions);
    }

    /**
     * Register an array of custom implicit validator extensions.
     *
     * @param  array  $extensions
     * @return void
     */
    public function addImplicitExtensions(array $extensions)
    {
        $this->addExtensions($extensions);

        foreach ($extensions as $rule => $extension) {
            $this->implicitRules[] = studly_case($rule);
        }
    }

    /**
     * Register a custom validator extension.
     *
     * @param  string  $rule
     * @param  \Closure|string  $extension
     * @return void
     */
    public function addExtension($rule, $extension)
    {
        $rule = Inflector::tableize($rule);

        $this->extensions[$rule] = $extension;
    }

    /**
     * Register a custom implicit validator extension.
     *
     * @param  string   $rule
     * @param  \Closure|string  $extension
     * @return void
     */
    public function addImplicitExtension($rule, $extension)
    {
        $this->addExtension($rule, $extension);

        $this->implicitRules[] = studly_case($rule);
    }

    /**
     * Get the array of custom validator message replacers.
     *
     * @return array
     */
    public function getReplacers()
    {
        return $this->replacers;
    }

    /**
     * Register an array of custom validator message replacers.
     *
     * @param  array  $replacers
     * @return void
     */
    public function addReplacers(array $replacers)
    {
        if ($replacers) {
            $keys = array_map(array('\Helpers\Inflector', 'tableize'), array_keys($replacers));

            $replacers = array_combine($keys, array_values($replacers));
        }

        $this->replacers = array_merge($this->replacers, $replacers);
    }

    /**
     * Register a custom validator message replacer.
     *
     * @param  string  $rule
     * @param  \Closure|string  $replacer
     * @return void
     */
    public function addReplacer($rule, $replacer)
    {
        $rule = Inflector::tableize($rule);

        $this->replacers[$rule] = $replacer;
    }

    /**
     * Get the data under validation.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the data under validation.
     *
     * @param  array  $data
     * @return void
     */
    public function setData(array $data)
    {
        $this->data = $this->parseData($data);
    }

    /**
     * Get the validation rules.
     *
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Set the validation rules.
     *
     * @param  array  $rules
     * @return \Validation\Validator
     */
    public function setRules(array $rules)
    {
        $this->rules = $this->explodeRules($rules);

        return $this;
    }

    /**
     * Set the custom attributes on the validator.
     *
     * @param  array  $attributes
     * @return \Validation\Validator
     */
    public function setAttributeNames(array $attributes)
    {
        $this->customAttributes = $attributes;

        return $this;
    }

    /**
     * Get the Database Presence Verifier implementation.
     *
     * @return \Validation\DatabasePresenceVerifier
     *
     * @throws \RuntimeException
     */
    public function getPresenceVerifier()
    {
        if ( ! isset($this->presenceVerifier)) {
            throw new \RuntimeException("Presence Verifier has not been set.");
        }

        return $this->presenceVerifier;
    }

    /**
     * Set the Database Presence Verifier implementation.
     *
     * @param  \Validation\DatabasePresenceVerifier  $presenceVerifier
     * @return void
     */
    public function setPresenceVerifier(DatabasePresenceVerifier $presenceVerifier)
    {
        $this->presenceVerifier = $presenceVerifier;
    }

    /**
     * Get the Translator implementation.
     *
     * @return \Translation\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Set the Translator implementation.
     *
     * @param  \Translation\Translator  $translator
     * @return void
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Get the custom messages for the validator
     *
     * @return array
     */
    public function getCustomMessages()
    {
        return $this->customMessages;
    }

    /**
     * Set the custom messages for the validator
     *
     * @param  array  $messages
     * @return void
     */
    public function setCustomMessages(array $messages)
    {
        $this->customMessages = array_merge($this->customMessages, $messages);
    }

    /**
     * Get the fallback messages for the validator.
     *
     * @return array
     */
    public function getFallbackMessages()
    {
        return $this->fallbackMessages;
    }

    /**
     * Set the fallback messages for the validator.
     *
     * @param  array  $messages
     * @return void
     */
    public function setFallbackMessages(array $messages)
    {
        $this->fallbackMessages = $messages;
    }

    /**
     * Get the failed validation rules.
     *
     * @return array
     */
    public function failed()
    {
        return $this->failedRules;
    }

    /**
     * Get the message container for the validator.
     *
     * @return \Support\MessageBag
     */
    public function messages()
    {
        if ( ! $this->messages) $this->passes();

        return $this->messages;
    }

    /**
     * An alternative more semantic shortcut to the message container.
     *
     * @return \Support\MessageBag
     */
    public function errors()
    {
        if ($this->messages === null) $this->passes();

        return $this->messages;
    }

    /**
     * Get the messages for the instance.
     *
     * @return \Support\MessageBag
     */
    public function getMessageBag()
    {
        return $this->messages();
    }

    /**
     * Call a custom validator extension.
     *
     * @param  string  $rule
     * @param  array   $parameters
     * @return bool
     */
    protected function callExtension($rule, $parameters)
    {
        $callback = $this->extensions[$rule];

        if ($callback instanceof Closure) {
            return call_user_func_array($callback, $parameters);
        } elseif (is_string($callback))
        {
            return $this->callClassBasedExtension($callback, $parameters);
        }
    }

    /**
     * Call a class based validator extension.
     *
     * @param  string  $callback
     * @param  array   $parameters
     * @return bool
     */
    protected function callClassBasedExtension($callback, $parameters)
    {
        list($class, $method) = explode('@', $callback);

        return call_user_func_array(array($this->container->make($class), $method), $parameters);
    }

    /**
     * Call a custom validator message replacer.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function callReplacer($message, $attribute, $rule, $parameters)
    {
        $callback = $this->replacers[$rule];

        if ($callback instanceof Closure) {
            return call_user_func_array($callback, func_get_args());
        } elseif (is_string($callback)) {
            return $this->callClassBasedReplacer($callback, $message, $attribute, $rule, $parameters);
        }
    }

    /**
     * Call a class based validator message replacer.
     *
     * @param  string  $callback
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function callClassBasedReplacer($callback, $message, $attribute, $rule, $parameters)
    {
        list($className, $method) = explode('@', $callback);

        $instance = new $className();

        return call_user_func_array(array($instance, $method), array_slice(func_get_args(), 1));
    }

    /**
     * Require a certain number of parameters to be present.
     *
     * @param  int    $count
     * @param  array  $parameters
     * @param  string $rule
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function requireParameterCount($count, $parameters, $rule)
    {
        if (count($parameters) < $count) {
            throw new \InvalidArgumentException("Validation rule $rule requires at least $count parameters.");
        }
    }

    /**
     * Handle dynamic calls to class methods.
     *
     * @param  string  $method
     * @param  array   $params
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $params)
    {
        $rule = Inflector::tableize(substr($method, 8));

        if (isset($this->extensions[$rule])) {
            return $this->callExtension($rule, $params);
        }

        throw new \BadMethodCallException("Method [$method] does not exist.");
    }
}
