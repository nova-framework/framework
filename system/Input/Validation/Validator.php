<?php
/**
 * Validator
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 04th, 2016
 */

namespace Nova\Input\Validation;

use Sirius\Validation\ErrorMessage;
use Sirius\Validation\Validator as BaseValidator;

use Nova\Input\Validation\RuleFactory;


class Validator extends BaseValidator
{
    public function __construct(RuleFactory $ruleFactory = null, ErrorMessage $errorMessagePrototype = null)
    {
        if ($ruleFactory === null) {
            $ruleFactory = new RuleFactory();
        }

        if ($errorMessagePrototype === null) {
            $errorMessagePrototype = new ErrorMessage();
        }

        parent::__construct($ruleFactory, $errorMessagePrototype);
    }
}
