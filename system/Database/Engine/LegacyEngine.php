<?php


namespace Nova\Database\Engine;

/**
 * Legacy database functions
 *
 * @package Nova\Database\Engine
 */
trait LegacyEngine
{
    /**
     * Legacy select call
     *
     * @param string $sql
     * @param array $array
     * @param int $fetchMode
     * @deprecated
     * @param string $class
     *
     * @return mixed
     */
    public function select($sql, $array = array(), $fetchMode = null, $class = '')
    {
        // Adapt legacy to new function parameters
        if (strtolower(substr($sql, 0, 7)) !== 'select ') {
            $sql = "SELECT " . $sql;
        }
        if (!is_array($array)) {
            $array = array();
        }
        if ($class == '') {
            $class = null;
        }

        // Call new parameter
        return $this->executeQuery($sql, $array, $fetchMode, $class);
    }

    /**
     * Legacy insert call
     *
     * @param string $table
     * @param array $data
     * @deprecated
     * @return mixed
     */
    public function insert($table, $data)
    {
        return $this->executeInsert($table, $data);
    }

    /**
     * Legacy update call
     *
     * @param string $table
     * @param array $data
     * @param array $where
     * @deprecated
     * @return mixed
     */
    public function update($table, $data, $where)
    {
        return $this->executeUpdate($table, $data, $where, null);
    }

    /**
     * Legacy delete call
     *
     * @param string $table
     * @param array $where
     * @param int $limit
     * @deprecated
     * @return mixed
     */
    public function delete($table, $where, $limit = 1)
    {
        return $this->executeDelete($table, $where, $limit);
    }
}