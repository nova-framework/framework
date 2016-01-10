<?php
/**
 * ClassicModel - Extended Base Class for all the Application Models.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 27th, 2015
 */

namespace App\Core;

use Nova\Database\Manager as Database;
use Nova\Input\Filter as InputFilter;
use Nova\Core\Model;

use \FluentStructure;
use \FluentPDO;
use \PDO;

class BaseModel extends Model
{
    /**
     * The Model's default Table name.
     *
     * @var string;
     */
    protected $table;

    /**
     * The model's default primary key.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The type of date/time field used for created_on and modified_on fields.
     * Valid types are: 'int', 'datetime', 'date'
     *
     * @var string
     *
     * @access protected
     */
    protected $dateFormat = 'datetime';

    /**
     * Whether or not to auto-fill a 'created_on' field on inserts.
     *
     * @var boolean
     *
     * @access protected
     */
    protected $autoCreated = true;

    /**
     * Field name to use to the created time column in the DB table.
     *
     * @var string
     *
     * @access protected
     */
    protected $createdField = 'created_on';

    /**
     * Whether or not to auto-fill a 'modified_on' field on updates.
     *
     * @var boolean
     *
     * @access protected
     */
    protected $autoModified = true;

    /**
     * Field name to use to the modified time column in the DB table.
     *
     * @var string
     *
     * @access protected
     */
    protected $modifiedField = 'modified_on';

    /**
     * Various callbacks available to the Class.
     * They are simple lists of method names (methods will be ran on $this).
     */
    protected $beforeFind   = array();
    protected $afterFind    = array();
    protected $beforeInsert = array();
    protected $afterInsert  = array();
    protected $beforeUpdate = array();
    protected $afterUpdate  = array();
    protected $beforeDelete = array();
    protected $afterDelete  = array();

    protected $callbackParams = array();

    /**
     * By default, we return items as objects. You can change this for the entire class by setting this
     * value to 'array' instead of 'object', or you can specify a qualified Class name for the returned object.
     * Alternatively, you can do it on a per-instance basis using the 'as_array()' and 'as_object()' methods.
     */
    protected $returnType = 'object';

    protected $tempReturnType = null;

    /**
     * Temporary select's WHERE attributes.
     */
    protected $tempWheres = array();

    /**
     * Temporary select's ORDER attribute.
     */
    protected $selectOrder = null;

    /**
     * Temporary select's LIMIT attribute.
     */
    protected $selectLimit = null;

    /**
     * Temporary select's OFFSET attribute.
     */
    protected $selectOffset = null;

    /**
     * Protected, non-modifiable attributes.
     */
    protected $protectedFields = array();

    /**
     * The InputFilter instance.
     */
    protected $inputFilter = null;

    /**
     * Optionally skip the validation.
     * Used in conjunction with skipValidation() to skip data validation for any future calls.
     */
    protected $skipValidation = false;

    /**
     * An array of validation rules.
     * This needs to be the same format as validation rules passed to the Validator helper.
     */
    protected $validateRules = array();

    /**
     * An array of extra rules to add to validation rules during inserts only.
     * Often used for adding 'required' rules to fields on insert, but not updates.
     *
     * array( 'username' => 'required' );
     *
     * @var array
     */
    protected $validateInsertRules = array();

    /**
     * The InputFilter's Error Messages will be stored there, while executing validation.
     *
     * @var array
     */
    protected $errors = array();

    /**
     * This can be set to avoid a database call if using $this->prepareData().
     *
     * @var Array Columns for the Model's database fields.
     */
    protected $fields = array();

    //--------------------------------------------------------------------

    /**
     * Constructor
     */
    public function __construct($connection = null, $inputFilter = null)
    {
        parent::__construct($connection);

        // Always protect our fields
        array_unshift($this->beforeInsert, 'protectFields');
        array_unshift($this->beforeUpdate, 'protectFields');

        //
        // Check our auto-set features and make sure they are part of our Observer System.

        if ($this->autoCreated === true) {
            array_unshift($this->beforeInsert, 'createdOn');
        }

        if ($this->autoModified === true) {
            array_unshift($this->beforeUpdate, 'modifiedOn');
        }

        // Do we have a Validator instance?
        if ($inputFilter instanceof InputFilter) {
            $this->inputFilter = $inputFilter;
        } else {
            $this->inputFilter = new InputFilter();
        }

        // Make sure our temp return type is correct.
        $this->tempReturnType = $this->returnType;
    }

