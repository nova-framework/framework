<?php

namespace NestedSet\Database\ORM;

use Nova\Database\Connection;
use Nova\Database\Schema\Blueprint;

use NestedSet\Database\ORM\Node;


class NestedSet
{
    /**
     * Add default nested set columns to the table. Also create an index.
     *
     * @param \Nova\Database\Schema\Blueprint $table
     * @param string $primaryKey
     */
    public static function columns(Blueprint $table, $primaryKey = 'id')
    {
        $table->integer('lft')->unsigned();
        $table->integer('rgt')->unsigned();
        $table->integer('parent_id')->unsigned()->nullable();

        $table->index(self::getDefaultColumns());
    }

    /**
     * Drop NestedSet columns.
     *
     * @param \Nova\Database\Schema\Blueprint $table
     */
    public static function dropColumns(Blueprint $table)
    {
        $columns = self::getDefaultColumns();

        $table->dropIndex($columns);
        $table->dropColumn($columns);
    }

    /**
     * Get a list of default columns.
     *
     * @return array
     */
    public static function getDefaultColumns()
    {
        return array('lft', 'rgt', 'parent_id');
    }

}
