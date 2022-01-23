<?php

namespace CS\PlatformHandlingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use CS\PlatformHandlingBundle\Entity\AdministrativeService;
use CS\PlatformHandlingBundle\Form\AdministrativeServiceType;
use CS\PlatformHandlingBundle\Form\AdministrativeServiceModifyType;
use CS\PlatformHandlingBundle\Form\AdministrativeServiceConfirmType;
use CS\PlatformHandlingBundle\Entity\AdministrativeRole;
use CS\PlatformHandlingBundle\Form\AdministrativeRoleModifyType;
use CS\PlatformHandlingBundle\Form\AdministrativeRoleType;
use CS\PlatformHandlingBundle\Form\AdministrativeRoleConfirmType;
use CS\PlatformHandlingBundle\Entity\CustomersService;
use CS\PlatformHandlingBundle\Form\CustomersServiceType;
use CS\PlatformHandlingBundle\Form\CustomersServiceModifyType;
use CS\PlatformHandlingBundle\Form\CustomersServiceConfirmType;
use CS\PlatformHandlingBundle\Entity\CustomersRole;
use CS\PlatformHandlingBundle\Form\CustomersRoleModifyType;
use CS\PlatformHandlingBundle\Form\CustomersRoleType;
use CS\PlatformHandlingBundle\Form\CustomersRoleConfirmType;