    //--------------------------------------------------------------------
    // QueryBuilder Methods
    //--------------------------------------------------------------------

    public function queryBuilder(FluentStructure $structure = null)
    {
        if ($structure === null) {
            $structure = new FluentStructure($this->primaryKey);
        }

        $this->db->countIncomingQuery();

        // Get a QueryBuilder instance.
        return $this->db->getQueryBuilder($structure);
    }

    public function buildQuery($method, $param = null)
    {
        $returnType = $this->tempReturnType;

        // Make sure our temp return type is correct.
        $this->tempReturnType = $this->returnType;

        // Get a QueryBuilder instance.
        $queryBuilder = $this->queryBuilder();

        // First, check and configure for the 'select' Method.
        if ($method == 'select') {
            $query = $queryBuilder->from($this->table(), $param);

            // Setup the fetch Method.
            if ($returnType == 'array') {
                $object = false;
            } else if ($returnType == 'object') {
                $object = true;
            } else {
                $object = $returnType;

                // Check for a valid className.
                $classPath = str_replace('\\', '/', ltrim($returnType, '\\'));

                if (! preg_match('#^App(?:/Modules/.+)?/Models/Entities/(.*)$#i', $classPath)) {
                    throw new \Exception(__('No valid Entity Name is given: {0}', $returnType));
                }

                if (! class_exists($returnType)) {
                    throw new \Exception(__('No valid Entity Class is given: {0}', $returnType));
                }
            }

            return $query->asObject($object);
        }

        // Then, configure the other Query building Methods.
        if ($method == 'insert') {
            $param = is_array($param) ? $param : array();

            $query = $queryBuilder->insertInto($this->table(), $param);
        } else if ($method == 'update') {
            $query = $queryBuilder->update($this->table(), $param);
        } else if ($method == 'delete') {
            $query = $queryBuilder->delete($this->table(), $param);
        } else {
            throw new \Exception(__('No valid Method given for Query building'));
        }

        return $query;
    }

    //--------------------------------------------------------------------
    // CRUD Support Methods
    //--------------------------------------------------------------------

    /**
     * A simple way to grab the first result of a search only.
     */
    public function first()
    {
        $result = $this->limit(1)->findAll();

        if (is_array($result) && (count($result) > 0)) {
            return array_shift($result);
        }

        return $result;
    }

    /**
     * Finds a single record based on it's primary key.
     *
     * @param mixed $id The primaryKey value of the object to retrieve.
     *
     * @return object
     */
    public function find($id)
    {
        if (! is_integer($id)) {
            throw new \UnexpectedValueException(__('Parameter should be an Integer'));
        }

        //
        $this->trigger('beforeFind', array('id' => $id, 'method' => 'find'));

        // Prepare the SQL Query.
        $sql = "SELECT * FROM " .$this->table() ." WHERE " .$this->primaryKey ." = :value";

        // Build and process the Query.
        $result = $this->buildQuery('select')->where($this->primaryKey, $id)->fetch();

        if (! empty($result)) {
            $result = $this->trigger('afterFind', array('id' => $id, 'method' => 'find', 'fields' => $result));
        }

        // Reset the Model State.
        $this->resetState();

        return $result;
    }

    /**
     * Fetch a single record based on an arbitrary WHERE call.
     *
     * @return object
     */
    public function findBy()
    {
        // Prepare the WHERE parameters.
        $params = func_get_args();

        $where = $this->setWhere($params);

        //
        $this->trigger('beforeFind', array('method' => 'findBy', 'fields' => $where));

        // Build and process the Query.
        $result = $this->buildQuery('select')->where($where)->fetch();

        if (! empty($result)) {
            $result = $this->trigger('afterFind', array('method' => 'findBy', 'fields' => $result));
        }

        // Reset the Model State.
        $this->resetState();

        return $result;
    }

