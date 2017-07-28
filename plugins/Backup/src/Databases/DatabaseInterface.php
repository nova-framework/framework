<?php

namespace Backup\Databases;


interface DatabaseInterface
{
    /**
     * Create a database dump
     *
     * @return boolean
     */
    public function dump($destinationFile);

    /**
     * Restore a database dump
     *
     * @return boolean
     */
    public function restore($sourceFile);

    /**
     * Return the file extension of a dump file (sql, ...)
     *
     * @return string
     */
    public function getFileExtension();
}