class ServicesAndRolesController extends Controller
{
    /* les actions pour les services clientèles */
    public function addCustomersServiceAction(Request $request)
    {
        $customService = new CustomersService();

        $form = $this->get('form.factory')->create(CustomersServiceType::class, $customService);

        /* lors de la soumission de formulaire */
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($customService);
                $em->flush();

                $request->getSession()->getFlashBag()->add('infoSuccess', "Le service ". $customService->getName() . " a été créé avec succés!");
                /* On reinitialise le formulaire */
                $customService = new CustomersService();
                $form = $this->get('form.factory')->create(CustomersServiceType::class, $customService);
                return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:addCustomersService.html.twig', array('action'=> 'add', 'form'=>$form->createView()));
            }else
            {
                $request->getSession()->getFlashBag()->add('infoError', "Echec de la création du service ". $customService->getName() . " !");
                return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:addCustomersService.html.twig', array('action'=> 'add', 'form'=>$form->createView()));
            }
        }


        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:addCustomersService.html.twig', array('action'=> 'add', 'form'=>$form->createView()));
    }

    public function updateCustomersServiceAction($id, Request $request)
    {
        /* l'algo ci-dessous est géré par le formType */
        /* si le service est déjà publié */
            /* on ne peut modifier que la descripion ou le prix */
        /* sinon on peut tout modifier */

        $em = $this->getDoctrine()->getManager();
        $customService = $em->getRepository('CSPlatformHandlingBundle:CustomersService')->find($id); 
         
        if($customService == null)
        {
            throw new NotFoundHttpException("Ce lien est invalide!");                
        }
        /* On recupère l'ancien nom */
        $previousName = $customService->getName();

        $form = $this->get('form.factory')->create(CustomersServiceModifyType::class, $customService);

        /* lors de la soumission de formulaire */
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {

                $em->flush();

                $request->getSession()->getFlashBag()->add('infoSuccess', "Le service ". $previousName . " a été modifié avec succés!");
                return $this->redirectToRoute('cs_platform_handling_services_roles_customers_service_view', array("id"=>$id));
            }else
            {
                $request->getSession()->getFlashBag()->add('infoError', "Echec de la modification du service ". $previousName . " !");        
                return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:updateCustomersService.html.twig', array('action'=> 'update', 'form'=>$form->createView()));        
            }
        }


        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:updateCustomersService.html.twig', array('action'=> 'update', 'form'=>$form->createView()));        
    }

    public function viewCustomersServiceAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère le service $id
        $customService = $em->getRepository('CSPlatformHandlingBundle:CustomersService')->getAdminServiceWithRoles($id);
        if (null === $customService) {
        throw new NotFoundHttpException("Ce lien n'est pas valide!");
        }                

        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:viewCustomersService.html.twig', array('service' => $customService));            
    }

    public function viewAllCustomersServiceAction()
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère tous les adminServices 

        $customServices = $em->getRepository('CSPlatformHandlingBundle:CustomersService')->getAllAdminServices();

        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:viewAllCustomersService.html.twig', array('services' => $customServices));    
    }

    public function deleteCustomersServiceAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère le service d'id $id
        $customService = $em->getRepository('CSPlatformHandlingBundle:CustomersService')->getAdminServiceWithRoles($id);

        if (null === $customService) {
        throw new NotFoundHttpException("Ce lien est invalide.");
        }
        if($customService->getPublished() == true)
        {
            throw new NotFoundHttpException("Ce service ne peut être supprimé car déjà publié.");
        }

        $form = $this->get('form.factory')->create(CustomersServiceConfirmType::class, $customService);
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {

                $roles = $customService->getRoles();
                /*On supprime les roles du service $customService*/
                foreach($roles as $role)
                {
                    $customService->removeRole($role);
                }
                /*On supprime les roles du service de la base de données*/
                $em->getRepository('CSPlatformHandlingBundle:CustomersRole')->removeAllRolesRelatedToService($customService->getId());
                
                /*On supprime le cycle de la base de données*/
                $em->remove($customService);
                $em->flush();

                $request->getSession()->getFlashBag()->add('infoSuccess','Le service '.$customService->getName().' a été supprimé!');
                return $this->redirectToRoute('cs_platform_handling_services_roles_customers_service_viewAll');
            }else
            {   
                $request->getSession()->getFlashBag()->add('infoError','Echec de la suppression du service '.$customService->getName().' !');
                return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:deleteCustomersService.html.twig', array('service'=>$customService, 'form'=>$form->createView()));
            }
        }

        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:deleteCustomersService.html.twig', array('service'=>$customService, 'action'=> 'delete', 'form'=>$form->createView()));    
    }

    public function disableCustomersServiceAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère le service d'id $id
        $customService = $em->getRepository('CSPlatformHandlingBundle:CustomersService')->find($id);

        if (null === $customService) {
        throw new NotFoundHttpException("Ce lien est invalide.");
        }
        if($customService->getPublished() == false)
        {
            throw new NotFoundHttpException("Ce service ne peut être désactivé mais doit être supprimé.");
        }

        $form = $this->get('form.factory')->create(CustomersServiceConfirmType::class, $customService);
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {
                /*On désactive les roles du service de la base de données*/
                $em->getRepository('CSPlatformHandlingBundle:CustomersRole')->disableAllRolesRelatedToService($customService->getId());
                
                /* On desactive le service */
                $customService->setIsActive(false);

                $em->flush();

                $request->getSession()->getFlashBag()->add('infoSuccess','Le service '.$customService->getName().' a été désactivé!');
                return $this->redirectToRoute('cs_platform_handling_services_roles_customers_service_viewAll');
            }else
            {   
                $request->getSession()->getFlashBag()->add('infoError','Echec de la désactivation du service '.$customService->getName().' !');
                return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:disableCustomersService.html.twig', array('service'=>$customService, 'form'=>$form->createView()));
            }
        }

        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:disableCustomersService.html.twig', array('service'=>$customService, 'action'=> 'disable', 'form'=>$form->createView()));    
    }

    public function enableCustomersServiceAction($id, Request $request)
    { 
        $em = $this->getDoctrine()->getManager();

        // On récupère le service d'id $id
        $customService = $em->getRepository('CSPlatformHandlingBundle:CustomersService')->find($id);

        if (null === $customService) {
        throw new NotFoundHttpException("Ce lien est invalide.");
        }
        if($customService->getIsActive() == true)
        {
            throw new NotFoundHttpException("Ce service est déjà activé.");
        }

        $form = $this->get('form.factory')->create(CustomersServiceConfirmType::class, $customService);
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {
                /* On active le service */
                $customService->setIsActive(true);

                $em->flush();

                $request->getSession()->getFlashBag()->add('infoSuccess','Le service '.$customService->getName().' a été activé!');
                return $this->redirectToRoute('cs_platform_handling_services_roles_customers_service_viewAll');
            }else
            {   
                $request->getSession()->getFlashBag()->add('infoError','Echec de l\'activation du service '.$customService->getName().' !');
                return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:enableCustomersService.html.twig', array('service'=>$customService, 'form'=>$form->createView()));
            }
        }

        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:enableCustomersService.html.twig', array('service'=>$customService, 'action'=> 'enable', 'form'=>$form->createView()));    
    }


    /*les actions pour les roles clientèles */

    public function addCustomersRoleAction(Request $request)
    {
        $customRole = new CustomersRole();

        $form = $this->get('form.factory')->create(CustomersRoleType::class, $customRole);

        /* lors de la soumission de formulaire */
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($customRole);
                $em->flush();

                $request->getSession()->getFlashBag()->add('infoSuccess', "Le rôle ". $customRole->getName() . " a été créé avec succés!");
               
                /*On reinitialise le formulaire */
                $customRole = new CustomersRole();

                $form = $this->get('form.factory')->create(CustomersRoleType::class, $customRole);
                return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:addCustomersRole.html.twig', array('action'=> 'add', 'form'=>$form->createView()));
            }else
            {
                $request->getSession()->getFlashBag()->add('infoError', "Echec de la création du rôle ". $customRole->getName() . " !");
                return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:addCustomersRole.html.twig', array('action'=> 'add', 'form'=>$form->createView()));
            }
        }


        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:addCustomersRole.html.twig', array('action'=> 'add', 'form'=>$form->createView()));
    }

    public function updateCustomersRoleAction($id, Request $request)
    {

        /* l'algo ci-dessous est géré par le formType */
        /* si le rôle est déjà publié */
            /* on ne peut modifier que la descripion */
        /* sinon on peut tout modifier */

        $em = $this->getDoctrine()->getManager();
        $customRole = $em->getRepository('CSPlatformHandlingBundle:CustomersRole')->getRoleWithRelatedService($id); 
         
        if($customRole == null)
        {
            throw new NotFoundHttpException("Ce lien est invalide!");                
        }
        /* On recupère l'ancien nom */
        $previousName = $customRole->getName();

        $form = $this->get('form.factory')->create(CustomersRoleModifyType::class, $customRole);

        /* lors de la soumission de formulaire */
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {

                $em->flush();

                $request->getSession()->getFlashBag()->add('infoSuccess', "Le rôle ". $previousName . " a été modifié avec succés!");

                return $this->redirectToRoute('cs_platform_handling_services_roles_customers_role_view', array("id"=>$id));
            }else
            {
                $request->getSession()->getFlashBag()->add('infoError', "Echec de la modification du role ". $previousName . " !");        
                return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:updateCustomersRole.html.twig', array('action'=> 'update', 'form'=>$form->createView()));        
            }
        }


        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:updateCustomersRole.html.twig', array('action'=> 'update', 'form'=>$form->createView()));        
    
    }

    public function viewCustomersRoleAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère le role $id
        $customRole = $em->getRepository('CSPlatformHandlingBundle:CustomersRole')->getRoleWithRelatedService($id);
        if (null === $customRole) {
        throw new NotFoundHttpException("Ce lien n'est pas valide!");
        }                

        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:viewCustomersRole.html.twig', array('role' => $customRole));            
    }

    public function viewAllCustomersRoleAction()
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère tous les adminroles 

        $customRoles = $em->getRepository('CSPlatformHandlingBundle:CustomersRole')->getAllRolesWithRelatedServices();

        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:viewAllCustomersRole.html.twig', array('roles' => $customRoles));    
    }

    public function deleteCustomersRoleAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère le role d'id $id
        $customRole = $em->getRepository('CSPlatformHandlingBundle:CustomersRole')->getRoleWithRelatedService($id);

        if (null === $customRole) {
        throw new NotFoundHttpException("Ce lien est invalide.");
        }

        if($customRole->getPublished() == true)
        {
            throw new NotFoundHttpException("Ce rôle ne peut être supprimé car déjà publié mais il  peut être désactivé.");
        }

        $form = $this->get('form.factory')->create(CustomersRoleConfirmType::class, $customRole);
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {

                /*On supprime le role de la base de données*/
                $em->remove($customRole);
                $em->flush();

                $request->getSession()->getFlashBag()->add('infoSuccess','Le rôle '.$customRole->getName().' a été supprimé!');
                return $this->redirectToRoute('cs_platform_handling_services_roles_customers_role_viewAll');
            }else
            {   
                $request->getSession()->getFlashBag()->add('infoError','Echec de la suppression du rôle '.$customRole->getName().' !');
                return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:deleteCustomersRole.html.twig', array('role'=>$customRole, 'form'=>$form->createView()));
            }
        }

        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:deleteCustomersRole.html.twig', array('role'=>$customRole, 'action'=> 'delete', 'form'=>$form->createView()));    
    }

    public function disableCustomersRoleAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère le Role d'id $id
        $customRole = $em->getRepository('CSPlatformHandlingBundle:CustomersRole')->find($id);

        if (null === $customRole) {
        throw new NotFoundHttpException("Ce lien est invalide.");
        }
        if($customRole->getPublished() == false)
        {
            throw new NotFoundHttpException("Ce Role ne peut être désactivé mais doit être supprimé.");
        }

        $form = $this->get('form.factory')->create(CustomersRoleConfirmType::class, $customRole);
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            { 
                /* On desactive le Role */
                $customRole->setIsActive(false);

                $em->flush();

                $request->getSession()->getFlashBag()->add('infoSuccess','Le Role '.$customRole->getName().' a été désactivé!');
                return $this->redirectToRoute('cs_platform_handling_services_roles_customers_role_view', array('id'=>$id));
            }else
            {   
                $request->getSession()->getFlashBag()->add('infoError','Echec de la désactivation du Role '.$customRole->getName().' !');
                return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:disableCustomersRole.html.twig', array('role'=>$customRole, 'form'=>$form->createView()));
            }
        }

        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:disableCustomersRole.html.twig', array('role'=>$customRole, 'action'=> 'disable', 'form'=>$form->createView()));    
    }

    public function enableCustomersRoleAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère le Role d'id $id
        $customRole = $em->getRepository('CSPlatformHandlingBundle:CustomersRole')->find($id);

        if (null === $customRole) {
        throw new NotFoundHttpException("Ce lien est invalide.");
        }
        if($customRole->getIsActive() == true)
        {
            throw new NotFoundHttpException("Ce Role est déjà activé.");
        }

        $form = $this->get('form.factory')->create(CustomersRoleConfirmType::class, $customRole);
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            { 
                /* On active le Role */
                $customRole->setIsActive(true);

                $em->flush();

                $request->getSession()->getFlashBag()->add('infoSuccess','Le Role '.$customRole->getName().' a été réactivé!');
                return $this->redirectToRoute('cs_platform_handling_services_roles_customers_role_view', array('id'=>$id));
            }else
            {   
                $request->getSession()->getFlashBag()->add('infoError','Echec de la réactivation du Role '.$customRole->getName().' !');
                return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:enableCustomersRole.html.twig', array('role'=>$customRole, 'form'=>$form->createView()));
            }
        }

        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:enableCustomersRole.html.twig', array('role'=>$customRole, 'action'=> 'enable', 'form'=>$form->createView()));    
    }


    /* les actions pour les services adminitratifs */
    public function addAdministrativeServiceAction(Request $request)
    {
        $adminService = new AdministrativeService();

        $form = $this->get('form.factory')->create(AdministrativeServiceType::class, $adminService);

        /* lors de la soumission de formulaire */
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($adminService);
                $em->flush();

                $request->getSession()->getFlashBag()->add('infoSuccess', "Le service ". $adminService->getName() . " a été créé avec succés!");
                /* On reinitialise le formulaire */
                $adminService = new AdministrativeService();
                $form = $this->get('form.factory')->create(AdministrativeServiceType::class, $adminService);
                return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:addAdministrativeService.html.twig', array('action'=> 'add', 'form'=>$form->createView()));
            }else
            {
                $request->getSession()->getFlashBag()->add('infoError', "Echec de la création du service ". $adminService->getName() . " !");
                return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:addAdministrativeService.html.twig', array('action'=> 'add', 'form'=>$form->createView()));
            }
        }


        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:addAdministrativeService.html.twig', array('action'=> 'add', 'form'=>$form->createView()));
    }

    public function updateAdministrativeServiceAction($id, Request $request)
    {
        /* l'algo ci-dessous est géré par le formType */
        /* si le service est déjà publié */
            /* on ne peut modifier que la descripion ou le prix */
        /* sinon on peut tout modifier */

        $em = $this->getDoctrine()->getManager();
        $adminService = $em->getRepository('CSPlatformHandlingBundle:AdministrativeService')->find($id); 
         
        if($adminService == null)
        {
            throw new NotFoundHttpException("Ce lien est invalide!");                
        }
        /* On recupère l'ancien nom */
        $previousName = $adminService->getName();

        $form = $this->get('form.factory')->create(AdministrativeServiceModifyType::class, $adminService);

        /* lors de la soumission de formulaire */
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {

                $em->flush();

                $request->getSession()->getFlashBag()->add('infoSuccess', "Le service ". $previousName . " a été modifié avec succés!");
                return $this->redirectToRoute('cs_platform_handling_services_roles_administrative_service_view', array("id"=>$id));
            }else
            {
                $request->getSession()->getFlashBag()->add('infoError', "Echec de la modification du service ". $previousName . " !");        
                return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:updateAdministrativeService.html.twig', array('action'=> 'update', 'form'=>$form->createView()));        
            }
        }


        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:updateAdministrativeService.html.twig', array('action'=> 'update', 'form'=>$form->createView()));        
    }

    public function viewAdministrativeServiceAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère le service $id
        $adminService = $em->getRepository('CSPlatformHandlingBundle:AdministrativeService')->getAdminServiceWithRoles($id);
        if (null === $adminService) {
        throw new NotFoundHttpException("Ce lien n'est pas valide!");
        }                

        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:viewAdministrativeService.html.twig', array('service' => $adminService));            
    }

    public function viewAllAdministrativeServiceAction()
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère tous les adminServices 

        $adminServices = $em->getRepository('CSPlatformHandlingBundle:AdministrativeService')->getAllAdminServices();

        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:viewAllAdministrativeService.html.twig', array('services' => $adminServices));    
    }

    public function deleteAdministrativeServiceAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère le service d'id $id
        $adminService = $em->getRepository('CSPlatformHandlingBundle:AdministrativeService')->getAdminServiceWithRoles($id);

        if (null === $adminService) {
        throw new NotFoundHttpException("Ce lien est invalide.");
        }
        if($adminService->getPublished() == true)
        {
            throw new NotFoundHttpException("Ce service ne peut être supprimé car déjà publié.");
        }

        $form = $this->get('form.factory')->create(AdministrativeServiceConfirmType::class, $adminService);
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {

                $roles = $adminService->getRoles();
                /*On supprime les roles du service $adminService*/
                foreach($roles as $role)
                {
                    $adminService->removeRole($role);
                }
                /*On supprime les roles du service de la base de données*/
                $em->getRepository('CSPlatformHandlingBundle:AdministrativeRole')->removeAllRolesRelatedToService($adminService->getId());
                
                /*On supprime le cycle de la base de données*/
                $em->remove($adminService);
                $em->flush();

                $request->getSession()->getFlashBag()->add('infoSuccess','Le service '.$adminService->getName().' a été supprimé!');
                return $this->redirectToRoute('cs_platform_handling_services_roles_administrative_service_viewAll');
            }else
            {   
                $request->getSession()->getFlashBag()->add('infoError','Echec de la suppression du service '.$adminService->getName().' !');
                return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:deleteAdministrativeService.html.twig', array('service'=>$adminService, 'form'=>$form->createView()));
            }
        }

        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:deleteAdministrativeService.html.twig', array('service'=>$adminService, 'action'=> 'delete', 'form'=>$form->createView()));    
    }

    public function disableAdministrativeServiceAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère le service d'id $id
        $adminService = $em->getRepository('CSPlatformHandlingBundle:AdministrativeService')->find($id);

        if (null === $adminService) {
        throw new NotFoundHttpException("Ce lien est invalide.");
        }
        if($adminService->getPublished() == false)
        {
            throw new NotFoundHttpException("Ce service ne peut être désactivé mais doit être supprimé.");
        }

        $form = $this->get('form.factory')->create(AdministrativeServiceConfirmType::class, $adminService);
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {
                /*On désactive les roles du service de la base de données*/
                $em->getRepository('CSPlatformHandlingBundle:AdministrativeRole')->disableAllRolesRelatedToService($adminService->getId());
                
                /* On desactive le service */
                $adminService->setIsActive(false);

                $em->flush();

                $request->getSession()->getFlashBag()->add('infoSuccess','Le service '.$adminService->getName().' a été désactivé!');
                return $this->redirectToRoute('cs_platform_handling_services_roles_administrative_service_viewAll');
            }else
            {   
                $request->getSession()->getFlashBag()->add('infoError','Echec de la désactivation du service '.$adminService->getName().' !');
                return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:disableAdministrativeService.html.twig', array('service'=>$adminService, 'form'=>$form->createView()));
            }
        }

        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:disableAdministrativeService.html.twig', array('service'=>$adminService, 'action'=> 'disable', 'form'=>$form->createView()));    
    }

    public function enableAdministrativeServiceAction($id, Request $request)
    { 
        $em = $this->getDoctrine()->getManager();

        // On récupère le service d'id $id
        $adminService = $em->getRepository('CSPlatformHandlingBundle:AdministrativeService')->find($id);

        if (null === $adminService) {
        throw new NotFoundHttpException("Ce lien est invalide.");
        }
        if($adminService->getIsActive() == true)
        {
            throw new NotFoundHttpException("Ce service est déjà activé.");
        }

        $form = $this->get('form.factory')->create(AdministrativeServiceConfirmType::class, $adminService);
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {
                /* On active le service */
                $adminService->setIsActive(true);

                $em->flush();

                $request->getSession()->getFlashBag()->add('infoSuccess','Le service '.$adminService->getName().' a été activé!');
                return $this->redirectToRoute('cs_platform_handling_services_roles_administrative_service_viewAll');
            }else
            {   
                $request->getSession()->getFlashBag()->add('infoError','Echec de l\'activation du service '.$adminService->getName().' !');
                return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:enableAdministrativeService.html.twig', array('service'=>$adminService, 'form'=>$form->createView()));
            }
        }

        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:enableAdministrativeService.html.twig', array('service'=>$adminService, 'action'=> 'enable', 'form'=>$form->createView()));    
    }


    /*les actions pour les roles administratifs */

    public function addAdministrativeRoleAction(Request $request)
    {
        $adminRole = new AdministrativeRole();

        $form = $this->get('form.factory')->create(AdministrativeRoleType::class, $adminRole);

        /* lors de la soumission de formulaire */
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($adminRole);
                $em->flush();

                $request->getSession()->getFlashBag()->add('infoSuccess', "Le rôle ". $adminRole->getName() . " a été créé avec succés!");
               
                /*On reinitialise le formulaire */
                $adminRole = new AdministrativeRole();

                $form = $this->get('form.factory')->create(AdministrativeRoleType::class, $adminRole);
                return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:addAdministrativeRole.html.twig', array('action'=> 'add', 'form'=>$form->createView()));
            }else
            {
                $request->getSession()->getFlashBag()->add('infoError', "Echec de la création du rôle ". $adminRole->getName() . " !");
                return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:addAdministrativeRole.html.twig', array('action'=> 'add', 'form'=>$form->createView()));
            }
        }


        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:addAdministrativeRole.html.twig', array('action'=> 'add', 'form'=>$form->createView()));
    }

    public function updateAdministrativeRoleAction($id, Request $request)
    {

        /* l'algo ci-dessous est géré par le formType */
        /* si le rôle est déjà publié */
            /* on ne peut modifier que la descripion */
        /* sinon on peut tout modifier */

        $em = $this->getDoctrine()->getManager();
        $adminRole = $em->getRepository('CSPlatformHandlingBundle:AdministrativeRole')->getRoleWithRelatedService($id); 
         
        if($adminRole == null)
        {
            throw new NotFoundHttpException("Ce lien est invalide!");                
        }
        /* On recupère l'ancien nom */
        $previousName = $adminRole->getName();

        $form = $this->get('form.factory')->create(AdministrativeRoleModifyType::class, $adminRole);

        /* lors de la soumission de formulaire */
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {

                $em->flush();

                $request->getSession()->getFlashBag()->add('infoSuccess', "Le rôle ". $previousName . " a été modifié avec succés!");

                return $this->redirectToRoute('cs_platform_handling_services_roles_administrative_role_view', array("id"=>$id));
            }else
            {
                $request->getSession()->getFlashBag()->add('infoError', "Echec de la modification du role ". $previousName . " !");        
                return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:updateAdministrativeRole.html.twig', array('action'=> 'update', 'form'=>$form->createView()));        
            }
        }


        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:updateAdministrativeRole.html.twig', array('action'=> 'update', 'form'=>$form->createView()));        
    
    }

    public function viewAdministrativeRoleAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère le role $id
        $adminRole = $em->getRepository('CSPlatformHandlingBundle:AdministrativeRole')->getRoleWithRelatedService($id);
        if (null === $adminRole) {
        throw new NotFoundHttpException("Ce lien n'est pas valide!");
        }                

        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:viewAdministrativeRole.html.twig', array('role' => $adminRole));            
    }

    public function viewAllAdministrativeRoleAction()
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère tous les adminroles 

        $adminRoles = $em->getRepository('CSPlatformHandlingBundle:AdministrativeRole')->getAllRolesWithRelatedServices();

        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:viewAllAdministrativeRole.html.twig', array('roles' => $adminRoles));    
    }

    public function deleteAdministrativeRoleAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère le role d'id $id
        $adminRole = $em->getRepository('CSPlatformHandlingBundle:AdministrativeRole')->getRoleWithRelatedService($id);

        if (null === $adminRole) {
        throw new NotFoundHttpException("Ce lien est invalide.");
        }

        if($adminRole->getPublished() == true)
        {
            throw new NotFoundHttpException("Ce rôle ne peut être supprimé car déjà publié mais il  peut être désactivé.");
        }

        $form = $this->get('form.factory')->create(AdministrativeRoleConfirmType::class, $adminRole);
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {

                /*On supprime le role de la base de données*/
                $em->remove($adminRole);
                $em->flush();

                $request->getSession()->getFlashBag()->add('infoSuccess','Le rôle '.$adminRole->getName().' a été supprimé!');
                return $this->redirectToRoute('cs_platform_handling_services_roles_administrative_role_viewAll');
            }else
            {   
                $request->getSession()->getFlashBag()->add('infoError','Echec de la suppression du rôle '.$adminRole->getName().' !');
                return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:deleteAdministrativeRole.html.twig', array('role'=>$adminRole, 'form'=>$form->createView()));
            }
        }

        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:deleteAdministrativeRole.html.twig', array('role'=>$adminRole, 'action'=> 'delete', 'form'=>$form->createView()));    
    }

    public function disableAdministrativeRoleAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère le Role d'id $id
        $adminRole = $em->getRepository('CSPlatformHandlingBundle:AdministrativeRole')->find($id);

        if (null === $adminRole) {
        throw new NotFoundHttpException("Ce lien est invalide.");
        }
        if($adminRole->getPublished() == false)
        {
            throw new NotFoundHttpException("Ce Role ne peut être désactivé mais doit être supprimé.");
        }

        $form = $this->get('form.factory')->create(AdministrativeRoleConfirmType::class, $adminRole);
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            { 
                /* On desactive le Role */
                $adminRole->setIsActive(false);

                $em->flush();

                $request->getSession()->getFlashBag()->add('infoSuccess','Le Role '.$adminRole->getName().' a été désactivé!');
                return $this->redirectToRoute('cs_platform_handling_services_roles_administrative_role_view', array('id'=>$id));
            }else
            {   
                $request->getSession()->getFlashBag()->add('infoError','Echec de la désactivation du Role '.$adminRole->getName().' !');
                return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:disableAdministrativeRole.html.twig', array('role'=>$adminRole, 'form'=>$form->createView()));
            }
        }

        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:disableAdministrativeRole.html.twig', array('role'=>$adminRole, 'action'=> 'disable', 'form'=>$form->createView()));    
    }

    public function enableAdministrativeRoleAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère le Role d'id $id
        $adminRole = $em->getRepository('CSPlatformHandlingBundle:AdministrativeRole')->find($id);

        if (null === $adminRole) {
        throw new NotFoundHttpException("Ce lien est invalide.");
        }
        if($adminRole->getIsActive() == true)
        {
            throw new NotFoundHttpException("Ce Role est déjà activé.");
        }

        $form = $this->get('form.factory')->create(AdministrativeRoleConfirmType::class, $adminRole);
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            { 
                /* On active le Role */
                $adminRole->setIsActive(true);

                $em->flush();

                $request->getSession()->getFlashBag()->add('infoSuccess','Le Role '.$adminRole->getName().' a été réactivé!');
                return $this->redirectToRoute('cs_platform_handling_services_roles_administrative_role_view', array('id'=>$id));
            }else
            {   
                $request->getSession()->getFlashBag()->add('infoError','Echec de la réactivation du Role '.$adminRole->getName().' !');
                return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:enableAdministrativeRole.html.twig', array('role'=>$adminRole, 'form'=>$form->createView()));
            }
        }

        return $this->render('CSPlatformHandlingBundle:ServicesAndRoles:enableAdministrativeRole.html.twig', array('role'=>$adminRole, 'action'=> 'enable', 'form'=>$form->createView()));    
    }


}