    /**
     * Retrieves a number of items based on an array of primary_values passed in.
     *
     * @param  array $values An array of primary key values to find.
     *
     * @return object or FALSE
     */
    public function findMany($values)
    {
        if (! is_array($values) || empty($values)) {
            throw new \UnexpectedValueException(__('Parameter should be a non empty Array'));
        }

        // Build and process the Query.
        $query = $this->buildQuery('select')->where($this->primaryKey, $values);

        $query = ($this->selectOrder === null) ? $query : $query->orderBy($this->selectOrder);

        // Fetch the result.
        $result = $query->fetchAll();

        // Reset the Model State.
        $this->resetState();

        return $result;
    }

    /**
     * Retrieves a number of items based on an arbitrary WHERE call.
     *
     * @return object or FALSE
     */
    public function findManyBy()
    {
        $params = func_get_args();

        $this->setWhere($params);

        return $this->findAll();
    }

    /**
     * Fetch all of the records in the table.
     * Can be used with scoped calls to restrict the results.
     *
     * @return object or FALSE
     */
    public function findAll()
    {
        // Prepare the WHERE details.
        $where = $this->wheres();

        //
        $this->trigger('beforeFind', array('method' => 'findAll', 'fields' => $where));

        // Build and process the Query.
        $query = $this->buildQuery('select')->where($where);

        $query = ($this->selectLimit === null)  ? $query : $query->limit($this->selectLimit);

        $query = ($this->selectOffset === null) ? $query : $query->offset($this->selectOffset);

        $query = ($this->selectOrder === null)  ? $query : $query->orderBy($this->selectOrder);

        // Fetch the result.
        $result = $query->fetchAll();

        if (is_array($result)) {
            foreach ($result as $key => &$row) {
                $row = $this->trigger('afterFind', array('method' => 'findAll', 'fields' => $row));
            }
        }

        // Reset the Model State.
        $this->resetState();

        return $result;
    }

    /**
     * Inserts data into the database.
     *
     * @param array $data An array of key/value pairs to insert to database.
     *
     * @return mixed The primaryKey value of the inserted record, or FALSE.
     */
    public function insert($data, $skipValidation = null)
    {
        $skipValidation = is_null($skipValidation) ? $this->skipValidation : $skipValidation;

        if ($skipValidation === false) {
            $data = $this->validate($data, 'insert', $skipValidation);
        }

        //
        $result = false;

        // Will be false if it didn't validate.
        if ($data !== false) {
            $data = $this->trigger('beforeInsert', array('method' => 'insert', 'fields' => $data));

            // Prepare the Data.
            $data = $this->prepareData($data);

            // Execute the INSERT.
            $result = $this->db->insert($this->table(), $data);

            if ($result !== false) {
                $this->trigger('afterInsert', array('id' => $result, 'fields' => $data, 'method' => 'insert'));
            }
        }

        // Reset the Model State.
        $this->resetState();

        return $result;
    }

    /**
     * Performs the SQL standard for a combined DELETE + INSERT, using PRIMARY and UNIQUE keys to
     * determine which row to replace.
     *
     * @param $data
     *
     * @return bool
     */
    public function replace($data, $skipValidation = null)
    {
        $skipValidation = is_null($skipValidation) ? $this->skipValidation : $skipValidation;

        if ($skipValidation === false) {
            $data = $this->validate($data, 'insert', $skipValidation);
        }

        //
        $result = false;

        // Will be false if it didn't validate.
        if ($data !== false) {
            // Prepare the Data.
            $data = $this->prepareData($data);

            // Execute the REPLACE.
            $result = $this->db->replace($this->table(), $data);

            if ($result !== false) {
                $this->trigger('afterInsert', array('id' => $id, 'fields' => $data, 'method' => 'replace'));
            }
        }

        // Reset the Model State.
        $this->resetState();

        return $result;
    }

    /**
     * Updates an existing record in the database.
     *
     * @param mixed $id   The primaryKey value of the record to update.
     * @param array $data An array of value pairs to update in the record.
     *
     * @return bool
     */
    public function update($id, $data, $skipValidation = null)
    {
        $skipValidation = is_null($skipValidation) ? $this->skipValidation : $skipValidation;

        if ($skipValidation === false) {
            $data = $this->validate($data, 'update', $skipValidation);
        }

        //
        $result = false;

        // Will be false if it didn't validate.
        if ($data !== false) {
            $data = $this->trigger('beforeUpdate', array('id' => $id, 'method' =>'update', 'fields' => $data));

            // Prepare the Data.
            $data = $this->prepareData($data);

            // Build and process the Query.
            $result = $this->buildQuery('update')->set($data)->where($this->primaryKey, $id)->execute();

            $result = $this->trigger('afterUpdate', array('id' => $id, 'method' => 'update', 'fields' => $data, 'result' => $result));
        }

        // Reset the Model State.
        $this->resetState();

        return $result;
    }

