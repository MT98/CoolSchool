<?php

namespace CS\CoreBundle\Entity;

class CredentialsComponentsForChange{

    private $username;
    private $usernameConfirmation;
    private $password;
    private $passwordConfirmation;
    private $code;
    private $what;


    public function getWhat()
    {
        return $this->what;
    }
    public function setWhat($what)
    {
        $this->what = $what;
        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }
    public function getUsername()
    {
        return $this->username;
    }
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }
    public function getPassword()
    {
        return $this->password;
    }
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function getUsernameConfirmation()
    {
        return $this->usernameConfirmation;
    }
    public function setUsernameConfirmation($usernameConfirmation)
    {
        $this->usernameConfirmation = $usernameConfirmation;
        return $this;
    }

    public function getPasswordConfirmation()
    {
        return $this->passwordConfirmation;
    }
    public function setPasswordConfirmation($passwordConfirmation)
    {
        $this->passwordConfirmation = $passwordConfirmation;
        return $this;
    }
   
}