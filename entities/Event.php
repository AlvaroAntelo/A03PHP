<?php
class Event
{
    private $id;
    private $userId;
    private $title;
    private $description;
    private $startDate;
    private $endDate;

    public function __construct($userId, $title, $description, $startDate, $endDate, $id = null)
    {
        $this->id = $id;
        $this->setUserId($userId);
        $this->setTitle($title);
        $this->setDescription($description);
        $this->setStartDate($startDate);
        $this->setEndDate($endDate);
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    // Setters
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }
}
