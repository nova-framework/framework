<?php
/**
 * Base
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 14th, 2016
 */

namespace Nova\ORM;

use Nova\Database\Connection;
use Nova\Database\Manager as Database;


abstract class Base
{
    public static $whereOperators = array("=", "!=", ">", "<", ">=", "<=", "<>", "LIKE");

    /**
     * Temporary select's WHERE attributes.
     */
    protected $tempWheres = array();

    /**
     * Temporary select's ORDER attribute.
     */
    protected $selectOrder = null;

    /**
     * Temporary select's LIMIT attribute.
     */
    protected $selectLimit = null;

    /**
     * Temporary select's OFFSET attribute.
     */
    protected $selectOffset = null;

    /**
     * Constuctor.
     */
    public function __construct()
    {
    }

    //--------------------------------------------------------------------
    // Query Building Methods
    //--------------------------------------------------------------------

    public function where($field, $value = '')
    {
        if(is_null($field)) {
            $this->tempWheres = array();
        } else {
            $params = func_get_args();

            $this->setWhere($params);
        }

        return $this;
    }

    /**
     * Limit results
     *
     * @param int $limit
     * @return BaseModel $this
     */
    public function limit($limit = null)
    {
        if (! is_null($limit) && ! is_integer($limit)) {
            throw new \UnexpectedValueException(__d('system', 'Invalid parameter'));
        }

        $this->selectLimit  = $limit;

        return $this;
    }

    /**
     * Offset
     *
     * @param int $offset
     * @return BaseModel $this
     */
    public function offset($offset = null)
    {
        if (! is_null($offset) && ! is_integer($offset)) {
            throw new \UnexpectedValueException(__d('system', 'Invalid parameter'));
        }

        $this->selectOffset = $offset;

        return $this;
    }

    /**
     * Order by
     * @param mixed $order
     * @return BaseModel $this
     */
    public function orderBy($order)
    {
        if(empty($order)) {
            $this->selectOrder = null;
        }
        // Ccheck if the Field contains conditions.
        else if (strpos($order, ' ') !== false) {
            // Simplify the white spaces on Field.
            $order = preg_replace('/\s+/', ' ', trim($order));

            // Explode the field into its components.
            $segments = explode(' ', $order);

            if(count($segments) !== 2) {
                throw new \UnexpectedValueException(__d('system', 'Invalid parameter'));
            }

            $field = $segments[0];
            $sense = strtoupper($segments[1]);

            if(($sense != 'ASC') && ($sense != 'DESC')) {
                throw new \UnexpectedValueException(__d('system', 'Invalid parameter'));
            }

            $this->selectOrder = $field .' ' .$sense;
        }
        else {
            $this->selectOrder = $order;
        }

        return $this;
    }

    protected function resetState()
    {
        // Reset our select WHEREs
        $this->tempWheres = array();

        // Reset our select ORDER
        $this->selectOrder = null;

        // Reset our select LIMIT
        $this->selectLimit = null;

        // Reset our select OFFSET
        $this->selectOffset = null;
    }

    /**
     * Set where
     * @param array $params
     * @return array
     */
    protected function setWhere(array $params = array())
    {
        if (empty($params)) {
            return $this->tempWheres;
        }

        // Get the WHERE condition.
        $condition = array_shift($params);

        if ($condition == null) {
            // Remove all previous defined conditions from our own WHEREs array, too.
            $this->tempWheres = array();
        } else if (is_array($condition)) {
            // Is given an array of Conditions; merge them into our own WHEREs array.
            $this->tempWheres = array_merge($this->tempWheres, $condition);
        } else if (count($params) == 1) {
            // Store the condition and its value.
            $this->tempWheres[$condition] = array_shift($params);
        } else if (count($params) == 2) {
            $operator = array_shift($params);

            if (! in_array($operator, Connection::$whereOperators, true)) {
                throw new \UnexpectedValueException(__d('system', 'Second parameter is invalid'));
            }

            $condition = sprintf('%s %s ?', $condition, $operator);

            // Store the composed condition and its value.
            $this->tempWheres[$condition] = array_shift($params);
        } else {
            throw new \UnexpectedValueException(__d('system', 'Invalid number of parameters'));
        }

        return $this->tempWheres;
    }

    /**
     * Parse the where conditions.
     *
     * @param array $where
     * @param $bindParams
     * @return string
     */
    public static function parseWhereConditions(array $where, &$bindParams)
    {
        $result = '';

        $connection = Database::getConnection();

        // Flag which say when we need to add an AND keyword.
        $idx = 0;

        foreach ($where as $field => $value) {
            if ($idx > 0) {
                // Add the 'AND' keyword for the current condition.
                $result .= ' AND ';
            } else {
                $idx++;
            }

            // Firstly, we need to check if the Field contains conditions.
            if (strpos($field, ' ') !== false) {
                // Simplify the white spaces on Field.
                $field = preg_replace('/\s+/', ' ', trim($field));

                // Explode the field into its components.
                $segments = explode(' ', $field);

                if (count($segments) != 3) {
                    throw new \UnexpectedValueException(__d('system', 'Invalid parameters'));
                }

                $fieldName = $segments[0];
                $operator  = $segments[1];
                $bindName  = $segments[2];

                if (! in_array($operator, self::$whereOperators, true)) {
                    throw new \UnexpectedValueException(__d('system', 'Invalid parameters'));
                }

                if ($bindName == '?') {
                    $result .= "$fieldName $operator :$fieldName";

                    $bindParams[$fieldName] = $value;
                } else {
                    if ((substr($bindName, 0, 1) !== ':') || ! is_array($value)) {
                        throw new \UnexpectedValueException(__d('system', 'Invalid parameters'));
                    }

                    $result .= "$fieldName $operator $bindName";

                    // Extract the Value from the array.
                    $value = $value[$bindName];

                    // Remove first character, aka ':', from bindName.
                    $bindName = substr($bindName, 1);

                    $bindParams[$bindName] = $value;
                }

                continue;
            }

            // Process the condition based on Value type.
            if (is_null($value)) {
                $result .= "$field is NULL";

                continue;
            }

            if (is_array($value)) {
                // We need something like: user_id IN (1, 2, 3)
                $result .= "$field IN (" . implode(', ', array_map(array($connection, 'quote'), $value)) . ")";
            } else {
                $result .= "$field = :$field";
            }

            $bindParams[$field] = $value;
        }

        if(empty($result)) {
            // There are no WHERE conditions, then we make the Database to match all records.
            $result = '1';
        }

        return $result;
    }

    /**
     * Wheres
     * @return array
     */
    protected function wheres()
    {
        return $this->tempWheres;
    }

    protected function getOrder()
    {
        return $this->selectOrder;
    }

    protected function getLimit()
    {
        return $this->selectLimit;
    }

    protected function getOffset()
    {
        return $this->selectOffset;
    }

}
