<?php

namespace CS\PlatformHandlingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomersRole
 *
 * @ORM\Table(name="customers_role")
 * @ORM\Entity(repositoryClass="CS\PlatformHandlingBundle\Repository\CustomersRoleRepository")
 */
class CustomersRole
{

    /**
     * @ORM\OneToMany(targetEntity="CS\CustomersPlatformBundle\Entity\Work", cascade={"persist"}, mappedBy="role")
     */
    private $works;

    /**
     * @ORM\ManyToMany(targetEntity="CS\PlatformHandlingBundle\Entity\CustomersRole")
     * 
     */
    private $managedRoles;


    /**
     * @ORM\ManyToOne(targetEntity="CS\PlatformHandlingBundle\Entity\CustomersService", inversedBy="roles")
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
     * @return CustomersRole
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
     * @return CustomersRole
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
     * @return CustomersRole
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
     * @return CustomersRole
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
     * @param \CS\PlatformHandlingBundle\Entity\CustomersService $service
     *
     * @return CustomersRole
     */
    public function setService(\CS\PlatformHandlingBundle\Entity\CustomersService $service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return \CS\PlatformHandlingBundle\Entity\CustomersService
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
        $this->setIsActive(true);
    }

    /**
     * Add managedRole
     *
     * @param \CS\PlatformHandlingBundle\Entity\CustomersRole $managedRole
     *
     * @return CustomersRole
     */
    public function addManagedRole(\CS\PlatformHandlingBundle\Entity\CustomersRole $managedRole)
    {
        $this->managedRoles[] = $managedRole;

        return $this;
    }

    /**
     * Remove managedRole
     *
     * @param \CS\PlatformHandlingBundle\Entity\CustomersRole $managedRole
     */
    public function removeManagedRole(\CS\PlatformHandlingBundle\Entity\CustomersRole $managedRole)
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return CustomersRole
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
     * @return CustomersRole
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

    /**
     * Add work
     *
     * @param \CS\CustomersPlatformBundle\Entity\Work $work
     *
     * @return CustomersRole
     */
    public function addWork(\CS\CustomersPlatformBundle\Entity\Work $work)
    {
        $this->works[] = $work;
        $work->setRole($this);

        return $this;
    }

    /**
     * Remove work
     *
     * @param \CS\CustomersPlatformBundle\Entity\Work $work
     */
    public function removeWork(\CS\CustomersPlatformBundle\Entity\Work $work)
    {
        $this->works->removeElement($work);
    }

    /**
     * Get works
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWorks()
    {
        return $this->works;
    }
}
