<?php

namespace CS\CustomersPlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Subscription
 *
 * @ORM\Table(name="subscription")
 * @ORM\Entity(repositoryClass="CS\CustomersPlatformBundle\Repository\SubscriptionRepository")
 */
class Subscription
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="CS\CustomersPlatformBundle\Entity\School",inversedBy="subscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $school;

    /**
     * @ORM\ManyToOne(targetEntity="CS\PlatformHandlingBundle\Entity\CustomersService", inversedBy="subscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customersService;



    /**
     * @var \DateTime
     *
     * @ORM\Column(name="subscriptionDate", type="datetimetz", nullable=true)
     */
    private $subscriptionDate;

    /**
     * @var bool
     *
     * @ORM\Column(name="expired", type="boolean")
     */
    private $expired;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expirationDate", type="datetimetz")
     */
    private $expirationDate;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set subscriptionDate
     *
     * @param \DateTime $subscriptionDate
     *
     * @return Subscription
     */
    public function setSubscriptionDate($subscriptionDate)
    {
        $this->subscriptionDate = $subscriptionDate;

        return $this;
    }

    /**
     * Get subscriptionDate
     *
     * @return \DateTime
     */
    public function getSubscriptionDate()
    {
        return $this->subscriptionDate;
    }

    /**
     * Set expired
     *
     * @param boolean $expired
     *
     * @return Subscription
     */
    public function setExpired($expired)
    {
        $this->expired = $expired;

        return $this;
    }

    /**
     * Get expired
     *
     * @return bool
     */
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * Set expirationDate
     *
     * @param \DateTime $expirationDate
     *
     * @return Subscription
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * Get expirationDate
     *
     * @return \DateTime
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }


    /**
     * Set customersService
     *
     * @param \CS\PlatformHandlingBundle\Entity\CustomersService $customersService
     *
     * @return Subscription
     */
    public function setCustomersService(\CS\PlatformHandlingBundle\Entity\CustomersService $customersService)
    {
        $this->customersService = $customersService;

        return $this;
    }

    /**
     * Get customersService
     *
     * @return \CS\PlatformHandlingBundle\Entity\CustomersService
     */
    public function getCustomersService()
    {
        return $this->customersService;
    }

    /**
     * Set school
     * Attention cette fonction doit être utiliser indirectement par CS/CustomersPlatformBundle/School::addSubscription()
     * Pour faire la liaison en les deux entités en relation ManyToOne
     *
     * @param \CS\CustomersPlatformBundle\Entity\School $school
     *
     * @return Subscription
     */
    public function setSchool(\CS\CustomersPlatformBundle\Entity\School $school)
    {
        $this->school = $school;

        return $this;
    }

    /**
     * Get school
     *
     * @return \CS\CustomersPlatformBundle\Entity\School
     */
    public function getSchool()
    {
        return $this->school;
    }

    public function __construct()
    {
        $this->setSubscriptionDate(new \DateTime());

        $this->setExpired(false);

        /* Le service sera disponible pour un mois avant de faire des facturations */
        $this->setExpirationDate(new \DateTime());
        $this->setExpirationDate($this->getExpirationDate()->modify('+1 month')->modify('+5 days'));
    }
}