    /**
     * Updates many records by an array of ids.
     *
     * While updateBatch() allows modifying multiple, arbitrary rows of data on each row,
     * updateMany() sets the same values for each row.
     *
     * $ids = array(1, 2, 3, 5, 12);
     *
     * $data = array(
     *     'deleted_by' => 1
     * );
     *
     * $this->model->updateMany($ids, $data);
     *
     * @param array $ids  An array of primaryKey values to update.
     * @param array $data An array of value pairs to modify in each row.
     *
     * @return bool
     */
    public function updateMany($ids, $data, $skipValidation = null)
    {
        if (! is_array($ids) || (count($ids) == 0)) {
            return null;
        }
        $skipValidation = is_null($skipValidation) ? $this->skipValidation : $skipValidation;

        if ($skipValidation === false) {
            $data = $this->validate($data, 'update', $skipValidation);
        }

        //
        $result = false;

        $data = $this->trigger('beforeUpdate', array('ids' => $ids, 'method' => 'updateMany', 'fields' => $data));

        // Will be false if it didn't validate.
        if ($data !== false) {
            // Prepare the Data.
            $data = $this->prepareData($data);

            // Build and process the Query.
            $result = $this->buildQuery('update')->set($data)->where($this->primaryKey, $ids)->execute();

            $this->trigger('afterUpdate', array('ids' => $ids, 'fields' => $data, 'result' => $result, 'method' => 'updateMany'));
        }

        // Reset the Model State.
        $this->resetState();

        return $result;
    }

    /**
     * Update records in the database using a standard WHERE clause.
     *
     * Your last parameter should be the $data array with values to update on the rows.
     * Any additional parameters should be provided to make up a typical WHERE clause.
     * This could be a single array, or a column name and a value.
     *
     * $data = array('deleted_by' => 1);
     * $wheres = array('user_id' => 15);
     *
     * $this->updateBy($wheres, $data);
     * $this->updateBy('user_id', 15, $data);
     *
     * @param array $data An array of data pairs to update.
     * @param             One or more WHERE-acceptable entries.
     *
     * @return bool
     */
    public function updateBy()
    {
        $params = func_get_args();

        $data = array_pop($params);

        if (empty($params) || empty($data)) {
            throw new \UnexpectedValueException(__('Invalid parameters'));
        }

        // Prepare the WHERE parameters.
        $where = $this->setWhere($params);

        //
        $result = false;

        $data = $this->trigger('beforeUpdate', array('method' => 'updateBy', 'fields' => $data));

        // Will be false if it didn't validate.
        if (($data = $this->validate($data)) !== false) {
            // Prepare the Data.
            $data = $this->prepareData($data);

            // Build and process the Query.
            $result = $this->buildQuery('update')->set($data)->where($where)->execute();

            $this->trigger('afterUpdate', array('method' => 'updateBy', 'fields' => $data, 'result' => $result));
        }

        // Reset the Model State.
        $this->resetState();

        return $result;
    }

    /**
     * Updates all records and sets the value pairs passed in the array.
     *
     * @param array $data An array of value pairs with the data to change.
     *
     * @return bool
     */
    public function updateAll($data, $skipValidation = null)
    {
        $data = $this->trigger('beforeUpdate', array('method' => 'updateAll', 'fields' => $data));

        $skipValidation = is_null($skipValidation) ? $this->skipValidation : $skipValidation;

        if ($skipValidation === false) {
            $data = $this->validate($data, 'update', $skipValidation);
        }

        //
        $result = false;

        // Will be false if it didn't validate.
        if ($data !== false) {
            // Prepare the Data.
            $data = $this->prepareData($data);

            // Build and process the Query.
            $result = $this->buildQuery('update')->set($data)->execute();

            $this->trigger('afterUpdate', array('method' => 'updateAll', 'fields' => $data, 'result' => $result));
        }

        // Reset the Model State.
        $this->resetState();

        return $result;
    }

