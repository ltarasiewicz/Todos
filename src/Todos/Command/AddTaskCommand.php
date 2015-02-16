<?php

namespace Todos\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Todos\Entity\Task;

/**
 * A class that defines todo:add tasl
 */
class AddTaskCommand extends Command
{
    
    /**     
     * @var EntityManager 
     */
    private $entityManager;
    
    /**
     * 
     * @param EntityManager
     */
    public function __construct($entityManager) {
        parent::__construct();
        $this->entityManager = $entityManager;
    }
    
    public function configure()
    {
        $this
            ->setName('todo:add')
            ->setDescription('Add a todo to the list')
            ->addArgument(
                    'category',
                    InputArgument::REQUIRED,
                    'What is the category of this task?'                    
            )
            ->addArgument(
                    'description',
                    InputArgument::REQUIRED,
                    'What do you need to do?'
            )
            ->addArgument(
                    'deadline',
                    InputArgument::OPTIONAL,
                    'By the end of what date and hour is the task to be delivered? (dd-mm-yy)'
            );
    }
    
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $category = $input->getArgument('category') ?: null;
        $description = $input->getArgument('description') ?: null;
        $deadline = $input->getArgument('deadline') ?: null;
        
        /**
         * Initialize the process only if the required arguments are provided
         */
        if ($category && $description) {
            
            
            /**
             * Create a new task and persist it to the database
             */
            $newTask = new Task;
            $newTask
                    ->setCategory($category)
                    ->setDescription($description)
                    ->setDeadline(new \DateTime($deadline));
            
            $this->entityManager->persist($newTask);
            $this->entityManager->flush();
            
                /**
                 * Convert the entity properties into string for rendering in the tabularized format
                 */
                $status = (string) $newTask->isComplete();                  
                $stringStatus = $status ? 'Complete' : 'Incomplete';
                
                $deadline = $newTask->getDeadline();   
                
                /**
                 * Convert the \DateTime object into string for rendering in the tabularized format
                 */
                if ($deadline instanceof \DateTime) {
                    $stringDeadline = $deadline->format('Y-m-d');
                } else {
                    $stringDeadline = 'Unknown';
                }   
                
                
            /**
             * Initialize the table object and render the output in a tabularized format
             */            
            $table = new Table($output);
            $table
                ->setHeaders(array('ID', 'Category', 'Description', 'Deadline', 'Complete'))
                ->setRows(array(
                    array($newTask->getId(), $newTask->getCategory(), $newTask->getDescription(), 
                        $stringDeadline, $stringStatus)
                ));

            $table->render();   
            
        } else {
            
            $output->writeln('<info>Failed to write the task to the database</info>');    
            
        }                   
        
    }
}