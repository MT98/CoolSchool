<?php

namespace CS\CustomersPlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Works
 *
 * @ORM\Table(name="work")
 * @ORM\Entity(repositoryClass="CS\CustomersPlatformBundle\Repository\WorkRepository")
 */
class Work
{
    /**
     * @ORM\ManyToOne(targetEntity="CS\CustomersPlatformBundle\Entity\CustomerUser", inversedBy="works")
     */
    private $customerUser;

    /**
     * @ORM\ManyToOne(targetEntity="CS\PlatformHandlingBundle\Entity\CustomersRole", inversedBy="works")
     */
    private $role;

    /**
     * @ORM\ManyToOne(targetEntity="CS\CustomersPlatformBundle\Entity\School", inversedBy="works")
     */
    private $school;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startDate", type="datetimetz")
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endDate", type="datetimetz", nullable=true)
     */
    private $endDate;

    /**
     * @var Boolean
     *
     * @ORM\Column(name="expired", type="boolean")
     */
    private $expired;


    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /* Constructeur */
    public function __construct()
    {
       $this->setStartDate(new \Datetime());
       $this->setExpired(false); 
    }



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
     * Set role
     *
     * @param \CS\PlatformHandlingBundle\Entity\CustomersRole $role
     *
     * @return Work
     */
    public function setRole(\CS\PlatformHandlingBundle\Entity\CustomersRole $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \CS\PlatformHandlingBundle\Entity\CustomersRole
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set school
     *
     * @param \CS\CustomersPlatformBundle\Entity\School $school
     *
     * @return Work
     */
    public function setSchool(\CS\CustomersPlatformBundle\Entity\School $school = null)
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

    /**
     * Set customerUser
     *
     * @param \CS\CustomersPlatformBundle\Entity\CustomerUser $customerUser
     *
     * @return Work
     */
    public function setCustomerUser(\CS\CustomersPlatformBundle\Entity\CustomerUser $customerUser = null)
    {
        $this->customerUser = $customerUser;

        return $this;
    }

    /**
     * Get customerUser
     *
     * @return \CS\CustomersPlatformBundle\Entity\CustomerUser
     */
    public function getCustomerUser()
    {
        return $this->customerUser;
    }


    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Work
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return Work
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set expired
     *
     * @param boolean $expired
     *
     * @return Work
     */
    public function setExpired($expired)
    {
        $this->expired = $expired;

        return $this;
    }

    /**
     * Get expired
     *
     * @return boolean
     */
    public function getExpired()
    {
        return $this->expired;
    }
}
