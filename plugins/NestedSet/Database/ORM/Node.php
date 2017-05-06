<?php

namespace Plugins\NestedSet\Database\ORM;

use Nova\Database\ORM\Relations\BelongsTo;
use Nova\Database\ORM\Relations\HasMany;
use Nova\Database\ORM\Model;
use Nova\Database\ORM\Collection as BaseCollection;

use Plugins\NestedSet\Database\ORM\Builder;
use Plugins\NestedSet\Database\ORM\Collection;

use Exception;
use LogicException;


class Node extends Model
{
    /**
     * Insert direction.
     *
     * @var string
     */
    const BEFORE = 'before';

    /**
     * Insert direction.
     *
     * @var string
     */
    const AFTER = 'after';

    /**
     * Column name to store the reference to parent's node.
     *
     * @var string
     */
    protected $parentColumn = 'parent_id';

    /**
     * Column name for left index.
     *
     * @var string
     */
    protected $leftColumn = 'lft';

    /**
     * Column name for right index.
     *
     * @var string
     */
    protected $rightColumn = 'rgt';

    /**
     * Whether the node is being deleted.
     *
     * @var bool
     */
    static protected $deleting = false;

    /**
     * Pending operation.
     *
     * @var array
     */
    protected $pending = array('root');

    /**
     * Whether the node has moved since last save.
     *
     * @var bool
     */
    protected $moved = false;

    /**
     * @var \Carbon\Carbon
     */
    protected static $deletedAt;

    /**
     * Keep track of the number of performed operations.
     *
     * @var int
     */
    protected static $actionsPerformed = 0;


    /**
     * {@inheritdoc}
     */
    protected static function boot()
    {
        parent::boot();

        static::signOnEvents();
    }

    /**
     * @return bool
     */
    public static function usesSoftDelete()
    {
        static $softDelete = null;

        if (is_null($softDelete)) {
            $instance = new static();

            return $softDelete = method_exists($instance, 'bootSoftDeletingTrait');
        }

        return $softDelete;
    }

    /**
     * Sign on model events.
     */
    protected static function signOnEvents()
    {
        static::saving(function(Node $model)
        {
            return $model->callPendingAction();
        });

        static::deleting(function(Node $model)
        {
            // We will need fresh data to delete node safely
            $model->refreshNode();
        });

        static::deleted(function (Node $model)
        {
            $model->deleteDescendants();
        });

        // Soft Deleting support.
        if (! static::usesSoftDelete()) return;

        static::restoring(function(Node $model)
        {
            static::$deletedAt = $model->{$model->getDeletedAtColumn()};
        });

        static::restored(function(Node $model)
        {
            $model->restoreDescendants(static::$deletedAt);
        });
    }

    /**
     * {@inheritdoc}
     *
     * Saves a node in a transaction.
     */
    public function save(array $options = array())
    {
        return $this->getConnection()->transaction(function() use ($options)
        {
            return parent::save($options);
        });
    }

    /**
     * {@inheritdoc}
     *
     * Delete a node in transaction if model is not soft deleting.
     */
    public function delete()
    {
        return $this->getConnection()->transaction(function()
        {
            return parent::delete();
        });
    }

    /**
     * Set an action.
     *
     * @param string $action
     *
     * @return $this
     */
    protected function setAction($action)
    {
        $this->pending = func_get_args();

        return $this;
    }

    /**
     * Clear pending action.
     */
    protected function clearAction()
    {
        $this->pending = null;
    }

    /**
     * Call pending action.
     *
     * @return null|false
     */
    protected function callPendingAction()
    {
        $this->moved = false;

        if ( ! $this->pending) return;

        $method = 'action' .ucfirst(array_shift($this->pending));

        //
        $parameters = $this->pending;

        $this->pending = null;

        //
        $this->moved = call_user_func_array(array($this, $method), $parameters);
    }

