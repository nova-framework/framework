<?php
/**
 * BelongsToPivot
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 13th, 2016
 */

namespace Nova\ORM\Relation;

use Nova\Database\Connection;
use Nova\Database\Manager as Database;
use Nova\ORM\Model;

use \PDO;


class Pivot extends Model
{
    /**
     * The parent model of the relationship.
     *
     * @var \Nova\ORM\Model
     */
    protected $parent;

    /**
     * The name of the foreign key column.
     *
     * @var string
     */
    protected $foreignKey;

    /**
     * The name of the "other key" column.
     *
     * @var string
     */
    protected $otherKey;

    protected $otherId;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    public function __construct($tableName, $foreignKey, $otherKey, $otherId)
    {
        $this->tableName = $tableName;

        $this->foreignKey = $foreignKey;

        // Execute the parent Constructor.
        parent::__construct();

        // The otherKey and otherId are associated to target Model.
        $this->otherKey = $otherKey;

        $this->otherId = $otherId;
    }

    /**
     * Get the foreign key column name.
     *
     * @return string
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * Get the "other key" column name.
     *
     * @return string
     */
    public function getOtherKey()
    {
        return $this->otherKey;
    }

    /**
     * Set the key names for the pivot model instance.
     *
     * @param  string  $foreignKey
     * @param  string  $otherKey
     * @return $this
     */
    public function setPivotKeys($foreignKey, $otherKey)
    {
        $this->foreignKey = $foreignKey;

        $this->otherKey = $otherKey;

        return $this;
    }

    public function get()
    {
        $table = $this->table();

        // Prepare the SQL Query.
        $sql = sprintf('SELECT %s FROM %s WHERE %s = :otherId', $this->foreignKey, $table, $this->otherKey);

        //
        $params = array('otherId' => $this->otherId);

        // Prepare the PDO Statement.
        $stmt = $this->db->rawPrepare($sql, $params, array('otherId' => PDO::PARAM_INT));

        // Execute the Statement and return false if it fail.
        $result = $stmt->execute();

        if ($result !== false) {
            $result = array();

            while(($id = $stmt->fetchColumn()) !== false) {
                $result[] = $id;
            }
        }

        return $result;
    }

    public function attach($ids)
    {
        $table = $this->table();

        // Ensure having always an array of IDs.
        if(! is_array($ids)) {
            $ids = array(intval($ids));
        }

        // Prepare the SQL Query.
        $sql = sprintf('INSERT INTO %s (`%s`, `%s`) VALUES', $table, $this->foreignKey, $this->otherKey);

        // Prepare the values insertion in pairs.
        $otherId = intval($this->otherId);

        $idx = 0;

        foreach($ids as $id) {
            if ($idx > 0) {
                $sql .= ' ,';
            } else {
                $idx++;
            }

            // Force the current ID to integer.
            $id = intval($id);

            if($id < 1) {
                throw new \UnexpectedValueException(__d('system', 'Invalid parameters'));
            }

            // Prepare the keys Data; considering that we support only integer keys.
            $data = array($id, $otherId);

            // Prepare the SQL insert values.
            $sql .= ' (' .implode(', ', array_map(array($this->db, 'quote'), $data)) .')';
        }

        // Prepare the Statement and return the result of its execution.
        $stmt = $this->db->rawPrepare($sql);

        $result = $stmt->execute();

        return $result;
    }

    public function dettach($ids = null)
    {
        $table = $this->table();

        $foreignKey = $this->foreignKey;

        $where = array($this->otherKey => $this->otherId);

        if(! is_null($ids)) {
            $where[$foreignKey] = $ids;
        }

        //Prepare the paramTypes.
        $paramTypes = $this->getParamTypes($where, true);

        return $this->db->delete($table, $where, $paramTypes);
    }

    public function sync($ids)
    {
        $result = $this->dettach();

        if($result !== false) {
            $result = $this->attach($ids);
        }

        return $result;
    }

}
