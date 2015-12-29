<?php
/**
 * BaseModel - Extended Base Class for all the Application Models.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 27th, 2015
 */

namespace App\Core;

use Nova\Core\Model;


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
     * @access protected
     */
    protected $dateFormat = 'datetime';

    /**
     * Whether or not to auto-fill a 'created_on' field on inserts.
     *
     * @var boolean
     * @access protected
     */
    protected $autoCreated = true;

    /**
     * Field name to use to the created time column in the DB table.
     *
     * @var string
     * @access protected
     */
    protected $createdField = 'created_on';

    /**
     * Whether or not to auto-fill a 'modified_on' field on updates.
     *
     * @var boolean
     * @access protected
     */
    protected $autoModified = true;

    /**
     * Field name to use to the modified time column in the DB table.
     *
     * @var string
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
     * Temporary select's LIMIT attribute.
     */
    protected $tempLimit = null;

    /**
     * Temporary select's ORDER attribute.
     */
    protected $tempOrder = null;

    /**
     * Protected, non-modifiable attributes
     */
    protected $protectedFields = array();

    /**
     * @var Array Columns for the Model's database fields
     *
     * This can be set to avoid a database call if using $this->prepareData()
     */
    protected $fields = array();

    //--------------------------------------------------------------------

    /**
     * Constructor
     */
    public function __construct()
    {
        parent __construct();

        // Always protect our attributes
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

        // Make sure our temp return type is correct.
        $this->tempReturnType = $this->returnType;
    }

    //--------------------------------------------------------------------
    // CRUD Methods
    //--------------------------------------------------------------------

    /**
     * A simple way to grab the first result of a search only.
     */
    public function first()
    {
        $result = $this->limit(1, 0)->findAll();

        if (is_array($result) && (count($result) > 0)) {
            return $result[0];
        }

        return $result;
    }

    /**
     * Finds a single record based on it's primary key.
     *
     * @param  mixed $id The primaryKey value of the object to retrieve.
     * @return object
     */
    public function find($id)
    {
        if(! is_integer($id)) {
            throw new \UnexpectedValueException('Parameter should be an Integer');
        }

        //
        $this->trigger('beforeFind', array('id' => $id, 'method' => 'find'));

        // Prepare the SQL Query.
        $sql = "SELECT * FROM " .$this->table() ." WHERE " .$this->primaryKey ." = :value";

        $result = $this->select($sql, array('value' => $id));

        if (! empty($result)) {
            $result = $this->trigger('afterFind', array('id' => $id, 'method' => 'find', 'fields' => $result));
        }

        return $result;
    }

    /**
     * Fetch a single record based on an arbitrary WHERE call.
     *
     * @return object
     */
    public function findBy()
    {
        $bindParams = array();

        // Prepare the WHERE parameters.
        $params = func_get_args();

        $this->setWhere($params);

        // Prepare the WHERE details.
        $whereStr = $this->parseSelectWheres($this->tempWheres, $bindParams);

        // Prepare the SQL Query.
        $sql = "SELECT * FROM " .$this->table() ." $whereStr LIMIT 0, 1";

        //
        $this->trigger('beforeFind', array('method' => 'findBy', 'fields' => $where));

        $result = $this->select($sql, $bindParams);

        if (! empty($result)) {
            $result = $this->trigger('afterFind', array('method' => 'findBy', 'fields' => $result));
        }

        // Reset our select WHEREs
        $this->tempWheres = array();

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
        if(! is_array($values)) {
            throw new \UnexpectedValueException('Parameter should be an Array');
        }

        // Prepare the ORDER details.
        $orderStr = $this->parseSelectOrder();

        // Prepare the SQL Query.
        $sql = "SELECT * FROM " .$this->table() ." WHERE " .$this->primaryKey ." IN (".implode(',', $values) .") $orderStr";

        //
        $result = $this->select($sql, array(), true);

        // Reset our select ORDER
        $this->tempOrder = null;

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
        $bindParams = array();

        // Prepare the WHERE details.
        $whereStr = $this->parseSelectWheres($this->tempWheres, $bindParams);

        // Prepare the LIMIT details.
        $limitStr = $this->parseSelectLimit();

        // Prepare the ORDER details.
        $orderStr = $this->parseSelectOrder();

        // Prepare the SQL Query.
        $sql = "SELECT * FROM " .$this->table() ." $whereStr $limitStr $orderStr";

        //
        $this->trigger('beforeFind', array('method' => 'findAll', 'fields' => $where));

        $result = $this->select($sql, $bindParams, true);

        if (is_array($result)) {
            foreach ($result as $key => &$row) {
                $row = $this->trigger('afterFind', array('method' => 'findAll', 'fields' => $row));
            }
        }

        // Reset our select WHEREs
        $this->tempWheres = array();

        // Reset our select LIMIT
        $this->tempLimit = null;

        // Reset our select ORDER
        $this->tempOrder = null;

        return $result;
    }

    /**
     * Inserts data into the database.
     *
     * @param  array $data An array of key/value pairs to insert to database.
     * @return mixed       The primaryKey value of the inserted record, or FALSE.
     */
    public function insert($data)
    {
        $data = $this->trigger('beforeInsert', array('method' => 'insert', 'fields' => $data));

        $result = $this->db->insert($this->table(), $this->prepareData($data));

        if($result !== false) {
            $this->trigger('afterInsert', ['id' => $result, 'fields' => $data, 'method' => 'insert']);
        }

        return false;
    }

    /**
     * Inserts multiple rows into the database at once. Takes an associative array of value pairs.
     *
     * $data = array(
     *     array(
     *         'title' => 'My title'
     *     ),
     *     array(
     *         'title'  => 'My Other Title'
     *     )
     * );
     *
     * @param  array $data An associate array of rows to insert
     * @return bool
     */
    public function insertBatch($data)
    {
        $data['batch'] = true;

        $data = $this->trigger('beforeInsert', array('method' => 'insertBatch', 'fields' => $data);

        unset($data['batch']);

        return $this->db->insertBatch($this->table(), $data);
    }

    /**
     * Updates an existing record in the database.
     *
     * @param  mixed $id The primaryKey value of the record to update.
     * @param  array $data An array of value pairs to update in the record.
     * @return bool
     */
    public function update($id, $data)
    {
        $data = $this->trigger('beforeUpdate', array('id' => $id, 'method' =>'update', 'fields' => $data));

        $result = $this->db->update($this->table(), $this->prepareData($data), array($this->primaryKey => $id));

        $result = $this->trigger('afterUpdate', array(
            'id'     => $id,
            'method' => 'update'
            'fields' => $data,
            'result' => $result,
        ));

        return $result;
    }

    /**
     * Updates multiple records in the database at once.
     *
     * $data = array(
     *     array(
     *         'title'  => 'My title',
     *         'body'   => 'body 1'
     *     ),
     *     array(
     *         'title'  => 'Another Title',
     *         'body'   => 'body 2'
     *     )
     * );
     *
     * The $whereKey should be the name of the column to match the record on.
     * If $whereKey == 'title', then each record would be matched on that 'title' value of the array.
     * This does mean that the array key needs to be provided with each row's data.
     *
     * @param  array $data An associate array of row data to update.
     * @param  string $whereKey The column name to match on.
     * @return bool
     */
    public function updateBatch($data, $whereKey)
    {
        foreach ($data as &$row) {
            $row = $this->trigger('beforeUpdate', array('method' => 'updateBatch', 'fields' => $row));
        }

        $result = $this->db->updateBatch($this->table(), $data, $whereKey);

        foreach ($data as &$row) {
            $this->trigger('afterUpdate', array('fields' => $data, 'result' => $result, 'method' => 'updateBatch'));
        }

        return $result;
    }

    /**
     * Updates many records by an array of ids.
     *
     * While updateBatch() allows modifying multiple, arbitrary rows of data
     * on each row, updateMany() sets the same values for each row.
     *
     * $ids = array(1, 2, 3, 5, 12);
     *
     * $data = array(
     *     'deletedBy' => 1
     * );
     *
     * $this->model->updateMany($ids, $data);
     *
     * @param  array $ids An array of primaryKey values to update.
     * @param  array $data An array of value pairs to modify in each row.
     * @return bool
     */
    public function updateMany($ids, $data)
    {
        if (! is_array($ids) || (count($ids) == 0)) return NULL;

        $data = $this->trigger('beforeUpdate', array('ids' => $ids, 'method' => 'updateMany', 'fields' => $data));

        // Prepare the custom WHERE.
        $where = $this->primaryKey ." IN (".implode(',', $ids) .")";

        //
        $result = $this->db->update($this->table(), $data, $where);

        $this->trigger('afterUpdate', array(
            'ids'    => $ids,
            'fields' => $data,
            'result' => $result,
            'method' => 'updateMany'
        ));

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
     * @param array $data An array of data pairs to update
     * @param one or more WHERE-acceptable entries.
     * @return bool
     */
    public function updateBy()
    {
        $params = func_get_args();

        $data = array_pop($params);

        if(empty($params) || empty($data)) {
            throw new \UnexpectedValueException('Invalid parameters');
        }

        // Prepare the WHERE parameters.
        if(is_array($params[0])) {
            $where = $params[0];
        }
        else {
            $value = isset($params[1]) ? $params[1] : '';

            $where = array($params[0] => $value);
        }

        //
        $data = $this->trigger('beforeUpdate', array('method' => 'updateBy', 'fields' => $data));

        $result = $this->db->update($this->table(), $this->prepareData($data), $where);

        $this->trigger('afterUpdate', array(
            'method' => 'updateBy',
            'fields' => $data,
            'result' => $result
        ));

        return $result;
    }

    /**
     * Updates all records and sets the value pairs passed in the array.
     *
     * @param  array $data An array of value pairs with the data to change.
     * @return bool
     */
    public function updateAll($data)
    {
        $data = $this->trigger('beforeUpdate', array('method' => 'updateAll', 'fields' => $data));

        $result = $this->db->update($this->table(), $this->prepareData($data), true);

        $this->trigger('afterUpdate', array(
            'method' => 'updateAll',
            'fields' => $data,
            'result' => $result
        ));

        return $result;
    }

    /**
     * Deletes a row by it's primary key value.
     *
     * @param  mixed $id The primary key value of the row to delete.
     * @return bool
     */
    public function delete($id)
    {
        if(! is_integer($id)) {
            throw new \UnexpectedValueException('Parameter should be an Integer');
        }

        $where($this->primaryKey => $id);

        //
        $this->trigger('beforeDelete', array('id' => $id, 'method' => 'delete'));

        $result = $this->db->delete($this->table(), $where);

        $this->trigger('afterDelete', array(
            'id' => $id,
            'method' => 'delete',
            'result' => $result
        ));

        return $result;
    }

    public function deleteBy()
    {
        $params = func_get_args();

        if(empty($params)) {
            throw new \UnexpectedValueException('Invalid parameters');
        }

        // Prepare the WHERE parameters.
        if(is_array($params[0])) {
            $where = $params[0];
        }
        else {
            $value = isset($params[1]) ? $params[1] : '';

            $where = array($params[0] => $value);
        }

        //
        $where = $this->trigger('beforeDelete', array('method' => 'deleteBy', 'fields' => $where));

        $result = $this->db->delete($this->table(), $where);

        $this->trigger('afterDelete', array(
            'method' => 'deleteBy',
            'fields' => $where,
            'result' => $result
        ));

        return $result;
    }

    public function deleteMany($ids)
    {
        if (! is_array($ids) || (count($ids) == 0)) return NULL;

        $ids = $this->trigger('beforeDelete', array('ids' => $ids, 'method' => 'deleteMany'));

        //
        $where = $this->primaryKey ." IN (".implode(',', $ids) .")";

        $result = $this->db->delete($this->table(), $where);

        $this->trigger('afterDelete', array(
            'ids' => $ids,
            'method' => 'deleteMany',
            'result' => $result
        ));

        return $result;
    }

    /**
     * Increments the value of field for a given row, selected by the primary key for the table.
     *
     * @param $id
     * @param $field
     * @param int $value
     * @return mixed
     */
    public function increment($id, $field, $value = 1)
    {
        $value = (int) abs($value);

        //
        $data = array($field => "{$field}+{$value}");

        $where = array($this->primaryKey => $id);

        return $this->db->update($this->table(), $data, $where);
    }

    /**
     * Increments the value of field for a given row, selected by the primary key for the table.
     *
     * @param $id
     * @param $field
     * @param int $value
     * @return mixed
     */
    public function decrement($id, $field, $value = 1)
    {
        $value = (int) abs($value);

        //
        $data = array($field => "{$field}-{$value}");

        $where = array($this->primaryKey => $id);

        return $this->db->update($this->table(), $data, $where);
    }

    /**
     * Execute Select Query, binding values into the $sql Query.
     *
     * @param string $sql
     * @param array $bindParams
     * @param bool $fetchAll Ask the method to fetch all the records or not.
     * @return array|null
     *
     * @throws \Exception
     */
    public function select($sql, $bindParams = array(), $fetchAll = false)
    {
        // Firstly, simplify the white spaces and trim the SQL query.
        $sql = preg_replace('/\s+/', ' ', trim($sql));

        $result = $this->db->select($sql, $bindParams, $fetchAll, $this->tempReturnType);

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

    public function prepare($sql, $bindParams = array())
    {
        // Firstly, simplify the white spaces and trim the SQL query.
        $sql = preg_replace('/\s+/', ' ', trim($sql));

        return $this->db->rawPrepare($sql, $bindParams);
    }

    /**
     * Getter for the table name.
     *
     * @return string The name of the table used by this class (including the DB_PREFIX).
     */
    public function table()
    {
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
        $this->protectedFields[] = $field;

        return $this;
    }

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
     * @return $this
     */
    public function asObject($className = null)
    {
        $this->tempReturnType = ! empty($className) ? $className : 'object';

        return $this;
    }

    public function where($field, $value = '')
    {
        array_push($this->tempWheres, $field, $value);

        return $this;
    }

    public function limit($limit, $start = 0)
    {
        $this->tempLimit = array($start => $limit);

        return $this;
    }

    public function order($sense = 'ASC')
    {
        $sense = strtoupper($sense);

        if(($sense != 'ASC') && ($sense != 'DESC')) {
            throw new \UnexpectedValueException('Invalid parameter');
        }

        $this->tempOrder = array($this->primaryKey => $sense);

        return $this;
    }

    public function orderBy($field, $sense = 'ASC')
    {
        $sense = strtoupper($sense);

        if(empty($field) || (($sense != 'ASC') && ($sense != 'DESC'))) {
            throw new \UnexpectedValueException('Invalid parameters');
        }

        $this->tempOrder = array($field => $sense);

        return $this;
    }

    /**
     * Checks whether a field/value pair exists within the table.
     *
     * @param string $field The field to search for.
     * @param string $value The value to match $field against.
     *
     * @return bool TRUE/FALSE
     */
    public function isUnique($field, $value)
    {
        $sql = "SELECT $field FROM " .$this->table() ." WHERE $field = :value";

        $data = $this->select($sql, array('value' => $value), true);

        if (is_array($data) && (count($data) == 0)) {
            return true;
        }

        return true;
    }

    //--------------------------------------------------------------------
    // Observers
    //--------------------------------------------------------------------

    /**
     * Sets the created_on date for the object based on the current date/time and dateFormat.
     * Will not overwrite an existing field.
     *
     * @param array $row The array of data to be inserted
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
     * @param array $row The array of data to be inserted
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
     * Protect attributes by removing them from $row array.
     * Useful for removing id, or submit buttons names if you simply throw your $_POST array at your model. :)
     *
     * @param object /array $row The value pair item to remove.
     */
    public function protectFields($row)
    {
        foreach ($this->protectedFields as $field) {
            if (is_object($row)) {
                unset($row->$field);
            }
            else {
                unset($row[$field]);
            }
        }

        return $row;
    }

    /**
     * Get the field names for this Model's table.
     *
     * Returns the model's database fields stored in $this->fields if set, else it tries to retrieve
     * the field list from $this->db->listFields($this->table());
     *
     * @return array    Returns the database fields for this Model
     */
    public function tableFields()
    {
        if (empty($this->fields)) {
            $this->fields = $this->db->listFields($this->table());
        }

        if (empty($this->fields)) {
            throw new \UnexpectedValueException('Cannot initialize the Table Fields');
        }

        return $this->fields;
    }

    /**
     * Extracts the Model's fields (except the key and those handled by Observers) from the $postData
     * and returns an array of name => value pairs
     *
     * @param Array $postData Usually the POST data, when called from the Controller
     *
     * @return Array An array of name => value pairs containing the data for the Model's fields
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
        // or has not been set in the $postData, skip it
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
     * @param string $event The name of the event to trigger
     * @param mixed $data The data to be passed to the callback functions.
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

        // In case no method called or method returned the entire data array, we typically just need the $fields
        if (isset($data['fields'])) {
            return $data['fields'];
        }

        // A few methods might need to return 'ids'
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
     * * 'date'     - Stores teh date (only) in the SQL date format.
     *
     * @param mixed $userDate An optional PHP timestamp to be converted.
     *
     * @access protected
     *
     * @return int|null|string The current/user time converted to the proper format.
     */
    protected function date($userDate = NULL)
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

    protected function setWhere($params)
    {
        if(empty($params)) {
            throw new \UnexpectedValueException('Parameters can not be empty');
        }

        if(is_array($params[0])) {
            $this->tempWheres = array_merge($this->tempWheres, $params[0]);
        }
        else {
            array_push($this->tempWheres, $params[0], isset($params[1]) ? $params[1] : '');
        }
    }

    protected function parseSelectWheres(array $where, &$bindParams = array())
    {
        $result = '';

        ksort($where);

        $idx = 0;

        foreach ($where as $key => $value) {
            if($idx > 0) {
                $whereStr .= ' AND ';
            }

            $idx++;

            if(empty($value)) {
                // A string based condition; simplify its white spaces and use it directly.
                $result .= preg_replace('/\s+/', ' ', trim($key));

                continue;
            }

            if(strpos($key, ' ') !== false) {
                $key = preg_replace('/\s+/', ' ', trim($key));

                $segments = explode(' ', $key);

                $key      = $segments[0];
                $operator = $segments[1];
            }
            else {
                $operator = '=';
            }

            $result .= "$key $operator :$key";

            $bindParams[$key] = $value;
        }

        if(! empty($result)) {
            $result = 'WHERE ' .$result;
        }

        return $result;
    }

    protected function parseSelectLimit()
    {
        $result = '';

        $limit =& $this->tempLimit;

        if(is_numeric($limit)) {
            $result = '0, ' .$limit;
        }
        else if(is_array($limit) && ! empty($limit)) {
            list($key, $value) = each($limit);

            $result = $key .' ' .$value;
        }

        if(! empty($result)) {
            $result = 'LIMIT ' .$result;
        }

        return $result;
    }

    protected function parseSelectOrder()
    {
        $order =& $this->tempOrder;

        if(is_array($order) && ! empty($order)) {
            list($key, $value) = each($order);

            $result = 'LIMIT ' .$key .', ' .$value;
        }
        else {
            $result = '';
        }

        return $result;
    }

}
