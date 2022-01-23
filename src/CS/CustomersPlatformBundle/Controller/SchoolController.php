<?php

namespace CS\CustomersPlatformBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CS\CustomersPlatformBundle\Entity\RegisterSchool;
use CS\CustomersPlatformBundle\Entity\School;
use CS\CustomersPlatformBundle\Entity\CustomerUser;
use CS\CustomersPlatformBundle\Entity\Work;
use CS\CustomersPlatformBundle\Entity\Subscription;
use CS\PlatformHandlingBundle\Entity\CustomersRole;
use CS\PlatformHandlingBundle\Entity\CustomersService;

class SchoolController extends Controller
{
    public function indexAction()
    {
        return $this->render('CSCustomersPlatformBundle:Default:index.html.twig');
    }
       
    public function addAction(Request $request)
    {
        /* On utilise le service 'cs_customers_platform.form.flow.registerSchool' et craueFormFlowbundle */

        $formData = new RegisterSchool();
        $flow = $this->get('cs_customers_platform.form.flow.registerSchool');

        $flow->bind($formData);

        // form of the current step
        $form = $flow->createForm();
        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            if ($flow->nextStep()) {
                
                // form for the next step
                $form = $flow->createForm();
            } else {

                $em = $this->getDoctrine()->getEntityManager();
                

                // flow finished
				// ...

                /* we reconstitute the normal entities school, administrator, subscriptions from $formData*/
                /* on fait le lien concret entre l'ecole, l'administrateur de l'ecole et les services auxquels l'ecole s'est souscrit */
                /* si l'administrateur est nouveau dans la plateforme, on envoie un message de confirmation de compte par email à l'administrateur
                   sinon on ne fait rien
                */
                /* aprés que l'admin soit connecté, on lui affiche un panel de confirmation de rôle:
                   ce qu'il peut se faire ailleurs
                */
                /* on enregistre les données en BD */

                $school = new School();
                $school = $formData->getSchool();

                $customerUser = new CustomerUser();
                $customerUser = $formData->getCustomerUser();

                /* On récupère les services gratuits et ceux disponibles gratuitement pendant 1 mois */
                $services = $em->getRepository('CSPlatformHandlingBundle:CustomersService')->findBy(array('published'=>true, 'isActive'=>true));

                if($services == null)
                {
                    throw new NotFoundHttpException("Impossible d'enregistrer cette école car aucun service n'est disponible!");
                }
                
                /* l'ecole doit maintenant être souscrit à ces services ($services)*/
                /* et l'administrateur ($customerUser) doit jouer le rôle d'administrateur Général au niveau
                du service CustomersPlatform */

                /*Alors ajoutons les abonnements à ceux de l'ecole*/
                foreach($services as $service)
                {
                    $subscription = new Subscription();
                    /*On fait un lien bidirectionnel entre le service et l'abonnement */
                    $service->addSubscription($subscription);
                    /*On fait un lien bidirectionnel entre l'école et l'abonnement */
                    $school->addSubscription($subscription);

                    /*On les persist */
                    $em->persist($service);
                }

                /* Ajoutons l'admin à l'école et l'ecole à l'admin*/
                $school->addCustomerUser($customerUser);

                /* Ajoutons ce nouveau rôle (work) d'admnistrateur */
                $work = new Work();
                $customerUser->addWork($work);
                $school->addWork($work);

                $adminRole = $em->getRepository('CSPlatformHandlingBundle:CustomersRole')->findOneByCode('AGE');
                $adminRole->addWork($work);
                
                
                /* after we persist all of them without the initial $formData*/	    
                $em->persist($school);
                $em->persist($customerUser);
                $em->persist($work);

                /* on met à jour la base de données */
                $em->flush(); 

                /* si l'administrateur est nouveau dans la plateforme, on lui envoie un mail de validation */
                if($customerUser->getId() == null)
                {
                    $this->get('cs_customers_platform.email.mailer')->sendConfirmationAccount($customerUser);
                }

                $flow->reset(); // remove step data from the session

                $request->getSession()->getFlashBag()->add('infoSuccess', "L'école ". $formData->getSchool()->getName() . " a été ajouté avec succés!");


                
                /*On redirige vers la page de vue */
            }
        }

