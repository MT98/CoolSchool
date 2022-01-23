<?php

namespace CS\CoreBundle\Services;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use CS\PlatformHandlingBundle\Entity\CoolSchoolEmployee;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AuthenticationEventListener
{
    private $router;
    private $doctrine;
    private $dispatcher; 
    private $user;

    public function __construct(\Symfony\Component\Routing\Router $router, \Doctrine\Bundle\DoctrineBundle\Registry $doctrine, \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher)
    {
        $this->router = $router;
        $this->doctrine = $doctrine;
        $this->dispatcher = $dispatcher;
    
    }


    /* les concernants la réussite des authentifications */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        /* On récupère les données de l'utilisateur connecté*/
        $this->user = $event->getAuthenticationToken()->getUser();

        if($this->user instanceof CoolSchoolEmployee)
        {
            /* on récupère l'utilisateur de la base données afin de procéder à des modifications */
            $em = $this->doctrine->getEntityManager();
            $employee = $em->getRepository('CSPlatformHandlingBundle:CoolSchoolEmployee')->find($this->user->getId());


            /* on réactualise sa date de dernière connection */
            $employee->setLastConnection(new \DateTime());
           
            /* on met à jour la base de données */
            $em->flush();
            
            $this->dispatcher->addListener(KernelEvents::RESPONSE, array($this, 'onKernelSuccessResponseForAdministration'));   
        }else
        {
            return;
        }
    }

    public function onKernelSuccessResponseForAdministration(FilterResponseEvent $event)
    {
      
        $event->getResponse()->headers->set('Location', $this->router->generate('cs_platform_handling_coolschool_team_employee_viewAll'));            
        
    }
}