<?php

namespace Todos\Entity;

/**
 * @Entity @Table(name="tasks")
 **/
class Task
{
    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $id;
    
    /** @Column(type="string") **/
    protected $category;

    /** @Column(type="string") **/
    protected $description;
    
    /** @Column(type="date", nullable=true) **/
    protected $deadline;
    
    /** @Column(type="boolean", nullable=true) **/
    protected $complete = false;
    
    public function getId()
    {
        return $this->id;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }    
    
    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }     
    
    public function getDeadline()
    {
        return $this->deadline ? clone $this->deadline: null;
    }

    public function setDeadline(\DateTime $deadline = null)
    {
        $this->deadline = $deadline;
        return $this;
    }       
    
    public function isComplete()
    {
        return $this->complete;
    }
    
    public function setComplete( $status = boolean)
    {
        $this->complete = $status;
    }
}