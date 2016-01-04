<?php
/**
 * ORM Annotations - Column
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date January 2nd, 2016
 */

namespace Nova\ORM\Annotation;

use Doctrine\Common\Annotations\Annotation;
use PDO;

/**
 * Class Column
 * @package Nova\ORM\Annotation
 *
 * @Annotation
 * @Annotation\Target("PROPERTY")
 */
class Column extends Annotation
{
    /**
     * @var string
     * @Annotation\Required
     */
    public $name;

    /**
     * @var string
     * @Enum({"string", "int", "float", "double", "text"})
     */
    public $type = "string";

    /**
     * @var string
     */
    public $default;

    /**
     * @var bool
     */
    public $primary = false;

    /**
     * @var bool
     */
    public $null = false;

    /**
     * @var bool
     */
    public $autoIncredimental = false;


    /**
     * Property Field in the Entity class
     *
     * @var string
     */
    private $propertyField = "";

    /**
     * Get property field in Entity Class
     *
     * @return string
     */
    public function getPropertyField()
    {
        return $this->propertyField;
    }

    /**
     * Set property field name in Entity class
     *
     * @param string $propertyField
     */
    public function setPropertyField($propertyField)
    {
        $this->propertyField = $propertyField;
    }

    /**
     * Get PDO Type Integers from our ORM Type enumeration
     *
     * @return int PDO Integer from PDO::PARAM_*
     */
    public function getPdoType()
    {
        switch($this->type){
            case 'string':
                return PDO::PARAM_STR;
            case 'int':
                return PDO::PARAM_INT;
            case 'double' || 'float' || 'text':
                return PDO::PARAM_STR;
            default:
                return PDO::PARAM_STR;
        }
    }
}