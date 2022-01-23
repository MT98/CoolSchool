<?php

namespace CS\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CS\CoreBundle\Entity\User;
use CS\CoreBundle\Form\UserType;
use CS\CoreBundle\Entity\ForgotCredentialsForAdministration;
use CS\CoreBundle\Form\ForgotCredentialsForAdministrationType;
use CS\CoreBundle\Entity\CredentialsComponentsForChange;
use CS\CoreBundle\Form\CredentialsComponentsForChangeType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use CS\PlatformHandlingBundle\Entity\CoolSchoolEmployee;



class SecurityController extends Controller
{

    public function changeCredentialsForAdministrationAction(Request $request, string $what, string $code)
    {
        /* On reçoit la requête puis on vérifie s'il existe bien et qu'il n'as pas expiré */
        /* si oui on vérifie de quel type de recupèration, il s'agit (username/password) */
            /* on affiche le formulaire de changement de (username/password) */
            /* s'il soummet, on procéde aux changements et on fait expiré le lien*/
            /* et on le renvoie à la page de connexion avec une notification de success */
            /* si le changement ne reussit pas, on lui renvoie un message d'erreur*/
        /* si le lien n'est pas correct */
        /* on lui envoie un message d'erreur en fonction du cas spécifique */
        
        if($request->isMethod('GET'))
        {
            $em = $this->getDoctrine()->getEntityManager();
            /* On hash le code pour le comparer à celui stocker dans la base de donnees*/
            $hashedCode = hash('sha512', $code);
            $forgot = $em->getRepository('CSCoreBundle:ForgotCredentialsForAdministration')->getNotUsedItem($what, $hashedCode);
            if($forgot == null)
            {
                throw new NotFoundHttpException("Ce lien n'est pas valide car il n'existe pas où il a expiré! \n Merci de recommencer la procédure de récupèration!");
            }else
            {
                /* le lien est valide */
                $credentials = new CredentialsComponentsForChange();
                /* On y met le code nous permettant de le retrouver */
                $credentials->setCode($code);
                $credentials->setWhat($what);
                $form = $this->get('form.factory')->create(CredentialsComponentsForChangeType::class, $credentials);
                
                return $this->render('CSCoreBundle:Security:changeCredentialsForAdministration.html.twig', array('form'=>$form->createView(), 'what'=>$what));
            }
        }elseif($request->isMethod('POST'))
        {
            $credentials = new CredentialsComponentsForChange();
            $form = $this->get('form.factory')->create(CredentialsComponentsForChangeType::class, $credentials);
            
            $form->handleRequest($request);
            /* si la confirmation est correcte */
            /* il faudra aussi vérifier que l'ip n'a pas fait de requêtes, il y a 15minutes */
            if($form->isValid())
            {
                $code = $credentials->getCode();
                $what = $credentials->getWhat();

                $em = $this->getDoctrine()->getEntityManager();
                /* On hash le code pour le comparer à celui stocker dans la base de donnees*/
                $hashedCode = hash('sha512', $code);
                $forgot = $em->getRepository('CSCoreBundle:ForgotCredentialsForAdministration')->getNotUsedItemWithEmployee($what, $hashedCode);
                if($forgot == null)
                {
                    throw new NotFoundHttpException("Ce code n'est pas valide car il n'existe pas où il a expiré! \n Merci de recommencer la procédure de récupèration!");
                }else
                {
                    /*On recupère l'employé et on modifie ses coordonées */
                    $employee = $forgot->getEmployee();
                    if($what == "password")
                    {
                        /* Faire attention lorsque ce dernier a été hashé */
                        $employee->setPassword($credentials->getPassword());
                    }else if($what == "username")
                    {
                        $employee->setUsername($credentials->getUsername());
                    }

                    /* On fait expiré le lien */
                    $forgot->setExpired(true);
                    $forgot->setExpirationDate(new \DateTime());

                    /* On actualise la base de données */
                    $em->persist($employee);
                    $em->flush();

                    $request->getSession()->getFlashBag()->add('infoSuccess','Vos infos de connexion ont été modifiées avec succés !');
                    return $this->redirectToRoute('login_for_administration');            

                }
            }else
            {
                $request->getSession()->getFlashBag()->add('infoError','Echec de la modification des paramètres de connexion !');
                return $this->render('CSCoreBundle:Security:changeCredentialsForAdministration.html.twig', array('form'=>$form->createView(), 'what'=>$credentials->getWhat()));                    
            }
            
        }
    }



