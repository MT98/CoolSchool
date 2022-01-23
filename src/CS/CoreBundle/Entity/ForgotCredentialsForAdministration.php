<?php

namespace CS\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ForgotCredentialsForAdministration
 *
 * @ORM\Table(name="forgot_credentials_for_administration")
 * @ORM\Entity(repositoryClass="CS\CoreBundle\Repository\ForgotCredentialsForAdministrationRepository")
 */
class ForgotCredentialsForAdministration
{

    /**
     * @ORM\ManyToOne(targetEntity="CS\PlatformHandlingBundle\Entity\CoolSchoolEmployee")
     * @ORM\JoinColumn(nullable=false)
     */
    private $employee;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="expired", type="boolean", nullable=true)
     */
    private $expired;
    
    private $email;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="what", type="string", length=255)
     */
    private $what;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     */
    private $code;

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
     * Set what
     *
     * @param string $what
     *
     * @return ForgotCredentialsForAdministration
     */
    public function setWhat($what)
    {
        $this->what = $what;

        return $this;
    }

    /**
     * Get what
     *
     * @return string
     */
    public function getWhat()
    {
        return $this->what;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return ForgotCredentialsForAdministration
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set expirationDate
     *
     * @param \DateTime $expirationDate
     *
     * @return ForgotCredentialsForAdministration
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

    public function getEmail()
    {
        return $this->email;
    }
    public function setEmail($email)
    {
        $this->email = $email;
        return;
    }

    /**
     * Set employee
     *
     * @param \CS\PlatformHandlingBundle\Entity\CoolSchoolEmployee $employee
     *
     * @return ForgotCredentialsForAdministration
     */
    public function setEmployee(\CS\PlatformHandlingBundle\Entity\CoolSchoolEmployee $employee)
    {
        $this->employee = $employee;

        return $this;
    }

    /**
     * Get employee
     *
     * @return \CS\PlatformHandlingBundle\Entity\CoolSchoolEmployee
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * Set expired
     *
     * @param boolean $expired
     *
     * @return ForgotCredentialsForAdministration
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


    public function __construct()
    {
        $this->setExpired(false);
    }
}
