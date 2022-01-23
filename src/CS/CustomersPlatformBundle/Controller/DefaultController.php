<?php

namespace CS\CustomersPlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CSCustomersPlatformBundle:Default:index.html.twig');
    }
}
