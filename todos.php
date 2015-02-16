<?php

/**
 * A file that initializes and rund the console application
 *
 * (c) Łukasz Tarasiewicz <lukasz.tarasiewicz@polcode.net>
 *
 */

require "vendor/autoload.php";

use Todos\Config\DbConnect;
use Symfony\Component\Console\Application;
use Todos\Command\AddTaskCommand;
use Todos\Command\ShowTasksCommand;
use Todos\Command\MarkAsCompleteCommand;

$db = new DbConnect;
$entityManager = $db->getEntityManager();

$application = new Application('Todos', '0.1.0');
$application->add(new AddTaskCommand($entityManager));
$application->add(new ShowTasksCommand($entityManager));
$application->add(new MarkAsCompleteCommand($entityManager));
$application->run();
