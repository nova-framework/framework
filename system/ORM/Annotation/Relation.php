<?php
/**
 * ORM Annotations - Relation
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date January 4nd, 2016
 */

namespace Nova\ORM\Annotation;

use Doctrine\Common\Annotations\Annotation;
use PDO;

/**
 * Class Relation
 * @package Nova\ORM\Annotation
 *
 * @Annotation
 * @Annotation\Target("PROPERTY")
 */
class Relation extends Annotation
{
    /**
     * @var string
     * @Annotation\Required()
     * @Annotation\Enum({"OneToOne", "")
     */
    public $type;
}
