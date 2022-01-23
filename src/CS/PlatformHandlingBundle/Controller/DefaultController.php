<?php

namespace CS\PlatformHandlingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CSPlatformHandlingBundle:Default:index.html.twig');
    }
}