    /**
     * Increments the value of field for a given row, selected by the primary key for the table.
     *
     * @param     $id
     * @param     $field
     * @param int $value
     *
     * @return mixed
     */
    public function increment($id, $field, $value = 1)
    {
        $value = (int) abs($value);

        //
        $data = array($field => "{$field}+{$value}");

        // Build and process the Query.
        return $this->buildQuery('update')->set($data)->where($this->primaryKey, $id)->execute();
    }

    /**
     * Decrements the value of field for a given row, selected by the primary key for the table.
     *
     * @param     $id
     * @param     $field
     * @param int $value
     *
     * @return mixed
     */
    public function decrement($id, $field, $value = 1)
    {
        $value = (int) abs($value);

        //
        $data = array($field => "{$field}-{$value}");

        // Build and process the Query.
        return $this->buildQuery('update')->set($data)->where($this->primaryKey, $id)->execute();
    }

    /**
     * Deletes a row by it's primary key value.
     *
     * @param mixed $id The primary key value of the row to delete.
     *
     * @return bool
     */
    public function delete($id)
    {
        if (! is_integer($id)) {
            throw new \UnexpectedValueException(__('Parameter should be an Integer'));
        }

        //
        $this->trigger('beforeDelete', array('id' => $id, 'method' => 'delete'));

        // Build and process the Query.
        $result = $this->buildQuery('delete')->where($this->primaryKey, $id)->execute();

        $this->trigger('afterDelete', array('id' => $id, 'method' => 'delete', 'result' => $result));

        return $result;
    }

    public function deleteBy()
    {
        $params = func_get_args();

        if (empty($params)) {
            throw new \UnexpectedValueException(__('Invalid parameters'));
        }

        // Prepare the WHERE parameters.
        $where = $this->setWhere($params);

        //
        $where = $this->trigger('beforeDelete', array('method' => 'deleteBy', 'fields' => $where));

        // Build and process the Query.
        $result = $this->buildQuery('delete')->where($where)->execute();

        $this->trigger('afterDelete', array('method' => 'deleteBy', 'fields' => $where, 'result' => $result));

        // Reset the Model State.
        $this->resetState();

        return $result;
    }

    public function deleteMany($ids)
    {
        if (! is_array($ids) || (count($ids) == 0)) {
            return null;
        }

        $ids = $this->trigger('beforeDelete', array('ids' => $ids, 'method' => 'deleteMany'));

        // Build and process the Query.
        $result = $this->buildQuery('delete')->where($this->primaryKey, $ids)->execute();

        $this->trigger('afterDelete', array('ids' => $ids, 'method' => 'deleteMany', 'result' => $result));

        return $result;
    }

    //--------------------------------------------------------------------
    // Scope Methods
    //--------------------------------------------------------------------

    /**
     * Temporarily sets our return type to an array.
     */
    public function asArray()
    {
        $this->tempReturnType = 'array';

        return $this;
    }

    /**
     * Temporarily sets our return type to an object.
     *
     * If $class is provided, the rows will be returned as objects that
     * are instances of that class. $class MUST be an fully qualified
     * class name, meaning that it must include the namespace, if applicable.
     *
     * @param string $class
     *
     * @return $this
     */
    public function asObject($className = null)
    {
        if ($className !== null) {
            $this->tempReturnType = $className;
        } else {
            $this->tempReturnType = 'object';
        }

        return $this;
    }

    //--------------------------------------------------------------------
    // Built-in Query Building Methods
    //--------------------------------------------------------------------

    /*
        The FluentPDO based QueryBuilder accept the following WHERE styles:

        where("field", "x");                           // Translated to field = 'x'
        where("field", null);                          // Translated to field IS NULL
        where(null);                                   // Reset clause and remove all previous defined conditions.
        where("field", array("x", "y"));               // Translated to field IN ('x', 'y')
        where("field > ?", "x");                       // Bound by PDO
        where("field > :name", array(':name' => 'x')); // Bound by PDO
        where(array("field1" => "value1", ...));       // Translated to field1 = 'value1' AND ...

        Then, to have a compatible WHERE method in the Model, we just need an array to keep the WHEREs and
        to optionally process the array given as the first argument, otherwise literally storing the parameters.
     */
    public function where($condition, $value = null)
    {
        $params = func_get_args();

        $this->setWhere($params);

        return $this;
    }

