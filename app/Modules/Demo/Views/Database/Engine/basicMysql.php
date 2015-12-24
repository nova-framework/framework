
<div class="page-header">
    <h1><?= $title ?></h1>
</div>

<h2>Demo #1, How to use a database engine. Get one first!</h2>
<p>In our config we have declared several prepared links, we gave them names. Example of configuration, configured in <kbd>app/Config/config.php</kbd></p>

<h4>Configuration</h4>
<pre>Config::set('database', array(
    'default' => array(
        'engine' => 'mysql',
        'config' => array(
            'host'          => 'localhost',
            'port'          => 3306, // Not required, default is 3306
            'database'      => 'dbname',
            'username'      => 'root',
            'password'      => 'password',
            'fetch_method'  => \PDO::FETCH_OBJ, // Not required, default is OBJ.
            'charset'       => 'utf8' // Not required, default and recommended is utf8.
        )
    ),
    /** Extra connections can be added here, some examples: */
    'sqlite' => array(
        'engine' => 'sqlite',
        'config' => array(
            'file'          => 'database.sqlite',
            'fetch_method'  => \PDO::FETCH_OBJ // Not required, default is OBJ.
        )
    )

));</pre>

<h4>Usage of links</h4>
<p>
    To use links and access the data sources you need to get an instance of your link connection.
    First you need to know the link name you want to use. For example, we will use the 'default' link here and initiate it:
</p>
<pre>$engine = \Nova\Database\Manager::getEngine(); // Leaving the parameter empty is the same as using 'default' there.</pre>

<h4>Functions on the Engines:</h4>
<pre>function selectOne($sql, $bindParams = array(), $method = null, $class = null);
function selectAll($sql, $bindParams = array(), $method = null, $class = null);
function select($sql, $bindParams = array(), $fetchAll = false, $method = null, $class = null);

function insert($table, $data, $transaction = false, $multipleInserts = false);
function superInsert($table, $data, $transaction = false);

function update($table, $data, $where, $limit = 1);

function delete($table, $where, $limit = 1);

function truncate($table);

// And these are raw functions we could use to make special queries etc.
function rawPrepare($sql, $bind = array(), $method = null, $class = null);
public function raw($sql, $fetch = false);
public function rawQuery($sql);</pre>

<br><br>
<h2>Demo #2, Select Usage of the engine.</h2>
<p>How can we use it, now it's pretty easy. We have the following methods we could use:</p>

<h4>Example, select all cars from our demo database:</h4>
<p>For example we could fetch all our cars from our MySQL Database:</p>
<strong>Code</strong>
<pre>$cars = $engine->selectAll("SELECT * FROM " . DB_PREFIX . "car;");</pre>
<strong>Result</strong>
<pre><?php var_dump($demo2_example1); ?></pre>

<br><br>
<h2>Demo #3, Inserting data</h2>
<p>How to insert, also easy, just like this example:</p>

<strong>Code</strong>
<pre>$data = array('make' => 'BMW', 'model' => 'i8', 'costs' => 138000);
$carid = $engine->insert(DB_PREFIX . 'car', $data);

var_dump($carid);</pre>

<strong>Result</strong>
<pre><?php var_dump($demo3_example1); ?></pre>



<br><br>
<h2>Demo #4, Removing the last inserted BMW i8 (demo #3)</h2>

<strong>Code</strong>
<pre>$result = $engine->delete(DB_PREFIX . 'car', array('carid' => $carid));
var_dump($result)</pre>

<strong>Result</strong>
<pre><?php var_dump($demo4_example1); ?></pre>


<a class="btn btn-lg btn-success" href="<?= site_url('demos'); ?>">
    <?= __d('demo', 'Home'); ?>
</a>

