<?php
/**
 * MySQL Engine.
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date December 19th, 2015
 */

namespace Nova\Database\Engine;

use Nova\Database\Engine;
use Nova\Database\Manager;
use Nova\Database\Engine\Base as BaseEngine;


class MySQL extends BaseEngine
{

    /**
     * MySQLEngine constructor.
     * Please use the Factory to maintain instances of the drivers.
     *
     * @param $config array
     *
     * @throws \PDOException
     */
    public function __construct($config) {
        // Check for valid Config.
        if (! is_array($config)) {
            throw new \UnexpectedValueException('Parameter should be an Array');
        }

        // Default port if no port is provided.
        if (! isset($config['port'])) {
            $config['port'] = 3306;
        }

        // Some Database Servers go crazy when a charset parameter is added, then we should make it optional.
        if (! isset($config['charset'])) {
            $charsetStr = "";
        }
        else {
            $charsetStr = ($config['charset'] == 'auto') ? "" : ";charset=" . $config['charset'];
        }

        // Prepare the PDO's options.
        $options = array();

        if (isset($config['compress']) && ($config['compress'] === true)) {
            array_push($options, \PDO::MYSQL_ATTR_COMPRESS, true);
        }

        // Prepare the PDO's DSN
        $dsn = "mysql:host=" .$config['host'] .";port=" .$config['port'] .";dbname=" .$config['database'] .$charsetStr;

        parent::__construct($dsn, $config, $options);
    }

    /**
     * Get the name of the driver
     * @return string
     */
    public function getDriverName()
    {
        return "MySQL Driver";
    }

    /**
     * Get driver code, used in config as driver string.
     * @return string
     */
    public function getDriverCode()
    {
        return Manager::DRIVER_MYSQL;
    }

    public function insertBatch($table, $data, $transaction = false)
    {
        // Check for valid data.
        if (!is_array($data)) {
            throw new \Exception("Data to insert must be an array of column -> value.");
        }

        // Transaction?
        $status = false;

        if ($transaction) {
            $status = $this->beginTransaction();
        }

        // Holding status
        $failure = false;

        $ids = array();

        // Loop every record to insert
        foreach($data as $record) {
            ksort($record);

            $fieldNames = implode(',', array_keys($record));
            $fieldValues = ':'.implode(', :', array_keys($record));

            $stmt = $this->prepare("INSERT INTO $table ($fieldNames) VALUES ($fieldValues)");

            foreach ($record as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            // Execute
            $this->queryCount++;

            if (!$stmt->execute()) {
                $failure = true;

                // We need to exit foreach, to inform about the error, or rollback.
                break 1;
            }

            // If no error, capture the last inserted id
            $ids[] = $this->lastInsertId();
        }

        // Commit when in transaction
        if (! $failure && $transaction && $status) {
            $failure = ! $this->commit();
        }

        // Check for failures
        if ($failure) {
            // Ok, rollback when using transactions.
            if ($transaction) {
                $this->rollBack();
            }

            // False on error.
            return false;
        }

        return $ids;
    }

    /**
     * Truncate table
     * @param  string $table table name
     * @return int number of rows affected
     */
    public function truncate($table)
    {
        $this->queryCount++;

        return $this->exec("TRUNCATE TABLE $table");
    }

}