    /**
     * Make a root node.
     */
    protected function actionRoot()
    {
        // Simplest case that do not affect other nodes.
        if (! $this->exists) {
            $cut = $this->getLowerBound() + 1;

            $this->setAttribute($this->getLeftName(), $cut);

            $this->setAttribute($this->getRightName(), $cut + 1);

            return true;
        }

        if ($this->isRoot()) return false;

        // Reset parent object
        $this->setParent(null);

        return $this->insertAt($this->getLowerBound() + 1);
    }

    /**
     * Get the lower bound.
     *
     * @return int
     */
    protected function getLowerBound()
    {
        $field = $this->getRightName();

        return (int) $this->newServiceQuery()->max($field);
    }

    /**
     * Append a node to the parent.
     *
     * @param Node $parent
     *
     * @return bool
     */
    protected function actionAppendTo(Node $parent)
    {
        return $this->actionAppendOrPrepend($parent);
    }

    /**
     * Prepend a node to the parent.
     *
     * @param Node $parent
     *
     * @return bool
     */
    protected function actionPrependTo(Node $parent)
    {
        return $this->actionAppendOrPrepend($parent, true);
    }

    /**
     * Append or prepend a node to the parent.
     *
     * @param Node $parent
     * @param bool $prepend
     *
     * @return bool
     */
    protected function actionAppendOrPrepend(Node $parent, $prepend = false)
    {
        if (! $parent->exists) {
            throw new LogicException('Cannot use non-existing node as a parent.');
        }

        $this->setParent($parent);

        $parent->refreshNode();

        // Calculate the position for insertion.
        if ($prepend) {
            $cut = $parent->getLeft() + 1;
        } else {
            $cut = $parent->getRight();
        }

        if ($this->insertAt($cut)) {
            $parent->refreshNode();

            return true;
        }

        return false;
    }

    /**
     * Apply parent model.
     *
     * @param Node|null $value
     */
    protected function setParent($value)
    {
        $key = $this->getParentIdName();

        $this->attributes[$key] = ! is_null($value) ? $value->getKey() : null;

        $this->setRelation('parent', $value);
    }

    /**
     * Insert node before or after another node.
     *
     * @param Node $node
     * @param bool $after
     *
     * @return bool
     */
    protected function actionBeforeOrAfter(Node $node, $after = false)
    {
        if (! $node->exists) {
            throw new LogicException('Cannot insert before/after non-existing node.');
        }

        if ($this->getParentId() <> $node->getParentId()) {
            $this->setParent($node->getAttribute('parent'));
        }

        $node->refreshNode();

        return $this->insertAt($after ? $node->getRight() + 1 : $node->getLeft());
    }

    /**
     * Insert node before other node.
     *
     * @param Node $node
     *
     * @return bool
     */
    protected function actionBefore(Node $node)
    {
        return $this->actionBeforeOrAfter($node);
    }

    /**
     * Insert node after other node.
     *
     * @param Node $node
     *
     * @return bool
     */
    protected function actionAfter(Node $node)
    {
        return $this->actionBeforeOrAfter($node, true);
    }

    /**
     * Refresh node's crucial attributes.
     */
    public function refreshNode()
    {
        if (! $this->exists || (static::$actionsPerformed === 0)) return;

        $attributes = $this->newServiceQuery()->getNodeData($this->getKey());

        $this->attributes = array_merge($this->attributes, $attributes);

        $this->original = array_merge($this->original, $attributes);
    }

    /**
     * Get the root node.
     *
     * @param   array   $columns
     *
     * @return  Node
     */
    static public function root(array $columns = array('*'))
    {
        return static::whereIsRoot()->first($columns);
    }

    /**
     * Relation to the parent.
     *
     * @return BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(get_class($this), $this->getParentIdName());
    }

    /**
     * Relation to children.
     *
     * @return HasMany
     */
    public function children()
    {
        return $this->hasMany(get_class($this), $this->getParentIdName());
    }

    /**
     * Get query for descendants of the node.
     *
     * @return  \Plugins\NestedSet\Database\ORM\Builder
     */
    public function descendants()
    {
        return $this->newQuery()->whereDescendantOf($this->getKey());
    }

