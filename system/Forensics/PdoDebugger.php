<?php

namespace Nova\Forensics;

/**
 * Class PdoDebugger
 *
 * Emulates the PDO SQL statement in an extremely simple kind of way
 */
class PdoDebugger
{
    /**
     * Returns the emulated SQL string
     *
     * @param $raw_sql
     * @param $parameters
     * @return mixed
     */
    static public function show($raw_sql, $parameters)
    {
        $keys = array();
        $values = $parameters;

        foreach ($parameters as $key => $value) {

            // check if named parameters (':param') or anonymous parameters ('?') are used
            if (is_string($key)) {
                $keys[] = '/:'.$key.'/';
            } else {
                $keys[] = '/[?]/';
            }

            // bring parameter into human-readable format
            if (is_string($value)) {
                $values[$key] = "'" . $value . "'";
            } elseif (is_array($value)) {
                $values[$key] = implode(',', $value);
            } elseif (is_null($value)) {
                $values[$key] = 'NULL';
            }
        }

        $raw_sql = preg_replace($keys, $values, $raw_sql, 1, $count);

        return $raw_sql;
    }
}