    /* Cette gère la création du formulaire procédant à la premiere étape de récupèration de mot de passe ou username
    et l'envoie du lien de récupèration */
    public function forgotForAdministrationAction(Request $request)
    {
        $forgot = new ForgotCredentialsForAdministration();
        $form = $this->get('form.factory')->create(ForgotCredentialsForAdministrationType::class, $forgot);

        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            /* il faudra aussi vérifier que l'ip n'a pas fait de requêtes, il y a 15minutes */
            if($form->isValid())
            {
                /*
                on cherche à savoir ce qu'il a oublié
                on cree un code de recuperation unique qui expire à une date precise et qui doit etre encodé 
                on le sauvegarde
                on lui envoie un mail de recuperation
                apres on procéde à la récuperation quand on reçoit la requete
                permettrre la reinitialisation certaines configurations
                */

                /* On compte le nombre d'enregistrement que nous allons utliser pour l'unicité du code */
                $em = $this->getDoctrine()->getEntityManager();
                $counter = $em->getRepository('CSCoreBundle:ForgotCredentialsForAdministration')->count() +1;
                if($forgot->getWhat() == "password" || $forgot->getWhat() == "username" )
                {
                    /* On y ajoute l'email et l'indice d'unicité */
                    /* On supprime les caracteres indesirees pour les liens */
                    $email = $forgot->getEmail();
                    $search = array('@','.',' ');
                    $replace = array('-','-','-');
                    $code = str_replace($search, $replace, $email);
                    $code .= $counter;

                    /* on random 10 chiffres ou lettres */
                    for($i=0; $i<10; $i++)
                    {
                        $choice = rand(1,3);
                        /* chiffre*/
                        if($choice == 1)
                        {
                            $code.= rand(0,9);
                        }elseif($choice == 2) /* lettres majuscules */
                        {
                            $number = rand(65,90);
                            /* On concaténe l'equivalent en caractére ASCII */
                            $code.= chr($number);
                        }elseif($choice == 3)
                        {
                            $number = rand(97,122);
                            /* On concaténe l'equivalent en caractére ASCII */
                            $code.= chr($number);
                        }
                    }

                    /* on le hash */
                    $hashedCode = hash('sha512', $code);

                    $forgot->setCode($hashedCode);

                    /* Le lien sera actif pour deux jours */
                    $forgot->setExpirationDate(new \DateTime());
                    $forgot->setExpirationDate($forgot->getExpirationDate()->modify('+2 days'));

                    /* on lie le lien a l'employé */
                    $employee = $em->getRepository('CSPlatformHandlingBundle:CoolSchoolEmployee')->findOneByEmail($forgot->getEmail());
                    if($employee != null)
                    {
                        $forgot->setEmployee($employee);
                        $send = $this->get('cs_platform_handling.email.mailer')->sendForgotCredentialsForAdministrationNotification($employee->getEmail(), $employee->getName(), $forgot->getWhat(),$code);
                        if($send == true)
                        {
                            $em->persist($forgot);
                            $em->flush();

                            $request->getSession()->getFlashBag()->add('infoSuccess','Le lien de récupèration a été envoyé avec success à '.$forgot->getEmail().' !');

                            /* On reinitialise le formulaire */
                            $forgot = new ForgotCredentialsForAdministration();
                            $form = $this->get('form.factory')->create(ForgotCredentialsForAdministrationType::class, $forgot);
                            return $this->render('CSCoreBundle:Security:forgotForAdministration.html.twig', array('form'=>$form->createView()));            
                        }
                    }
                }                

            }else
            {
                $request->getSession()->getFlashBag()->add('infoError','Echec d\'envoie du mail de récupèration à '.$forgot->getEmail().' !');
                return $this->render('CSCoreBundle:Security:forgotForAdministration.html.twig', array('form'=>$form->createView()));            
            }
        }

        return $this->render('CSCoreBundle:Security:forgotForAdministration.html.twig', array('form'=>$form->createView()));
    }

    public function loginForAdministrationAction(Request $request)
    {
        /* Si le visiteur est deja identifié, on le redirige vers la page d'accueil */
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            return $this->redirectToRoute('cs_platform_handling_coolschool_team_employee_viewAll');   
        }

        /* le service authentification_utils permet de recuperer le nom d'utilisateur et l'erreur dans
         le cas où le formulaire soumis est invalide
        */
        $authenticationUtils = $this->get('security.authentication_utils');

        /* form handling spams */
        $user = new User();
        $form = $this->get('form.factory')->create(UserType::class, $user);

        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {
                /* redirecting to login_check by using POST method */
                return $this->redirectToRoute('login_check_for_administration', [
                    'request' => $request
                ], 307);
            }
        }


        return $this->render('CSCoreBundle:Security:login.html.twig', array(
            'last_username' => $authenticationUtils->getLastUserName(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'form' => $form->createView()
        ));
    }
    
}
