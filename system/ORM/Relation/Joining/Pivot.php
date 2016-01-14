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
use Nova\ORM\Model;


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

    public function attach($ids)
    {
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
        $idx = 0;

        foreach($ids as $id) {
            if ($idx > 0) {
                $sql .= ' ,';
            } else {
                $idx++;
            }

            // Prepare the keys Data; considering that we support only integer keys.
            $data = array(intval($id), intval($this->otherId));

            // Prepare the SQL insert values.
            $sql .= ' (' .implode(', ', array_map(array($this->db, 'quote'), $data)) .')';
        }

        // Prepare the Statement and return the result of its execution.
        $stmt = $this->db->prepare($sql);

        return $stmt->execute();
    }

    public function dettach($ids = null)
    {
        $where = array($this->otherKey => $this->otherId);

        if(! is_null($ids)) {
            $key = $this->primaryKey;

            $where[$key] = $ids;
        }

        return $this->db->delete($this->table(), $where);
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
