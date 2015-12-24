
<div class="page-header">
    <h1><?= $title ?></h1>
</div>

<h2>Export of the demo database</h2>
<p>Import this in your MySQL Installation</p>

<strong>Create Database</strong>
<pre>CREATE DATABASE `dbname` /*!40100 DEFAULT CHARACTER SET utf8 */;</pre>

<br><br>
<strong>Create Table</strong>
<pre>CREATE TABLE `smvc_car` (
    `carid` int(11) NOT NULL AUTO_INCREMENT,
    `make` varchar(45) NOT NULL,
    `model` varchar(90) NOT NULL,
    `costs` double NOT NULL,
    PRIMARY KEY (`carid`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;</pre>

<br><br>
<strong>Insert Demo Data</strong>
<pre>INSERT INTO `dbname`.`smvc_car`
(
    `carid`,
    `make`,
    `model`,
    `costs`
) VALUES (
    NULL,
    'Tesla',
    'Model S',
    97000
);</pre>


<a class="btn btn-lg btn-success" href="<?= site_url('demos'); ?>">
    <?= __d('demo', 'Home'); ?>
</a>
