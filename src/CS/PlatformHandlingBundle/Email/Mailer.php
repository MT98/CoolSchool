<?php
// src/CS/PlatformHandlingBundle/Email/Mailer.php

namespace CS\PlatformHandlingBundle\Email;

use CS\PlatformHandlingBundle\Entity\CoolSchoolEmployee;


class Mailer
{
  /**
   * @var \Swift_Mailer
   */
  private $mailer;
  private $confirmationLinks;
  private $mailingFrom;

  public function __construct(\Swift_Mailer $mailer, $confirmationLinks, $mailingFrom)
  {
    $this->mailer = $mailer;
    $this->confirmationLinks = $confirmationLinks;
    $this->mailingFrom = $mailingFrom;
  }

  public function sendConfirmationAccount(CoolSchoolEmployee $employee)
  {
      $message = new \Swift_Message(
        'Confirmation de compte chez MarianeSchool',
        "Attention! \n Lisez-bien ce qui suit pour éviter tout désagrément car ses infos ne seront plus modifiables aprés activation de votre compte.\n"
        ."Salut ".$employee->getName(). 
        "\n\nVos infos sont les suivantes: \n".
        /* photo ici */
        "Pays d'origine: ".$employee->getCountry()->getName().
        "\nPrénom: ".$employee->getFirstName().
        "\nNom: ".$employee->getLastName().
        "\nMatricule chez MarianeSchool: ".$employee->getId().

        "\n\nLes infos suivantes sont parcontre modifiable à tout moment par vous-même donc pas la peine de les signaler".
        "\n adresse: ".$employee->getAddress().
        "\n telephone: ".$employee->getTelephone().

        
        
        "\n\n\n Si vous êtes d'accord avec vos infos personnelles, veuillez confirmer votre compte pour jouer votre rôle chez MarianeSchool en cliquant sur le lien ci-dessous; \n"   
        . "Cliquez ". "<a href=\"".$this->confirmationLinks["employee"].$employee->getConfirmationToken()."\"> ici!</a>"
        ."\n Aprés avoir confirmé, vous vous connecterez avec ce mot de passe ".$employee->getPassword()." qu'on vous conseille de modifier pour en avoir un plus mémorable pour vous!"
      
      
        ."\n\n\n Si vous n'êtes pas d'accord, envoyez-nous un email en précisant votre matricule ou contactez-nous au 780193927"
      );
  
      $message
        ->addTo($employee->getEmail()) 
        ->addFrom($this->mailingFrom)
      ;
  
      $this->mailer->send($message);
  }

  public function sendUpdatingNotification(CoolSchoolEmployee $employee)
  {
 
      $message = new \Swift_Message(
        'Mise à jour de votre compte chez CoolSchool',
        "Salut ".$employee->getName(). "! Vos infos personnelles ont été modifiées par CoolSchool! Veuillez-vous reconnectez pour consulter votre profil!"
      );

      $message
        ->addTo($employee->getEmail()) // Ici bien sûr il faudrait un attribut "email", j'utilise "author" à la place
        ->addFrom($this->mailingFrom)
      ;

      $this->mailer->send($message);
  }


  public function sendErrorNotification($email)
  {
      $message = new \Swift_Message(
        'Error',
        "Désolé notre message d'activation était une erreur nous nous excusons de tout désagrément!"
      );
  
      $message
        ->addTo($email) // Ici bien sûr il faudrait un attribut "email", j'utilise "author" à la place
        ->addFrom($this->mailingFrom)
      ;
  
      $this->mailer->send($message);
  }

  public function sendDisablingNotification(CoolSchoolEmployee $employee)
  {
      $message = new \Swift_Message(
        'Désactivation de votre compte chez CoolSchool',
        "Salut ".$employee->getName(). "! Votre compte vient d'être désactivé par CoolSchool!"
      );
  
      $message
        ->addTo($employee->getEmail()) // Ici bien sûr il faudrait un attribut "email", j'utilise "author" à la place
        ->addFrom($this->mailingFrom)
      ;
  
      $this->mailer->send($message);
  }

  public function sendEnablingNotification(CoolSchoolEmployee $employee)
  {
      $message = new \Swift_Message(
        'Réactivation de votre compte chez MarianeSchool',
        "Salut ".$employee->getName(). "! Votre compte vient d'être réactivé par CoolSchool!"
      );
  
      $message
        ->addTo($employee->getEmail()) // Ici bien sûr il faudrait un attribut "email", j'utilise "author" à la place
        ->addFrom($this->mailingFrom)
      ;
  
      $this->mailer->send($message);
  }

  public function sendForgotCredentialsForAdministrationNotification(string $email, string $name, string $what, string $code)
  {
      $message = new \Swift_Message(
        'Réinitialisation des paramétres de connexion!',
        "Salut ".$name. "! Cliquez sur le lien ci-dessous pour réinitialiser vos paramètres de connexion. \n".
        $this->confirmationLinks["forgotCredentialsForAdministration"]."/".$what."/".$code
      );

      $message
        ->addTo($email) // Ici bien sûr il faudrait un attribut "email", j'utilise "author" à la place
        ->addFrom($this->mailingFrom)
      ;

      $this->mailer->send($message);
      return true;
  }

  
}
