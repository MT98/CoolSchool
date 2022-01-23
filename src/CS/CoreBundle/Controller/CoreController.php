<?php

namespace CS\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CoreController extends Controller
{
    public function indexAction()
    {
        return $this->render('CSCoreBundle:Core:index.html.twig');
    }
}
