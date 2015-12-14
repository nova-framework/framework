<?php


namespace Core\Database;

/**
 * Class Entity, can be extended with your database entities
 * @package Core\Database
 */
abstract class Entity
{
    /** @var null|string Will hold the table name, don't add prefix! */
    public $_table = null;

    /** @var null|array|string Will hold the primary key(s) names */
    public $_pks = null;

}