<?php

namespace CS\CoreBundle\Services;


class MyRequestStack
{
    private $requestStack;
  
    public function __construct(\Symfony\Component\HttpFoundation\RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    public function getRequestStack()
    {
        return $this->requestStack;
    }

}