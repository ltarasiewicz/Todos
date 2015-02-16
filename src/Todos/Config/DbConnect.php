<?php

namespace Todos\Config;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class DbConnect
{
    
    private $isDevMode = true;
    private $entityManager;
    
    public function __construct()
    {

        $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/../Entity"), $this->isDevMode);

        $conn = array(
            'driver'    =>  'pdo_sqlite',
            'path'  =>  __DIR__ . '/db.sqlite',
        );

        $this->entityManager = EntityManager::create($conn, $config);        
        
    }
    
    public function getEntityManager()
    {
        return $this->entityManager;
    }
}