        return $this->render('CSCustomersPlatformBundle:School:add.html.twig', array(
            'form' => $form->createView(),
            'flow' => $flow,
            'formData' => $formData,
            'action' => 'add'
        ));
		
    }

    public function updateAction($id, Request $request)
    {
        /* On utilise le service 'cs_customers_platform.form.flow.registerSchool' et craueFormFlowbundle */

        /* On récupère les entités de l'école */
        /* s'il existe, on reconstitue les $formData */
            /* On affiche les formulaires */
        /* Sinon 
            /* On envoie un message d'erreur */
        
        

        $em = $this->getDoctrine()->getEntityManager();
        $school = $em->getRepository('CSCustomersPlatformBundle:School')->getSchoolPropertiesWithSubscriptions($id);

        if($school != null)
        {
            /* récupèrons l'administrator */

            $works = $em->getRepository('CSCustomersPlatformBundle:Work')->getWorksBySchoolAndRole($school->getId(), 'AGE');
                        
            if($works == null)
            {
                throw new NotFoundHttpException("Il y a une erreur technique car il n'y a pas d'utilisateur jouant le rôle d'admin Général à l'Ecole ".$school->getName());
            }elseif(count($works) > 1)
            {
                throw new NotFoundHttpException("Il y a une erreur technique car plusieurs utilisateurs jouent le rôle d'admin Général à l'Ecole ".$school->getName());

            }/* TOut est correct */
            /*On doit avoir un seul admin general par ecole */
            else
            {
                $work = $works[0];
                $administrator = $work->getCustomerUser();
            }


        }else
        {
            throw new NotFoundHttpException("Ce lien n'est pas valide!");
        }
        

        
        $formData = new RegisterSchool();
        $formData->setSchool($school);
        $formData->setCustomerUser($administrator);

        $subscriptions = $school->getSubscriptions();
        foreach($subscriptions as $subscription)
        {
            $formData->addCustomersService($subscription->getCustomersService());
        }

        /* On copie l'id de l'ancien administrateur à des fins de comparaisons et de detections de changements */
        $initialAdministatorId = $administrator->getId();

        /* On copie les id des services déjà existants pour éviter l'affectation référentielle 
         car si faisait $initialSubscribedServices = $formData->getCustomersServices(), on aurait une egalité référentielle
        */
        $initialSubscribedServices = array();
        $i=0;
        foreach($formData->getCustomersServices() as $customersService)
        {
            $initialSubscribedServices[$i] = array($customersService->getId(), $customersService->getName());
        }

        /* On copie l'ancien email address de l'administrateur */
        $lastEmail = $administrator->getEmail();


        $flow = $this->get('cs_customers_platform.form.flow.updateSchool');

        $flow->bind($formData);

        // form of the current step
        $form = $flow->createForm();
        if ($flow->isValid($form)) {
                $flow->saveCurrentStepData($form);

                if ($flow->nextStep()) {

                    /* si l'admin a été modifié, on le précise à celui qui est entrain de modifier l'ecole
                    en le lui notifiant à la prochaine étape */
                    if($flow->getCurrentStep() == 3 && $initialAdministatorId != $formData->getCustomerUser()->getId())
                    {
                        $request->getSession()->getFlashBag()->add('infoWarning', "Attention, vous venez de changé l'administrateur. Si ce n'est pas ce que vous vouliez, appuyez sur le bouton 'RECOMMENCER'.");                               
                    }

                    // form for the next step
                    $form = $flow->createForm();
                } else {

                        /* On met à jour les entités récupèrés précedemment au niveau de doctrine gràce au $formData */
                        /* si nous avons un nouveau administrateur */
                        if($lastEmail != $formData->getCustomersUser()->getEmail())
                        {
                            /* On vérifie s'il est dans la base de données */
                            /* Si oui, on l'ajoute */
                            /* on lui donne le rôle d'administrateur et on lui envoie une notification */

                        }
                    /* On fait un flush */

                    /* On envoie une notification */

                }
        }

        return $this->render('CSCustomersPlatformBundle:School:update.html.twig', array(
            'form' => $form->createView(),
            'flow' => $flow,
            'formData' => $formData,
            'initialSubscribedServices' => $initialSubscribedServices,
            'action' => 'update'
        ));
		
    }



    public function deleteAction($id)
    {

    }
    public function disableAction($id)
    {

    }

    public function enableAction($id)
    {

    }

}


