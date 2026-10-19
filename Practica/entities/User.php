<?php
class User
{
    private $id;
    private $email;
    private $password;
    private $firstName;
    private $lastName;
    private $birthDate;
    private $about;

    public function __construct($email, $password, $firstName, $lastName, $birthDate, $about, $id = null)
    {
        $this->id = $id;
        $this->setEmail($email);
        $this->setPassword($password);
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setBirthDate($birthDate);
        $this->setAbout($about);
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getBirthDate()
    {
        return $this->birthDate;
    }

    public function getAbout()
    {
        return $this->about;
    }

    // Setters
    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;
    }

    public function setAbout($about)
    {
        $this->about = $about;
    }
}
