<?php
/**
 * BelongsToPivot
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 13th, 2016
 */

namespace Nova\ORM\Relation\Joining;

use Nova\Database\Connection;
use Nova\Database\Manager as Database;
use Nova\ORM\Engine;

use \PDO;


class Pivot extends Engine
{
    protected $otherKey;
    protected $otherId;


    public function __construct($tableName, $foreignKey, $otherKey, $otherId)
    {
        $this->tableName = $tableName;

        $this->primaryKey = $foreignKey;

        // Execute the parent Constructor.
        parent::__construct();

        // The otherKey and otherId are associated to target Model.
        $this->otherKey = $otherKey;
        $this->otherId  = $otherId;
    }

    public function get()
    {
        $sql = sprintf(
            'SELECT %s FROM %s WHERE %s = :whereId',
            $this->primaryKey,
            $this->table(),
            $this->otherKey
        );

        // Prepare the PDO Statement.
        $stmt = $this->db->rawPrepare($sql);

        // Bind the parameters.
        $stmt->bindValue(':whereId', $this->otherId, PDO::PARAM_INT);

        // Execute the Statement and return false if it fail.
        if (! $stmt->execute()) {
            return false;
        }

        //
        $result = array();

        while(($id = $stmt->fetchColumn()) !== false) {
            $result[] = $id;
        }

        return $result;
    }

    public function attach($ids)
    {
        // To avoid multiple insert into Database, we do everything into single step,
        // using a custom method based directly into raw PDO commands.

        $table = $this->table();

        $primaryKey = $this->primaryKey;
        $otherKey   = $this->otherKey;

        // Ensure having always an array of IDs.
        if(! is_array($ids)) {
            $ids = array(intval($ids));
        }

        // Prepare the SQL Query.
        $sql = sprintf(
            'INSERT IGNORE INTO %s (`%s`, `%s`) VALUES',
            $this->table(),
            $this->primaryKey,
            $this->otherKey
        );

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

        return $stmt->execute();
    }

    public function dettach($ids = null)
    {
        $where = array($this->otherKey => $this->otherId);

        if(! is_null($ids)) {
            $where[$this->primaryKey] = $ids;
        }

        //Prepare the paramTypes.
        $paramTypes = $this->getParamTypes($where, true);

        return $this->db->delete($this->table(), $where, $paramTypes);
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
