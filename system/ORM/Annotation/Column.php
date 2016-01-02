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
 * @Annotation\Target("class")
 */
class Column extends Annotation
{
    public $name;
    public $type;
    public $default;
}