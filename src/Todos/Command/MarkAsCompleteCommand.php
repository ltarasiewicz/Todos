<?php

namespace Todos\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

/**
 * A class that defines todo:add tasl
 */
class MarkAsCompleteCommand extends Command
{
    
    /**     
     * @var EntityManager 
     */
    private $entityManager;
    
    /**
     * 
     * @param EntityManager
     */
    public function __construct($entityManager) 
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }
    
    public function configure()
    {
        $this
            ->setName('todo:tick-off')
            ->setDescription('Add a todo to the list')
            ->addArgument(
                'ids',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'Provide an id of a task(s) that has been completed'                    
            );
    }
    
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $ids = $input->getArgument('ids') ?: null;
        $message = array();
        $tasks = array();
        
        if ($ids) {
            
            /**
             * Iterate through entities with the selected ids and set their status to complete (true)
             */
            foreach($ids as $id) {
                $completedTask = $this->entityManager->find('Todos\\Entity\Task', $id);
                $completedTask->setComplete(true);
                $this->entityManager->persist($completedTask);                
                $tasks[] = $completedTask;
            }
            
            $this->entityManager->flush();
            
            /** Number of updated tasks **/
            $noOfTasks = count($tasks);      
            
            /**
             * Generate output for the table console helper
             */      
            for($i = 0; $i < $noOfTasks; $i++) {
                
                /**
                 * Convert the entity properties into string for rendering in the tabularized format
                 */
                $status = (string) $tasks[$i]->isComplete();                  
                $stringStatus = $status ? 'Complete' : 'Incomplete';
                
                $deadline = $tasks[$i]->getDeadline();
                
                /**
                 * Convert the \DateTime object into string for rendering in the tabularized format
                 */
                if ($deadline instanceof \DateTime) {
                    $stringDeadline = $deadline->format('Y-m-d');
                } else {
                    $stringDeadline = 'Unknown';
                }
                
                /**
                 * Prepare the argument for the Table's instance setRows() method
                 */
                $message[$i] = array(
                    $tasks[$i]->getID(), $tasks[$i]->getCategory, $tasks[$i]->getDescription(), 
                    $stringDeadline, $stringStatus
                );                 
            }
            
            /**
             * Initialize the table object and render the output in a tabularized format
             */
            $table = new Table($output);
            $table
                ->setHeaders(array('ID', 'Category', 'Description', 'Deadline', 'Complete'))
                ->setRows($message);

            $table->render();              
            
        }                       
    }
}