    public function limit($limit)
    {
        if (! is_integer($limit) || ($limit < 0)) {
            throw new \UnexpectedValueException(__('Invalid parameter'));
        }

        $this->selectLimit  = $limit;

        return $this;
    }

    public function offset($offset)
    {
        if (! is_integer($offset) || ($offset < 0)) {
            throw new \UnexpectedValueException(__('Invalid parameter'));
        }

        $this->selectOffset = $offset;

        return $this;
    }

    public function orderBy($order)
    {
        if (! is_string($order) || empty($order)) {
            throw new \UnexpectedValueException(__('Invalid parameter'));
        }

        $this->selectOrder = $order;

        return $this;
    }

    //--------------------------------------------------------------------
    // Utility Methods
    //--------------------------------------------------------------------

    /**
     * Counts number of rows modified by an arbitrary WHERE call.
     *
     * @return INT
     */
    public function countBy()
    {
        $params = func_get_args();

        if (empty($params)) {
            throw new \UnexpectedValueException(__('Invalid parameters'));
        }

        $where = $this->setWhere($params);

        // Build and process the Query.
        return $this->buildQuery('select')->select($this->primaryKey)->where($where)->count();
    }

    /**
     * Counts total number of records, disregarding any previous conditions.
     *
     * @return int
     */
    public function countAll()
    {
        // Build and process the Query.
        return $this->buildQuery('select')->select($this->primaryKey)->where($this->wheres())->count();
    }

    /**
     * Checks whether a field/value pair exists within the table.
     *
     * @param string $field  The field to search for.
     * @param string $value  The value to match $field against.
     * @param string $ignore Optionally, the ignored primaryKey.
     *
     * @return bool TRUE/FALSE
     */
    public function isUnique($field, $value, $ignoreId = null)
    {
        // Build and process the Query.
        $query = $this->buildQuery('select')->where($field, $value);

        $query = ($ignoreId === null) ? $query : $query->where($this->primaryKey .' != ?', $ignoreId);

        $data = $query->fetchAll();


        if (is_array($data) && (count($data) == 0)) {
            return true;
        }

        return true;
    }

    /**
     * Getter for the table name.
     *
     * @return string The name of the table used by this class (including the DB_PREFIX).
     */
    public function table($table = null)
    {
        if ($table !== null) {
            // A custom Table Name is wanted.
            return DB_PREFIX .$table;
        }

        return DB_PREFIX .$this->table;
    }

    /**
     * Adds a field to the protectedFields array.
     *
     * @param $field
     *
     * @return mixed
     */
    public function protect($field)
    {
        if (empty($field)) {
            throw new \UnexpectedValueException(__('Invalid parameter'));
        }

        $this->protectedFields[] = $field;

        return $this;
    }

    /**
     * Protect attributes by removing them from $row array.
     * Useful for removing id, or submit buttons names if you simply throw your $_POST array at your model. :)
     *
     * @param object|array $row The value pair item to remove.
     */
    public function protectFields(array $row)
    {
        if (! empty($row)) {
            foreach ($this->protectedFields as $field) {
                if (is_object($row)) {
                    unset($row->$field);
                } else {
                    unset($row[$field]);
                }
            }
        }

        return $row;
    }

    /**
     * Get the field names for this Model's table.
     *
     * Returns the model's database fields stored in $this->fields if set, else it tries to retrieve
     * the field list from $this->db->listFields($this->table()).
     *
     * @return array Returns the database fields for this Model.
     */
    public function tableFields()
    {
        if (empty($this->fields)) {
            $this->fields = $this->db->listColumns($this->table());
        }

        if (empty($this->fields)) {
            throw new \UnexpectedValueException(__('Cannot initialize the Table Fields'));
        }

        return $this->fields;
    }

    /**
     * Execute Select Query, binding values into the $sql Query.
     *
     * @param string $sql
     * @param array  $params
     * @param bool   $fetchAll   Ask the method to fetch all the records or not.
     *
     * @return array|null
     *
     * @throws \Exception
     */
    public function select($sql, $params = array(), $paramTypes = array(), $fetchAll = false)
    {
        // Firstly, simplify the white spaces and trim the SQL query.
        $sql = preg_replace('/\s+/', ' ', trim($sql));

        $result = $this->db->select($sql, $params, $paramTypes, $this->tempReturnType, $fetchAll);

        // Make sure our temp return type is correct.
        $this->tempReturnType = $this->returnType;

        return $result;
    }

