#!/usr/bin/env php
<?php
require_once 'vendor/autoload.php';

use Cli\ControllerCommand;
use Cli\ModelCommand;
use Symfony\Component\Console\Application;

$console = new Application('Nova Framework Command Line Interface for v3.0', '1.0.0');
$console->add(new ControllerCommand());
$console->add(new ModelCommand());
$console->run();
