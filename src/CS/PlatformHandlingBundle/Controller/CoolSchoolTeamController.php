<?php

namespace CS\PlatformHandlingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use CS\PlatformHandlingBundle\Entity\CoolSchoolEmployee;
use CS\PlatformHandlingBundle\Form\CoolSchoolEmployeeType;
use CS\PlatformHandlingBundle\Form\CoolSchoolEmployeeModifyType;
use CS\PlatformHandlingBundle\Form\CoolSchoolEmployeeConfirmType;

class CoolSchoolTeamController extends Controller
{
    
    public function addEmployeeAction(Request $request)
    {
        /* aprés avoir soumis le formulaire, on doit le confirmer */
        $employee = new CoolSchoolEmployee();

        $form = $this->get('form.factory')->create(CoolSchoolEmployeeType::class, $employee);

        /* lors de la soumission de formulaire */
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($employee);
                $em->flush();

                $this->get('cs_platform_handling.email.mailer')->sendConfirmationAccount($employee);

                $request->getSession()->getFlashBag()->add('infoSuccess', "L'employé ". $employee->getName() . " a été ajouté avec succés!");

               return $this->redirectToRoute('cs_platform_handling_coolschool_team_employee_view', array("id"=>$id));
            }else
            {
                $request->getSession()->getFlashBag()->add('infoError', "Echec de l'ajout de l'employé ". $employee->getName() . " !");
                return $this->render('CSPlatformHandlingBundle:CoolSchoolTeam:addEmployee.html.twig', array('action'=> 'add', 'form'=>$form->createView()));
            }
        }


        return $this->render('CSPlatformHandlingBundle:CoolSchoolTeam:addEmployee.html.twig', array('action'=> 'add', 'form'=>$form->createView()));
    }

    public function updateEmployeeAction($id, Request $request)
    {
        /* si le compte n'est pas activé, on peut tout modifier */
            /* on affiche le formulaire */
            /* il soumet et on vérifie si tout est correct */
                /* si oui on modifie */
                /* sinon on lui renvoie le formulaire avec l'erreur */
        /* sinon , on ne peut modifier que l'address et le telephone et les rôles*/

        

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("CSPlatformHandlingBundle:CoolSchoolEmployee");
        $employee = $repository->find($id); 
         
        if($employee == null)
        {
            throw new NotFoundHttpException("Ce lien est invalide!");                
        }
        /* On recupère l'ancien email, nom et prenom */
        $previousEmail = $employee->getEmail();
        $previousFirstName = $employee->getFirstName();
        $previousLastName = $employee->getLastName();

        $form = $this->get('form.factory')->create(CoolSchoolEmployeeModifyType::class, $employee);

        /* lors de la soumission de formulaire */
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            /* si respecte toutes les contraintes */
            if($form->isValid())
            {
                /*On met à jour la date de mise à jour */
                $this->setUpdatedAt(new \DateTime());
                /* si l'ancien email est different de l'email actuel du formulaire */
                if($previousEmail != $employee->getEmail())
                {
                    if($employee->isEnabled() == false)
                    {
                        $employee->setPassword($employee->createPassword());
                        $em->flush();

                        $this->get('cs_platform_handling.email.mailer')->sendConfirmationAccount($employee);
                        $this->get('cs_platform_handling.email.mailer')->sendErrorNotification($previousEmail);
                        $request->getSession()->getFlashBag()->add('infoSuccess', "Le compte de l'employé(e)". $previousFirstName . " ". $previousLastName . " a été modifié(e) avec succés!");
                        return $this->redirectToRoute('cs_platform_handling_coolschool_team_employee_view', array("id"=>$id));
                    }else
                    {
                        $request->getSession()->getFlashBag()->add('infoError', "Echec de la modification du compte de l'employé(e)". $previousFirstName . " ". $previousLastName . " !");        
                        return $this->render('CSPlatformHandlingBundle:CoolSchoolTeam:updateEmployee.html.twig', array('action'=>'update','form'=>$form->createView()));                    
                    }
                    
                }/* L'email est le même que le précédent */
                else{
                    $em->flush();

                    /* On envoie une notification à l'employé!*/
                    if($employee->isEnabled() == true)
                    {
                        $this->get('cs_platform_handling.email.mailer')->sendUpdatingNotification($employee);
                    }else
                    {
                        $this->get('cs_platform_handling.email.mailer')->sendConfirmationAccount($employee);
                    }

                    $request->getSession()->getFlashBag()->add('infoSuccess', "Le compte de l'employé(e)". $previousFirstName . " ". $previousLastName . " a été modifié(e) avec succés!");
                    return $this->redirectToRoute('cs_platform_handling_coolschool_team_employee_view', array("id"=>$id));
                }
            }else
            {
                $request->getSession()->getFlashBag()->add('infoError', "Echec de la modification du compte de l'employé(e)". $previousFirstName . " ". $previousLastName . " !");        
                return $this->redirectToRoute('cs_platform_handling_coolschool_team_employee_update', array("id"=>$id));
            }
        }


        return $this->render('CSPlatformHandlingBundle:CoolSchoolTeam:updateEmployee.html.twig', array('action'=>'update','form'=>$form->createView()));
        
    }

    public function viewEmployeeAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'employé $id
        $employee = $em->getRepository('CSPlatformHandlingBundle:CoolSchoolEmployee')->getEmployeeWithAllInfo($id);
        if (null === $employee) {
        throw new NotFoundHttpException("Ce lien n'est pas valide!");
        }                

        return $this->render('CSPlatformHandlingBundle:CoolSchoolTeam:viewEmployee.html.twig', array('employee' => $employee));            
    }
     

    public function viewEmployeesAction()
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère tous les employes 

        $employees = $em->getRepository('CSPlatformHandlingBundle:CoolSchoolEmployee')->getAllEmployees();

        return $this->render('CSPlatformHandlingBundle:CoolSchoolTeam:viewEmployees.html.twig', array('employees' => $employees));    
    
    }

    
    public function disableEmployeeAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'employé(e) $id
        $employee = $em->getRepository('CSPlatformHandlingBundle:CoolSchoolEmployee')->find($id);

        if (null === $employee) {
        throw new NotFoundHttpException("Ce lien est invalide.");
        }
        if($employee->getExpired() == true)
        {
            throw new NotFoundHttpException("Ce compte est déjà désactivé.");
        }

        $form = $this->get('form.factory')->create(CoolSchoolEmployeeConfirmType::class, $employee);
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {

                $employee->setExpired(true);
                $employee->setExpirationDate(new \DateTime());
                $em->flush();

                $this->get('cs_platform_handling.email.mailer')->sendDisablingNotification($employee);

                $request->getSession()->getFlashBag()->add('infoSuccess','Le compte de l\'employé(e) '.$employee->getName().' a été désactivé!');
                return $this->redirectToRoute('cs_platform_handling_coolschool_team_employee_view', array('id'=>$id));
            }else
            {   
                $request->getSession()->getFlashBag()->add('infoError','Echec de la désactivation du compte de l\'employé(e) '.$employee->getName().' !');
                return $this->render('CSPlatformHandlingBundle:CoolSchoolTeam:disableEmployee.html.twig', array('action'=>'disable','employee'=>$employee, 'form'=>$form->createView()));
            }
        }

        // Ici, on gérera la désactivation de l'employé(e) en question

        return $this->render('CSPlatformHandlingBundle:CoolSchoolTeam:disableEmployee.html.twig', array('action'=>'disable','employee'=>$employee, 'form'=>$form->createView()));

    }

    public function enableEmployeeAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'employé(e) $id
        $employee = $em->getRepository('CSPlatformHandlingBundle:CoolSchoolEmployee')->find($id);

        if (null === $employee) {
        throw new NotFoundHttpException("Ce lien est invalide.");
        }
        if($employee->getExpired() == false)
        {
            throw new NotFoundHttpException("Ce compte n'est pas expiré!.");
        }

        $form = $this->get('form.factory')->create(CoolSchoolEmployeeConfirmType::class, $employee);
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {

                $employee->setExpired(false);
                $employee->setUpdatedAt(new \DateTime());
                $em->flush();

                $this->get('cs_platform_handling.email.mailer')->sendEnablingNotification($employee);

                $request->getSession()->getFlashBag()->add('infoSuccess','Le compte de l\'employé(e) '.$employee->getName().' a été réactivé!');
                return $this->redirectToRoute('cs_platform_handling_coolschool_team_employee_view', array('id'=>$id));
            }else
            {   
                $request->getSession()->getFlashBag()->add('infoError','Echec de la réactivation du compte de l\'employé(e) '.$employee->getName().' !');
                return $this->render('CSPlatformHandlingBundle:CoolSchoolTeam:enableEmployee.html.twig', array('action'=>'enable','employee'=>$employee, 'form'=>$form->createView()));
            }
        }

        // Ici, on gérera la désactivation de l'employé(e) en question

        return $this->render('CSPlatformHandlingBundle:CoolSchoolTeam:enableEmployee.html.twig', array('action'=>'enable','employee'=>$employee, 'form'=>$form->createView()));

    }

    public function deleteEmployeeAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'employé(e) $id
        $employee = $em->getRepository('CSPlatformHandlingBundle:CoolSchoolEmployee')->find($id);

        if (null === $employee) {
        throw new NotFoundHttpException("Ce lien est invalide.");
        }

        if($employee->getIsActive() == true)
        {
            throw new NotFoundHttpException("Ce compte ne peut plus être supprimé mais peut être désactivé.");
        }

        $form = $this->get('form.factory')->create(CoolSchoolEmployeeConfirmType::class, $employee);
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {

                /*On supprime l'employé et sa photo (automatique, cascade)*/
                $em->remove($employee);

                /* on met à jour */
                $em->flush();
                $this->get('cs_platform_handling.email.mailer')->sendErrorNotification($employee->getEmail());

                $request->getSession()->getFlashBag()->add('infoSuccess','Le compte de l\'employé(e) '.$employee->getName().' a été supprimé!');
                return $this->redirectToRoute('cs_platform_handling_coolschool_team_employee_viewAll');
            }else
            {   
                $request->getSession()->getFlashBag()->add('infoError','Echec de la suppression du compte de l\'employé(e) '.$employee->getName().' !');
                return $this->render('CSPlatformHandlingBundle:CoolSchoolTeam:enableEmployee.html.twig', array('action'=>'delete','employee'=>$employee, 'form'=>$form->createView()));
            }
        }

        // Ici, on gérera la désactivation de l'employé(e) en question

        return $this->render('CSPlatformHandlingBundle:CoolSchoolTeam:enableEmployee.html.twig', array('action'=>'delete','employee'=>$employee, 'form'=>$form->createView()));

    }


    public function confirmAction($confirmationToken, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("CSPlatformHandlingBundle:CoolSchoolEmployee");

        $employee = $repository->getEmployeeRelatedToThisToken($confirmationToken);

        /* si le token ne correspond à aucun compte d'employé */
        if($employee == null)
        {
            throw new NotFoundHttpException("Ce lien est invalide, Veuillez vous faire inscrire par l'équipe de CoolSchool");
        }else
        /* si le token correspond bien à un compte d'employé */
        {
            /* et que le compte ait déjà été activé */
            if($employee->isEnabled() == true)
            {
                throw new NotFoundHttpException("Ce compte a déjà été activé!");                
            }
            /* le compte n'as pas été activé. On le fait tout de suite */
    
            elseif($employee->isEnabled() == false)
            {
                $employee->setActive(true);
                $em->flush();
                $request->getSession()->getFlashBag()->add('infoSuccess', 'Votre compte a été bien activé, veuillez vous connectez avec le mot de passe qu\'on vous a envoyé par mail, '.$employee->getName(). ' !');
                return $this->redirectToRoute("login_for_administration");
            }
            else
            {
                throw new NotFoundHttpException("Cette page n'existe pas!");
            }

        }
    

    }

    


}
