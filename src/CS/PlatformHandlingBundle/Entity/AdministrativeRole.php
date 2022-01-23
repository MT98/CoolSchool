<?php

namespace CS\PlatformHandlingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdministrativeRole
 *
 * @ORM\Table(name="administrative_role")
 * @ORM\Entity(repositoryClass="CS\PlatformHandlingBundle\Repository\AdministrativeRoleRepository")
 */
class AdministrativeRole
{

    /**
     * @ORM\ManyToMany(targetEntity="CS\PlatformHandlingBundle\Entity\AdministrativeRole")
     * 
     */
    private $managedRoles;


    /**
     * @ORM\ManyToMany(targetEntity="CS\PlatformHandlingBundle\Entity\CoolSchoolEmployee", mappedBy="works")
     * 
     */
    private $employees;

    /**
     * @ORM\ManyToOne(targetEntity="CS\PlatformHandlingBundle\Entity\AdministrativeService", inversedBy="roles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $service;


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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(name="foroneuser", type="boolean")
     */
    private $foroneuser;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var bool
     *
     * @ORM\Column(name="published", type="boolean")
     */
    private $published;



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
     * Set name
     *
     * @param string $name
     *
     * @return AdministrativeRole
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return AdministrativeRole
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
     * Set description
     *
     * @param string $description
     *
     * @return AdministrativeRole
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set foroneuser
     *
     * @param boolean $foroneuser
     *
     * @return AdministrativeRole
     */
    public function setForoneuser($foroneuser)
    {
        $this->foroneuser = $foroneuser;

        return $this;
    }

    /**
     * Get foroneuser
     *
     * @return bool
     */
    public function getForoneuser()
    {
        return $this->foroneuser;
    }


    /**
     * Set service
     *
     * @param \CS\PlatformHandlingBundle\Entity\AdministrativeService $service
     *
     * @return AdministrativeRole
     */
    public function setService(\CS\PlatformHandlingBundle\Entity\AdministrativeService $service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return \CS\PlatformHandlingBundle\Entity\AdministrativeService
     */
    public function getService()
    {
        return $this->service;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->managedRoles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->employees = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setIsActive(true);
    }

    /**
     * Add managedRole
     *
     * @param \CS\PlatformHandlingBundle\Entity\AdministrativeRole $managedRole
     *
     * @return AdministrativeRole
     */
    public function addManagedRole(\CS\PlatformHandlingBundle\Entity\AdministrativeRole $managedRole)
    {
        $this->managedRoles[] = $managedRole;

        return $this;
    }

    /**
     * Remove managedRole
     *
     * @param \CS\PlatformHandlingBundle\Entity\AdministrativeRole $managedRole
     */
    public function removeManagedRole(\CS\PlatformHandlingBundle\Entity\AdministrativeRole $managedRole)
    {
        $this->managedRoles->removeElement($managedRole);
    }

    /**
     * Get managedRoles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getManagedRoles()
    {
        return $this->managedRoles;
    }

    /**
     * Add employee
     *
     * @param \CS\PlatformHandlingBundle\Entity\CoolSchoolEmployee $employee
     *
     * @return AdministrativeRole
     */
    public function addEmployee(\CS\PlatformHandlingBundle\Entity\CoolSchoolEmployee $employee)
    {
        $this->employees[] = $employee;

        return $this;
    }

    /**
     * Remove employee
     *
     * @param \CS\PlatformHandlingBundle\Entity\CoolSchoolEmployee $employee
     */
    public function removeEmployee(\CS\PlatformHandlingBundle\Entity\CoolSchoolEmployee $employee)
    {
        $this->employees->removeElement($employee);
    }

    /**
     * Get employees
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEmployees()
    {
        return $this->employees;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return AdministrativeRole
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set published
     *
     * @param boolean $published
     *
     * @return AdministrativeRole
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }
}
