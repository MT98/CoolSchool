<?php

namespace CS\CustomersPlatformBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;
use Craue\FormFlowBundle\Event\PostBindRequestEvent;
use Craue\FormFlowBundle\Form\FormFlowEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use CS\CustomersPlatformBundle\Entity\CustomerUser;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use CS\PlatformHandlingBundle\Entity\CustomersService;



class RegisterSchoolFlow extends FormFlow implements EventSubscriberInterface {

	private $doctrine;

	public function getDoctrine()
	{
		return $this->doctrine;
	}

	protected function loadStepsConfig() {
		return array(
			array(
				'label' => 'Informations sur l\'école',
				'form_type' => 'CS\CustomersPlatformBundle\Form\SchoolType',
			),
			array(
				'label' => 'Email de l\'administrateur',
				'form_type' => 'CS\CustomersPlatformBundle\Form\CustomerUserType',
			),
			array(
				'label' => 'Informations sur l\'administrateur',
				'form_type' => 'CS\CustomersPlatformBundle\Form\CustomerUserType',	
			)/*,
			array(
				'label' => 'subscriptions',
				'form_type' => 'CS\CustomersPlatformBundle\Form\CustomersServiceType',	
			)*/,
			array(
				'label' => 'Confirmation',
			),
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function setEventDispatcher(EventDispatcherInterface $dispatcher) {
		parent::setEventDispatcher($dispatcher);
		$dispatcher->addSubscriber($this);
	}
	/**
	 * {@inheritDoc}
	 */
	public static function getSubscribedEvents() {
		return array(
			FormFlowEvents::POST_BIND_REQUEST => 'onPostBindRequest',
		);
	}
	/* Quand l'utilisateur valide un step pour passer au suivant */
	public function onPostBindRequest(PostBindRequestEvent $event) {
		
		$step = $event->getStepNumber();
		
		if($step === 2)
		{
			/* on récupère l'eamil */
			 /* s'il existe on va sur l'étape suivante avec tous les champs disbled */
			 /*sinon on va sur l'etape suivante avec le champ email disbled */
			 $formData = $event->getFormData();
			
			$email = $formData->getCustomerUser()->getEmail();

			/*On récupère l'utilisateur associé */
			$em = $this->getDoctrine()->getEntityManager();
			$customerUser = $em->getRepository('CSCustomersPlatformBundle:CustomerUser')->findByEmail($email);

			
			/*s'il existe */
			if($customerUser != null)
			{
				/* On met les valeurs dans les champs */
				$formData->setCustomerUser($customerUser);
			}
		}
			
	}

	public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
	{
		$this->doctrine = $doctrine;
	}

}