    /**
     * Get query for siblings of the node.
     *
     * @param self::AFTER|self::BEFORE|null $dir
     *
     * @return \Plugins\NestedSet\Database\ORM\Builder
     */
    public function siblings($dir = null)
    {
        switch ($dir) {
            case self::AFTER:
                $query = $this->next();

                break;
            case self::BEFORE:
                $query = $this->prev();

                break;
            default:
                $query = $this->newQuery()
                    ->defaultOrder()
                    ->where($this->getKeyName(), '<>', $this->getKey());

                break;
        }

        $query->where($this->getParentIdName(), '=', $this->getParentId());

        return $query;
    }

    /**
     * Get query for siblings after the node.
     *
     * @return \Plugins\NestedSet\Database\ORM\Builder
     */
    public function nextSiblings()
    {
        return $this->siblings(self::AFTER);
    }

    /**
     * Get query for siblings before the node.
     *
     * @return \Plugins\NestedSet\Database\ORM\Builder
     */
    public function prevSiblings()
    {
        return $this->siblings(self::BEFORE);
    }

    /**
     * Get query for nodes after current node.
     *
     * @return \Plugins\NestedSet\Database\ORM\Builder
     */
    public function next()
    {
        return $this->newQuery()
            ->whereIsAfter($this->getKey())
            ->defaultOrder();
    }

    /**
     * Get query for nodes before current node in reversed order.
     *
     * @return \Plugins\NestedSet\Database\ORM\Builder
     */
    public function prev()
    {
        return $this->newQuery()
            ->whereIsBefore($this->getKey())
            ->reversed();
    }

    /**
     * Get query for ancestors to the node not including the node itself.
     *
     * @return  \Plugins\NestedSet\Database\ORM\Builder
     */
    public function ancestors()
    {
        return $this->newQuery()
            ->whereAncestorOf($this->getKey())
            ->defaultOrder();
    }

    /**
     * Make this node a root node.
     *
     * @return $this
     */
    public function makeRoot()
    {
        return $this->setAction('root');
    }

    /**
     * Save node as root.
     *
     * @return bool
     */
    public function saveAsRoot()
    {
        return $this->makeRoot()->save();
    }

    /**
     * Append and save a node.
     *
     * @param Node $node
     *
     * @return bool
     */
    public function append(Node $node)
    {
        return $node->appendTo($this)->save();
    }

    /**
     * Prepend and save a node.
     *
     * @param Node $node
     *
     * @return bool
     */
    public function prepend(Node $node)
    {
        return $node->prependTo($this)->save();
    }

    /**
     * Append a node to the new parent.
     *
     * @param Node $parent
     *
     * @return $this
     */
    public function appendTo(Node $parent)
    {
        return $this->setAction('appendTo', $parent);
    }

    /**
     * Prepend a node to the new parent.
     *
     * @param Node $parent
     *
     * @return $this
     */
    public function prependTo(Node $parent)
    {
        return $this->setAction('prependTo', $parent);
    }

    /**
     * Insert self after a node.
     *
     * @param Node $node
     *
     * @return $this
     */
    public function after(Node $node)
    {
        return $this->setAction('after', $node);
    }

    /**
     * Insert self after a node and save.
     *
     * @param Node $node
     *
     * @return bool
     */
    public function insertAfter(Node $node)
    {
        return $this->after($node)->save();
    }

    /**
     * Insert self before node.
     *
     * @param Node $node
     *
     * @return $this
     */
    public function before(Node $node)
    {
        return $this->setAction('before', $node);
    }

    /**
     * Insert self before a node and save.
     *
     * @param Node $node
     *
     * @return bool
     */
    public function insertBefore(Node $node)
    {
        if ($this->before($node)->save()) {
            // We'll' update the target node since it will be moved.
            $node->refreshNode();

            return true;
        }

        return false;
    }

    /**
     * Move node up given amount of positions.
     *
     * @param int $amount
     *
     * @return bool
     */
    public function up($amount = 1)
    {
        $sibling = $this->prevSiblings()->skip($amount - 1)->first();

        if (! is_null($sibling)) {
            return $this->insertBefore($sibling);
        }

        return false;
    }

