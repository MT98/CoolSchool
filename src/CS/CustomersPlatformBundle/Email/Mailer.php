<?php

namespace CS\CustomersPlatformBundle\Email;

use CS\CustomersPlatformBundle\Entity\CustomersUser;

class Mailer{

    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    private $linkPrefixes;
    private $mailingFrom;

    public function __construct(\Swift_Mailer $mailer, $linkPrefixes, $mailingFrom)
    {
        $this->mailer = $mailer;
        $this->linkPrefixes = $linkPrefixes;
        $this->mailingFrom = $mailingFrom;
    }

    public function sendConfirmationAccount(CustomersUser $customersUser)
    {
        $message = new \Swift_Message(
            'Confirmation de compte chez DjigueulSchool',
          
            "Salut ".$customersUser->getName()."!\n". 
            "Félicitations votre école vous a ajouté en tant qu'utilisateur dans leur plateforme scolaire. \n"
            ."Avec toutes les innovations que l'on a apportées pour vous, l'école va devenir un réel plaisir. \n"
        
            
            ."\n\n\n Prêt pour l'aventure? Veuillez confirmer votre compte chez DjigueulSchool en cliquant sur le lien ci-dessous; \n"   
            . "Cliquez ". "<a href=\"".$this->linkPrefixes["customersUser"].$customersUser->getConfirmationToken()."\"> ici!</a>"
            ."\n Aprés avoir confirmé, vous vous connecterez avec ce mot de passe ".$customersUser->getPassword()." qu'on vous conseille de modifier pour en avoir un plus mémorable et sûre pour vous!"
        );
    
        $message
            ->addTo($customersUser->getEmail()) 
            ->addFrom($this->mailingFrom)
        ;
    
        $this->mailer->send($message);
    }
}