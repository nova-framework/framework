<?php
/**
 * Filter
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 04th, 2016
 */

namespace Nova\Input;

use Sirius\Input\InputFilter;
use Sirius\Validation\ValidatorInterface;
use Sirius\Filtration\FiltratorInterface;

use Nova\Input\Element\Factory as ElementFactory;


class Filter extends InputFilter
{

    public function __construct(ElementFactory $elementFactory = null, ValidatorInterface $validator = null, FiltratorInterface $filtrator = null)
    {
        parent::__construct($elementFactory, $validator, $filtrator);
    }

    public function setRules($rules)
    {
        // Check for valid data.
        if (! is_array($rules)) {
            throw new \Exception("Rules parameter must be an Array.");
        }

        // Firstly, clear all Elements.
        $this->elements = array();

        $this->elementsIndex = PHP_INT_MAX;

        // Add the given Elements, one by one.
        foreach($rules as $fieldName => $fieldRules) {
            $this->addElement($fieldName, $fieldRules);
        }
    }

}
