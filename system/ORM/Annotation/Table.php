<?php
/**
 * ORM Annotations - Table
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date January 2nd, 2016
 */

namespace ORM\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Table
 * @package ORM\Annotation
 *
 * @Annotation
 * @Annotation\Target("class")
 */
class Table extends Annotation
{
    public $name;
    public $prefix;
}