    public function query($sql)
    {
        // Firstly, simplify the white spaces and trim the SQL query.
        $sql = preg_replace('/\s+/', ' ', trim($sql));

        $result = $this->db->rawQuery($sql, $this->tempReturnType);

        // Make sure our temp return type is correct.
        $this->tempReturnType = $this->returnType;

        return $result;
    }

    public function prepare($sql, $params = array(), $paramTypes = array())
    {
        // Firstly, simplify the white spaces and trim the SQL query.
        $sql = preg_replace('/\s+/', ' ', trim($sql));

        return $this->db->rawPrepare($sql, $params, $paramTypes);
    }

    //--------------------------------------------------------------------
    // Validation
    //--------------------------------------------------------------------

    /**
     * Validates the data passed into it based upon the Validator Rules setup in the $this->validateRules property.
     *
     * If $type == 'insert', any additional rules in the class var $insert_validate_rules
     * for that field will be added to the rules.
     *
     * @param array  $data An array of Validation Rules.
     * @param string $type Either 'update' or 'insert'.
     *
     * @return array|bool The original data or FALSE.
     */
    public function validate($data, $type = 'update', $skipValidation = null)
    {
        $skipValidation = is_null($skipValidation) ? $this->skipValidation : $skipValidation;

        if ($skipValidation) {
            return $data;
        }

        if (! empty($this->validateRules) && is_array($this->validateRules)) {
            $inputFilter =& $this->inputFilter;

            // Set the Input Filter Rules for Validation and Filtering.
            $inputFilter->setRules($this->validateRules);

            // Any insert additions?
            if (($type == 'insert') && is_array($this->validateInsertRules)) {
                $validator = $inputFilter->getValidator();

                foreach ($this->validateRules as $field => $row) {
                    if (isset($this->validateInsertRules[$field])) {
                        $validator->add($field, $this->validateInsertRules[$field]);
                    }
                }
            }

            // Populate the InputFilter instance with given Data.
            $inputFilter->populate($data);

            // Execute the Data Validation.
            if (! $inputFilter->isValid()) {
                // Something was wrong; store the current Filter's Error Messages and return false.
                $this->errors = $inputFilter->getErrors();

                return false;
            }

            // Valid Data given; clear the Error Messages and return the Filtered Values.
            $this->errors = array();

            return $inputFilter->getValues();
        }

        return $data;
    }

    public function validation()
    {
        return $this->validator;
    }

    //--------------------------------------------------------------------
    // Observers
    //--------------------------------------------------------------------

    /**
     * Sets the created_on date for the object based on the current date/time and dateFormat.
     * Will not overwrite an existing field.
     *
     * @param array $row The array of data to be inserted.
     *
     * @return array
     */
    public function createdOn($row)
    {
        if (! is_array($row) || empty($row['fields'])) {
            return null;
        }

        $row = $row['fields'];

        // created_on

        if (is_array($row) && ! array_key_exists($this->createdField, $row)) {
            $row[$this->createdField] = $this->date();
        }

        return $row;
    }

    /**
     * Sets the modified_on date for the object based on the current date/time and dateFormat.
     * Will not overwrite an existing field.
     *
     * @param array $row The array of data to be inserted.
     *
     * @return array
     */
    public function modifiedOn($row)
    {
        if (! is_array($row) || empty($row['fields'])) {
            return null;
        }

        $row = $row['fields'];

        // modified_on

        if (is_array($row) && ! array_key_exists($this->modifiedField, $row)) {
            $row[$this->modifiedField] = $this->date();
        }

        return $row;
    }

    //--------------------------------------------------------------------
    // Internal Methods
    //--------------------------------------------------------------------

