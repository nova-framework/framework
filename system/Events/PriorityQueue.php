<?php
/**
 * PriorityQueue - A Prority Queue with predictable queue order.
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 21th, 2015
 */

namespace Nova\Events;


class PriorityQueue extends \SplPriorityQueue
{
    protected $queueOrder = PHP_INT_MAX;


    public function insert($data, $priority)
    {
        if (is_int($priority)) {
            $priority = array($priority, $this->queueOrder--);
        }

        parent::insert($data, $priority);
    }
}
