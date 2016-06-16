<?php

namespace Database\ORM\Relations;

use Database\ORM\Collection;


class HasMany extends HasOneOrMany
{
    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        return $this->query->get();
    }

    /**
     * Execute the query and get the first result.
     *
     * @param  array  $columns
     * @return \Database\ORM\Model|static|null
     */
    public function first($columns = array('*'))
    {
        $results = $this->take(1)->get($columns);

        return (count($results) > 0) ? $results->first() : null;
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param  array   $models
     * @param  string  $relation
     * @return array
     */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
        }

        return $models;
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array   $models
     * @param  \Database\ORM\Collection  $results
     * @param  string  $relation
     * @return array
     */
    public function match(array $models, Collection $results, $relation)
    {
        return $this->matchMany($models, $results, $relation);
    }

}