    /**
     * Move node down given amount of positions.
     *
     * @param int $amount
     *
     * @return bool
     */
    public function down($amount = 1)
    {
        $sibling = $this->nextSiblings()->skip($amount - 1)->first();

        if (! is_null($sibling)) {
            return $this->insertAfter($sibling);
        }

        return false;
    }

    /**
     * Insert node at specific position.
     *
     * @param  int $position
     *
     * @return bool
     */
    protected function insertAt($position)
    {
        ++static::$actionsPerformed;

        if ($this->exists) {
            return $this->moveNode($position);
        }

        return $this->insertNode($position);
    }

    /**
     * Move a node to the new position.
     *
     * @since 2.0
     *
     * @param int $position
     *
     * @return int
     */
    protected function moveNode($position)
    {
        $updated = ($this->newServiceQuery()->moveNode($this->getKey(), $position) > 0);

        if ($updated) $this->refreshNode();

        return $updated;
    }

    /**
     * Insert new node at specified position.
     *
     * @since 2.0
     *
     * @param int $position
     *
     * @return bool
     */
    protected function insertNode($position)
    {
        $this->newServiceQuery()->makeGap($position, 2);

        $height = $this->getNodeHeight();

        $this->setAttribute($this->getLeftName(), $position);

        $this->setAttribute($this->getRightName(), $position + $height - 1);

        return true;
    }

    /**
     * Update the tree when the node is removed physically.
     */
    protected function deleteDescendants()
    {
        if (static::$deleting) return;

        $lft = $this->getLeft();
        $rgt = $this->getRight();

        // Make sure that inner nodes are just deleted and don't touch the tree
        // This makes sense in Laravel 4.2
        static::$deleting = true;

        $query = $this->newQuery()->whereNodeBetween(array($lft + 1, $rgt));

        if (static::usesSoftDelete() && $this->forceDeleting) {
            $query->withTrashed()->forceDelete();
        } else {
            $query->delete();
        }

        static::$deleting = false;

        if ($this->hardDeleting()) {
            $height = $rgt - $lft + 1;

            $this->newServiceQuery()->makeGap($rgt + 1, -$height);

            // In case if user wants to re-create the node
            $this->makeRoot();

            static::$actionsPerformed++;
        }
    }

    /**
     * Restore the descendants.
     *
     * @param $deletedAt
     */
    protected function restoreDescendants($deletedAt)
    {
        $this->newQuery()
            ->whereNodeBetween(array($this->getLeft() + 1, $this->getRight()))
            ->where($this->getDeletedAtColumn(), '>=', $deletedAt)
            ->restore();
    }

    /**
     * {@inheritdoc}
     *
     * @since 2.0
     */
    public function newBuilder($query)
    {
        return new Builder($query);
    }

