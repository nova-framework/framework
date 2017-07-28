<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'          => __('The :attribute must be accepted.'),
    'activeUrl'         => __('The :attribute is not a valid URL.'),
    'after'             => __('The :attribute must be a date after :date.'),
    'alpha'             => __('The :attribute may only contain letters.'),
    'alphaDash'         => __('The :attribute may only contain letters, numbers, and dashes.'),
    'alphaNum'          => __('The :attribute may only contain letters and numbers.'),
    'array'             => __('The :attribute must be an array.'),
    'before'            => __('The :attribute must be a date before :date.'),

    'between' => array(
        'numeric'       => __('The :attribute must be between :min and :max.'),
        'file'          => __('The :attribute must be between :min and :max kilobytes.'),
        'string'        => __('The :attribute must be between :min and :max characters.'),
        'array'         => __('The :attribute must have between :min and :max items.'),
    ),

    'boolean'           => __('The :attribute field must be true or false'),
    'confirmed'         => __('The :attribute confirmation does not match.'),
    'date'              => __('The :attribute is not a valid date.'),
    'dateFormat'        => __('The :attribute does not match the format :format.'),
    'different'         => __('The :attribute and :other must be different.'),
    'digits'            => __('The :attribute must be :digits digits.'),
    'digitsBetween'     => __('The :attribute must be between :min and :max digits.'),
    'email'             => __('The :attribute format is invalid.'),
    'exists'            => __('The selected :attribute is invalid.'),
    'image'             => __('The :attribute must be an image.'),
    'in'                => __('The selected :attribute is invalid.'),
    'integer'           => __('The :attribute must be an integer.'),
    'ip'                => __('The :attribute must be a valid IP address.'),

    'max' => array(
        'numeric'       => __('The :attribute may not be greater than :max.'),
        'file'          => __('The :attribute may not be greater than :max kilobytes.'),
        'string'        => __('The :attribute may not be greater than :max characters.'),
        'array'         => __('The :attribute may not have more than :max items.'),
    ),

    'mimes'             => __('The :attribute must be a file of type: :values.'),
    'min' => array(
        'numeric'       => __('The :attribute must be at least :min.'),
        'file'          => __('The :attribute must be at least :min kilobytes.'),
        'string'        => __('The :attribute must be at least :min characters.'),
        'array'         => __('The :attribute must have at least :min items.'),
    ),

    'not_in'            => __('The selected :attribute is invalid.'),
    'numeric'           => __('The :attribute must be a number.'),
    'regex'             => __('The :attribute format is invalid.'),
    'required'          => __('The :attribute field is required.'),
    'requiredIf'        => __('The :attribute field is required when :other is :value.'),
    'requiredWith'      => __('The :attribute field is required when :values is present.'),
    'requiredWithout'   => __('The :attribute field is required when :values is not present.'),
    'same'              => __('The :attribute and :other must match.'),

    'size' => array(
        'numeric'       => __('The :attribute must be :size.'),
        'file'          => __('The :attribute must be :size kilobytes.'),
        'string'        => __('The :attribute must be :size characters.'),
        'array'         => __('The :attribute must contain :size items.'),
    ),

    'unique'            => __('The :attribute has already been taken.'),
    'url'               => __('The :attribute format is invalid.'),

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => array(
    ),

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => array(
    ),

);
