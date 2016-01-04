<?php
/**
 * Helper
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 04th, 2016
 */

namespace Nova\Input\Validation;

use Sirius\Validation\RuleFactory as BaseRuleFactory;


class RuleFactory extends BaseRuleFactory
{
    function __construct()
    {
        parent::__construct();

        $this->setDefaultMessages();
    }

    protected function setDefaultMessages()
    {
        $defaultMessages = array(
            'alpha' => array(
                'default_message' => __d('system', 'This input can contain only letters'),
                'labeled_message' => __d('system', '{label} can contain only letters')
            ),
            'alphanumeric' => array(
                'default_message' => __d('system', 'This input must contain only letters and digits'),
                'labeled_message' => __d('system', '{label} must contain only letters and digits')
            ),
            'alphanumhyphen' => array(
                'default_message' => __d('system', 'This input must contain only letters, digits, spaces, hyphens and underscores'),
                'labeled_message' => __d('system', '{label} must contain only letters, digits, spaces, hyphens and underscores')
            ),
            'arraylength' => array(
                'default_message' => __d('system', 'This input should contain between {min} and {max} items'),
                'labeled_message' => __d('system', '{label} should contain between {min} and {max} items')
            ),
            'arraymaxlength' => array(
                'default_message' => __d('system', 'This input should contain less than {min} items'),
                'labeled_message' => __d('system', '{label} should contain less than {min} items')
            ),
            'arrayminlength' => array(
                'default_message' => __d('system', 'This input should contain at least {min} items'),
                'labeled_message' => __d('system', '{label} should contain at least {min} items')
            ),
            'between' => array(
                'default_message' => __d('system', 'This input must be between {min} and {max}'),
                'labeled_message' => __d('system', '{label} must be between {min} and {max}')
            ),
            'callback' => array(
                'default_message' => __d('system', 'This input does not meet the validation criteria'),
                'labeled_message' => __d('system', '{label} does not meet the validation criteria')
            ),
            'date' => array(
                'default_message' => __d('system', 'This input must be a date having the format {format}'),
                'labeled_message' => __d('system', '{label} must be a date having the format {format}')
            ),
            'datetime' => array(
                'default_message' => __d('system', 'This input must be a date having the format {format}'),
                'labeled_message' => __d('system', '{label} must be a date having the format {format}')
            ),
            'email' => array(
                'default_message' => __d('system', 'This input must be a valid email address'),
                'labeled_message' => __d('system', '{label} must be a valid email address')
            ),
            'emaildomain' => array(
                'default_message' => __d('system', 'This the email address does not belong to a valid domain'),
                'labeled_message' => __d('system', '{label} does not belong to a valid domain')
            ),
            'equal' => array(
                'default_message' => __d('system', 'This input is not equal to {value}'),
                'labeled_message' => __d('system', '{label} is not equal to {value}')
            ),
            'fullname' => array(
                'default_message' => __d('system', 'This input is not a valid full name (first name and last name)'),
                'labeled_message' => __d('system', '{label} is not a valid full name (first name and last name)')
            ),
            'greaterthan' => array(
                'default_message' => __d('system', 'This input should be greater than {min}'),
                'labeled_message' => __d('system', '{label} should be greater than {min}')
            ),
            'inlist' => array(
                'default_message' => __d('system', 'This input is not one of the accepted values'),
                'labeled_message' => __d('system', '{label} is not one of the accepted values')
            ),
            'integer' => array(
                'default_message' => __d('system', 'This input must be an integer number'),
                'labeled_message' => __d('system', '{label} must be an integer number')
            ),
            'ipaddress' => array(
                'default_message' => __d('system', 'This input is not a valid IP address'),
                'labeled_message' => __d('system', '{label} is not a valid IP address')
            ),
            'length' => array(
                'default_message' => __d('system', 'This input must be between {min} and {max} characters long'),
                'labeled_message' => __d('system', '{label} must be between {min} and {max} characters long')
            ),
            'lessthan' => array(
                'default_message' => __d('system', 'This input should be less than {max}'),
                'labeled_message' => __d('system', '{label} should be less than {max}')
            ),
            'match' => array(
                'default_message' => __d('system', 'This input does not match {item}'),
                'labeled_message' => __d('system', '{label} does not match {item}')
            ),
            'maxlength' => array(
                'default_message' => __d('system', 'This input should have less than {max} characters'),
                'labeled_message' => __d('system', '{label} should have less than {max} characters')
            ),
            'minlength' => array(
                'default_message' => __d('system', 'This input should have at least {min} characters'),
                'labeled_message' => __d('system', '{label} should have at least {min} characters')
            ),
            'notinlist' => array(
                'default_message' => __d('system', 'This input is one of the forbidden values'),
                'labeled_message' => __d('system', '{label} is one of the forbidden values')
            ),
            'notregex' => array(
                'default_message' => __d('system', 'This input should not match the regular expression {pattern}'),
                'labeled_message' => __d('system', '{label} Tshould not match the regular expression {pattern}')
            ),
            'number' => array(
                'default_message' => __d('system', 'This input must be a number'),
                'labeled_message' => __d('system', '{label} must be a number')
            ),
            'regex' => array(
                'default_message' => __d('system', 'This input does not match the regular expression {pattern}'),
                'labeled_message' => __d('system', '{label} does not match the regular expression {pattern}')
            ),
            'required' => array(
                'default_message' => __d('system', 'This field is required'),
                'labeled_message' => __d('system', '{label} is required')
            ),
            'requiredwhen' => array(
                'default_message' => __d('system', 'This field is required'),
                'labeled_message' => __d('system', '{label} is required')
            ),
            'requiredwith' => array(
                'default_message' => __d('system', 'This field is required'),
                'labeled_message' => __d('system', '{label} is required')
            ),
            'requiredwithout' => array(
                'default_message' => __d('system', 'This field is required'),
                'labeled_message' => __d('system', '{label} is required')
            ),
            'time' => array(
                'default_message' => __d('system', 'This input must be a time having the format {format}'),
                'labeled_message' => __d('system', '{label} must be a time having the format {format}')
            ),
            'url' => array(
                'default_message' => __d('system', 'This input is not a valid URL'),
                'labeled_message' => __d('system', '{label} is not a valid URL')
            ),
            'website' => array(
                'default_message' => __d('system', 'This input must be a valid website address'),
                'labeled_message' => __d('system', '{label} must be a valid website address')
            ),
            'fileextension' => array(
                'default_message' => __d('system', 'The file does not have an acceptable extension ({file_extensions})'),
                'labeled_message' => __d('system', '{label} does not have an acceptable extension ({file_extensions})')
            ),
            'fileimage' => array(
                'default_message' => __d('system', 'The file is not a valid image (only {image_types} are allowed)'),
                'labeled_message' => __d('system', '{label} is not a valid image (only {image_types} are allowed)')
            ),
            'fileimageheight' => array(
                'default_message' => __d('system', 'The file should be at least {min} pixels tall'),
                'labeled_message' => __d('system', '{label} should be at least {min} pixels tall')
            ),
            'fileimageratio' => array(
                'default_message' => __d('system', 'The image does must have a ratio (width/height) of {ratio})'),
                'labeled_message' => __d('system', '{label} does must have a ratio (width/height) of {ratio})')
            ),
            'fileimagewidth' => array(
                'default_message' => __d('system', 'The image should be at least {min} pixels wide'),
                'labeled_message' => __d('system', '{label} should be at least {min} pixels wide')
            ),
            'filesize' => array(
                'default_message' => __d('system', 'The file should not exceed {size}'),
                'labeled_message' => __d('system', '{label} should not exceed {size}')
            ),
            'uploadextension' => array(
                'default_message' => __d('system', 'The file does not have an acceptable extension ({file_extensions})'),
                'labeled_message' => __d('system', '{label} does not have an acceptable extension ({file_extensions})')
            ),
            'uploadimage' => array(
                'default_message' => __d('system', 'The file is not a valid image (only {image_types} are allowed)'),
                'labeled_message' => __d('system', '{label} is not a valid image (only {image_types} are allowed)')
            ),
            'uploadimageheight' => array(
                'default_message' => __d('system', 'The file should be at least {min} pixels tall'),
                'labeled_message' => __d('system', '{label} should be at least {min} pixels tall')
            ),
            'uploadimageratio' => array(
                'default_message' => __d('system', 'The image does must have a ratio (width/height) of {ratio})'),
                'labeled_message' => __d('system', '{label} does must have a ratio (width/height) of {ratio})')
            ),
            'uploadimagewidth' => array(
                'default_message' => __d('system', 'The image should be at least {min} pixels wide'),
                'labeled_message' => __d('system', '{label} should be at least {min} pixels wide')
            ),
            'uploadsize' => array(
                'default_message' => __d('system', 'The file should not exceed {size}'),
                'labeled_message' => __d('system', '{label} should not exceed {size}')
            ),
        );

        // Refresh the Error Messages using the translated ones.
        foreach($defaultMessages as $label => $row) {
            $this->setErrorMessages($label, $row['default_message'], $row['labeled_message']);
        }
    }
}
