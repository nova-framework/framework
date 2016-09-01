<?php

namespace Database\Query\Processors;

use Database\Query\Processor;


class SQLiteProcessor extends Processor
{
    /**
     * Process the results of a column listing query.
     *
     * @param  array  $results
     * @return array
     */
    public function processColumnListing($results)
    {
        return array_values(array_map(function($r) { $r = (object) $r; return $r->name; }, $results));
    }

}