    /**
     * Extracts the Model's fields (except the key and those handled by Observers) from the $postData
     * and returns an array of name => value pairs.
     *
     * @param array $postData Usually the POST data, when called from the Controller.
     *
     * @return array An array of name => value pairs containing the data for the Model's fields.
     */
    public function prepareData($postData)
    {
        if (empty($postData)) {
            return array();
        }

        $data = array();

        $skippedFields = array();

        // Though the model doesn't support multiple keys well, $this->key could be an array or a string...
        $skippedFields = array_merge($skippedFields, (array) $this->primaryKey);

        // Remove any protected attributes
        $skippedFields = array_merge($skippedFields, $this->protectedFields);

        $fields = $this->tableFields();

        // If the field is the primary key, one of the created/modified/deleted fields,
        // or has not been set in the $postData, skip it.
        foreach ($postData as $field => $value) {
            if (in_array($field, $skippedFields) || ! in_array($field, $fields)) {
                continue;
            }

            $data[$field] = $value;
        }

        return $data;
    }

    /**
     * Triggers a model-specific event and call each of it's Observers.
     *
     * @param string $event The name of the event to trigger.
     * @param mixed  $data  The data to be passed to the callback functions.
     *
     * @return mixed
     */
    public function trigger($event, $data = false)
    {
        if (! isset($this->$event) || ! is_array($this->$event)) {
            if (isset($data['fields'])) {
                return $data['fields'];
            }

            return $data;
        }

        foreach ($this->$event as $method) {
            if (strpos($method, '(') !== false) {
                preg_match('/([a-zA-Z0-9\_\-]+)(\(([a-zA-Z0-9\_\-\., ]+)\))?/', $method, $matches);

                $this->callbackParams = explode(',', $matches[3]);
            }

            $data = call_user_func_array(array($this, $method), array($data));
        }

        // In case no method called or method returned the entire data array, we typically just need the $fields.
        if (isset($data['fields'])) {
            return $data['fields'];
        }

        // A few methods might need to return 'ids'.
        if (isset($data['ids'])) {
            return $data['ids'];
        }

        return $data;
    }

    /**
     * A utility function to allow child models to use the type of date/time format that they prefer.
     * This is primarily used for setting created_on and modified_on values, but can be used by inheriting classes.
     *
     * The available time formats are:
     * * 'int'      - Stores the date as an integer timestamp.
     * * 'datetime' - Stores the date and time in the SQL datetime format.
     * * 'date'     - Stores the date (only) in the SQL date format.
     *
     * @param mixed $userDate An optional PHP timestamp to be converted.
     *
     * @access protected
     *
     * @return int|null|string The current/user time converted to the proper format.
     */
    protected function date($userDate = null)
    {
        $curr_date = ! empty($userDate) ? $userDate : time();

        switch ($this->dateFormat) {
            case 'int':
                return $curr_date;
                break;
            case 'datetime':
                return date('Y-m-d H:i:s', $curr_date);
                break;
            case 'date':
                return date('Y-m-d', $curr_date);
                break;
        }
    }

    protected function resetState()
    {
        // Reset our select WHEREs
        $this->tempWheres = array();

        // Reset our select ORDER
        $this->selectOrder = null;

        // Reset our select LIMIT
        $this->selectLimit = null;

        // Reset our select OFFSET
        $this->selectOffset = null;
    }

    protected function setWhere(array $params)
    {
        if (empty($params)) {
            $this->tempWheres;
        }

        // Get the WHERE condition.
        $condition = array_shift($params);

        if ($condition == null) {
            // Remove all previous defined conditions from our own WHEREs array, too.
            $this->tempWheres = array();
        } else if (is_array($condition)) {
            // Is given an array of Conditions; merge them into our own WHEREs array.
            $this->tempWheres = array_merge($this->tempWheres, $condition);
        } else if (count($params) == 1) {
            // Store the condition and its value.
            $this->tempWheres[$condition] = array_shift($params);
        } else if (count($params) == 2) {
            $operator = array_shift($params);

            if (! in_array($operator, Connection::$whereOperators, true)) {
                throw new \UnexpectedValueException(__('Second parameter is invalid'));
            }

            $condition = sprintf('%s $s ?', $condition, $operator);

            // Store the composed condition and its value.
            $this->tempWheres[$condition] = array_shift($params);
        } else {
            throw new \UnexpectedValueException(__('Invalid number of parameters'));
        }

        return $this->tempWheres;
    }

    protected function wheres()
    {
        return $this->tempWheres;
    }
}
