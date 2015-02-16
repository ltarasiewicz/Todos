<?php

require "vendor/autoload.php";

use Todos\Config\DbConnect;

$db = new DbConnect;
$entityManager = $db->getEntityManager();
