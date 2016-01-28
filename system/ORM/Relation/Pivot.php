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

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    public function __construct(Model $parent, array $attributes, $table, $exists = false)
    {
        $this->tableName = $table;

        // Execute the parent Constructor.
        parent::__construct();

        // Init this pivot Model.
        $this->attributes = $attributes;

        $this->initObject($exists);

        // Setup the parent Model.
        $this->parent = $parent;
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

    public function relatedIds()
    {
        $otherId = $this->getAttribute($this->otherKey);

        //
        $query = $this->newBaseQuery();

        $data = $query->where($this->otherKey, $otherId)->select($this->foreignKey)->get();

        if($data === null) {
            return false;
        }

        // Parse the gathered data and return the result.
        $result = array();

        foreach($data as $row) {
            $result[] = array_shift($row);
        }

        return $result;
    }
}
