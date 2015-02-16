<?php

namespace Todos\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

/**
 * A class that defines and adds a 'show todos' task to the application
 */
class ShowTasksCommand extends Command
{
    
    /**
     *
     * @var type 
     */
    private $entityManager;
    
    public function __construct($entityManager) {
        parent::__construct();
        $this->entityManager = $entityManager;
    }
    
    public function configure()
    {
        $this
            ->setName('todo:show')
            ->setDescription('Show tasks saved in the database')
            ->addArgument(
                    'what',
                    InputArgument::OPTIONAL,
                    'Define tasks that you want to list'                    
            )
            ->addArgument(
                    'ids',
                    InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                    'Give a list if ids of tasks that you want to see'                    
            );                
    }
    
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $whatToShow = $input->getArgument('what') ?: null;
        $ids = $input->getArgument('ids') ?: null;        
        $taskRepository = $this->entityManager->getRepository('Todos\\Entity\\Task');
        $message = array();
        $tasks = array();
        $wrongArguments = false;
        
        
        /**
         * Check what arguments were passed to the command
         */
        switch($whatToShow) {            
            case 'all':
                $tasks = $taskRepository->findAll();                        
                $noOfTasks = count($tasks);    
                break;
            case 'incomplete':
                $tasks = $taskRepository->findBy(
                        array('complete'  =>  false)
                );
                $noOfTasks = count($tasks);  
                break;
            case 'complete':
                $tasks = $taskRepository->findBy(
                        array('complete' => true)
                );
                $noOfTasks = count($tasks);     
                break;
            case 'ids':
                foreach($ids as $id) {
                    $tasks[] = $this->entityManager->find('Todos\\Entity\\Task', $id);                 
                }            
                $noOfTasks = count($tasks);       
                break;
            default:
                $wrongArguments = true;
                $output->writeln('<info>Please refer to the manual pages of this application</info>');
        }           
        
        
        /**
         * Make sure that the tabularized output can be generated
         */
        if ($wrongArguments === false) {
            
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
                    $tasks[$i]->getID(), $tasks[$i]->getCategory(), $tasks[$i]->getDescription(), 
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