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

    /** @var string */
    public $type;

    /** @var string */
    public $default;
}