<?php
/**
 * ORM Annotations - Table
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date January 2nd, 2016
 */

namespace Nova\ORM\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Table
 * @package Nova\ORM\Annotation
 *
 * @Annotation
 * @Annotation\Target("CLASS")
 */
class Table extends Annotation
{
    /**
     * @var string
     * @Annotation\Required()
     */
    public $name;

    /**
     * @var string
     */
    public $prefix = DB_PREFIX;

    /**
     * Link Name, default is default.
     * @var string
     */
    public $link = 'default';
}