    /**
     * Get a new base query that includes deleted nodes.
     *
     * @since 1.1
     *
     * @return \Plugins\NestedSet\Database\ORM\Builder
     */
    public function newServiceQuery()
    {
        return static::usesSoftDelete() ? $this->withTrashed() : $this->newQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function newCollection(array $models = array())
    {
        return new Collection($models);
    }

    /**
     * {@inheritdoc}
     */
    public function newFromBuilder($attributes = array(), $connection = null)
    {
        /** @var Node $instance */
        $instance = parent::newFromBuilder($attributes, $connection);

        $instance->clearAction();

        return $instance;
    }

    /**
     * {@inheritdoc}
     *
     * Use `children` key on `$attributes` to create child nodes.
     *
     * @param Node $parent
     *
     */
    public static function create(array $attributes = array(), Node $parent = null)
    {
        $children = array_pull($attributes, 'children');

        $instance = new static($attributes);

        if ($parent) $instance->appendTo($parent);

        $instance->save();

        // Now create children
        $relation = new BaseCollection();

        foreach ((array) $children as $child) {
            $relation->add($child = static::create($child, $instance));

            $child->setRelation('parent', $instance);
        }

        return $instance->setRelation('children', $relation);
    }

    /**
     * Get node height (rgt - lft + 1).
     *
     * @return int
     */
    public function getNodeHeight()
    {
        if (! $this->exists) return 2;

        return $this->getRight() - $this->getLeft() + 1;
    }

    /**
     * Get number of descendant nodes.
     *
     * @return int
     */
    public function getDescendantCount()
    {
        return round($this->getNodeHeight() / 2) - 1;
    }

    /**
     * Set the value of model's parent id key.
     *
     * Behind the scenes node is appended to found parent node.
     *
     * @param int $value
     *
     * @throws Exception If parent node doesn't exists
     */
    public function setParentIdAttribute($value)
    {
        $field = $this->getParentIdName();

        if ($this->getAttribute($field) != $value) {
            if (! is_null($value)) {
                $this->appendTo($this->newQuery()->findOrFail($value));
            } else {
                $this->makeRoot();
            }
        }
    }

    /**
     * Get whether node is root.
     *
     * @return boolean
     */
    public function isRoot()
    {
        $key = $this->getParentIdName();

        return ($this->getAttribute($key) === null);
    }

    /**
     * Get the lft key name.
     *
     * @return  string
     */
    public function getLeftName()
    {
        return $this->leftColumn;
    }

    /**
     * Get the rgt key name.
     *
     * @return  string
     */
    public function getRightName()
    {
        return $this->rightColumn;
    }

    /**
     * Get the parent id key name.
     *
     * @return  string
     */
    public function getParentIdName()
    {
        return $this->parentColumn;
    }

    /**
     * Get the value of the model's lft key.
     *
     * @return  integer
     */
    public function getLeft()
    {
        $key = $this->getLeftName();

        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
    }

    /**
     * Get the value of the model's rgt key.
     *
     * @return  integer
     */
    public function getRight()
    {
        $key = $this->getRightName();

        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
    }

    /**
     * Get the value of the model's parent id key.
     *
     * @return  integer
     */
    public function getParentId()
    {
        return $this->getAttribute($this->getParentIdName());
    }

    /**
     * Shorthand for next()
     *
     * @param  array  $columns
     *
     * @return Node
     */
    public function getNext(array $columns = array('*'))
    {
        return $this->next()->first($columns);
    }

    /**
     * Shorthand for prev()
     *
     * @param  array  $columns
     *
     * @return Node
     */
    public function getPrev(array $columns = array('*'))
    {
        return $this->prev()->first($columns);
    }

    /**
     * Shorthand for ancestors()
     *
     * @param  array  $columns
     *
     * @return Collection
     */
    public function getAncestors(array $columns = array('*'))
    {
        return $this->newQuery()->defaultOrder()->ancestorsOf($this->getKey(), $columns);
    }

    /**
     * Shorthand for descendants()
     *
     * @param  array  $columns
     *
     * @return Collection|Node[]
     */
    public function getDescendants(array $columns = array('*'))
    {
        return $this->newQuery()->defaultOrder()->descendantsOf($this->getKey(), $columns);
    }

    /**
     * Shorthand for siblings()
     *
     * @param array $columns
     *
     * @return Collection|Node[]
     */
    public function getSiblings(array $columns = array('*'))
    {
        return $this->siblings()->defaultOrder()->get($columns);
    }

    /**
     * Shorthand for nextSiblings().
     *
     * @param  array  $columns
     *
     * @return Collection|Node[]
     */
    public function getNextSiblings(array $columns = array('*'))
    {
        return $this->nextSiblings()->get($columns);
    }

    /**
     * Shorthand for prevSiblings().
     *
     * @param  array  $columns
     *
     * @return Collection|Node[]
     */
    public function getPrevSiblings(array $columns = array('*'))
    {
        return $this->prevSiblings()->get($columns);
    }

    /**
     * Get next sibling.
     *
     * @param  array  $columns
     *
     * @return Node
     */
    public function getNextSibling(array $columns = array('*'))
    {
        return $this->nextSiblings()->first($columns);
    }

    /**
     * Get previous sibling.
     *
     * @param  array  $columns
     *
     * @return Node
     */
    public function getPrevSibling(array $columns = array('*'))
    {
        return $this->prevSiblings()->reversed()->first($columns);
    }

    /**
     * Get whether a node is a descendant of other node.
     *
     * @param Node $other
     *
     * @return bool
     */
    public function isDescendantOf(Node $other)
    {
        return $this->getLeft() > $other->getLeft() and $this->getLeft() < $other->getRight();
    }

    /**
     * Get whether the node is immediate children of other node.
     *
     * @param Node $other
     *
     * @return bool
     */
    public function isChildOf(Node $other)
    {
        return ($this->getParentId() == $other->getKey());
    }

    /**
     * Get whether the node is a sibling of another node.
     *
     * @param Node $other
     *
     * @return bool
     */
    public function isSiblingOf(Node $other)
    {
        return ($this->getParentId() == $other->getParentId());
    }

    /**
     * Get whether the node is an ancestor of other node, including immediate parent.
     *
     * @param Node $other
     *
     * @return bool
     */
    public function isAncestorOf(Node $other)
    {
        return $other->isDescendantOf($this);
    }

    /**
     * Get statistics of errors of the tree.
     *
     * @since 2.0
     *
     * @return array
     */
    public static function countErrors()
    {
        $model = new static();

        return $model->newServiceQuery()->countErrors();
    }

    /**
     * Get the number of total errors of the tree.
     *
     * @since 2.0
     *
     * @return int
     */
    public static function getTotalErrors()
    {
        return array_sum(static::countErrors());
    }

    /**
     * Get whether the tree is broken.
     *
     * @since 2.0
     *
     * @return bool
     */
    public static function isBroken()
    {
        return (static::getTotalErrors() > 0);
    }

    /**
     * Get whether the node has moved since last save.
     *
     * @return bool
     */
    public function hasMoved()
    {
        return $this->moved;
    }

    /**
     * @return array
     */
    protected function getArrayableRelations()
    {
        $result = parent::getArrayableRelations();

        // To fix when converting tree to json falling to infinite recursion.
        unset($result['parent']);

        return $result;
    }

    /**
     * Get whether user is intended to delete the model from database entirely.
     *
     * @return bool
     */
    protected function hardDeleting()
    {
        return ! static::usesSoftDelete() or $this->forceDeleting;
    }

    /**
     * @return array
     */
    public function getBounds()
    {
        return array($this->getLeft(), $this->getRight());
    }

    /**
     * Fixes the tree based on parentage info.
     *
     * Requires at least one root node. This will not update nodes with invalid parent.
     *
     * @return int The number of fixed nodes.
     */
    public static function fixTree()
    {
        $model = new static();

        $columns = array(
            $model->getKeyName(),
            $model->getParentIdName(),
            $model->getLeftName(),
            $model->getRightName(),
        );

        $nodes = $model->newQuery()
            ->defaultOrder()
            ->get($columns)
            ->groupBy($model->getParentIdName());

        self::reorderNodes($nodes, $fixed);

        return $fixed;
    }

    /**
     * @param Plugins\NestedSet\Database\ORM\Collection $models
     * @param int $fixed
     * @param $parentId
     * @param int $cut
     *
     * @return int
     */
    protected static function reorderNodes(Collection $models, &$fixed, $parentId = null, $cut = 1)
    {
        /** @var Node $model */
        foreach ($models->get($parentId, array()) as $model) {
            $model->setLeft($cut);

            $cut = self::reorderNodes($models, $fixed, $model->getKey(), $cut + 1);

            $model->setRight($cut);

            if ($model->isDirty()) {
                $model->save();

                $fixed++;
            }

            ++$cut;
        }

        return $cut;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setLeft($value)
    {
        $this->attributes[$this->getLeftName()] = $value;
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setRight($value)
    {
        $this->attributes[$this->getRightName()] = $value;
        return $this;
    }


}
