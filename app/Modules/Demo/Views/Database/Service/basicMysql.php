
<div class="page-header">
    <h1><?= $title ?></h1>
</div>

<h2>Demo #1, Define service and entity in your app</h2>
<p>To use the Services (DBAL) with your entities, you should define those first.
In our example here we are defining a Car entity and a Car service. Both are very simple. Sources:</p>

<p>In our config we have declared several prepared links, we gave them names. Example of configuration, configured in <kbd>app/Config/config.php</kbd></p>

<h4><kbd>app/Models/Entities/Car.php</kbd></h4>
<pre>namespace App\Models\Entities;

use Nova\Database\Entity;
class Car extends Entity
{
    public $carid;
    public $model;
    public $costs;
}</pre>

<h4><kbd>app/Services/Database/Car.php</kbd></h4>
<pre>
namespace App\Services\Database;

use Nova\Database\Service;
class Car extends Service
{
    public function __construct() {
        $this->table = "car"; // This will set the table, Without the PREFIX!
        $this->primaryKeys = array("carid"); // This will set the columns used for primary keys.
        $this->fetchMethod = \PDO::FETCH_CLASS; // This will set the fetch method.
        $this->fetchClass = '\App\Models\Entities\Car'; // Fetch into our Entity we created before.
    }
    // By default we have some functions already made, but we could extend this by creating more functions here:

    public function getAll()
    {
        return $this->read("SELECT * FROM " . DB_PREFIX . "car");
    }
}</pre>


<h2>Demo #2, Basic selecting</h2>
<p>Basic selecting is like selecting on an engine, only this will return an array with instances of your entity!</p>

<h4>Service Instance</h4>
<p>Before we can continue we need to get our Service instance. Use the Manager to get an instance:</p>
<pre>$carservice = \Nova\Database\Manager::getService('Car');</pre>


<h4>Selecting with our custom made getAll() function.</h4>
<p>We created our custom service function 'getAll()' which is a shorthand for a long query, this is exactly how we could use a DBAL service to nuke using SQL inside of your Controller</p>

<strong>Code</strong>
<pre><?php var_dump($demo2); ?></pre>

<h4>Functions on the Service by default:</h4>
<pre>public function create($entity);
public function read($sql, $bindParams = array());
public function update($entity, $limit = 1);
public function delete($entity, $limit = 1);</pre>

<br><br>
<h2>Demo #3, Creating an Car entity and save it</h2>
<p>Creating and saving a Car entity:</p>

<strong>Code</strong>
<pre>$car = new Car(); // Create our entity
$car->make = 'BMW';
$car->model = '1-serie';
$car->costs = 40000;

$carservice->create($car); // CREATE operation</pre>

<strong>Result</strong>
<pre><?php var_dump($demo3); ?></pre>


<br><br>
<h2>Demo #4, Removing the last inserted BMW 1-serie (from demo #3)</h2>

<strong>Code</strong>
<pre>$carservice->delete($car);</pre>

<strong>Result</strong>
<pre><?php var_dump($demo4); ?></pre>


<a class="btn btn-lg btn-success" href="<?= site_url('demos'); ?>">
    <?= __d('demo', 'Home'); ?>
</a>

