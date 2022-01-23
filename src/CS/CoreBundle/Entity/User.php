<?php

namespace CS\CoreBundle\Entity;

class User{

    private $_username;
    private $_password;
    private $_targetPath;
    private $_rememberMe;

    public function getRememberMe()
    {
        return $this->_rememberMe;
    }
    public function setRememberMe($rememberMe)
    {
        $this->_rememberMe = $rememberMe;
        return $this;
    }
    public function getUsername()
    {
        return $this->_username;
    }
    public function getPassword()
    {
        return $this->_password;
    }
    public function getTargetPath()
    {
        return $this->_targetPath;
    }

    public function setUsername($username)
    {
        $this->_username = $username;
        return $this;
    }

    public function setPassword($password)
    {
        $this->_password = $password;
        return $this;
    }

    public function setTargetPath($targetPath)
    {
        $this->_targetPath = $targetPath;
        return $this;
    }
}