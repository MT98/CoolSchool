<?php

namespace CS\PlatformHandlingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Country
 *
 * @ORM\Table(name="country")
 * @ORM\Entity(repositoryClass="CS\PlatformHandlingBundle\Repository\CountryRepository")
 */
class Country
{

    /**
     * @ORM\OneToMany(targetEntity="CS\CustomersPlatformBundle\Entity\School", mappedBy="country") 
     */
    private $schools;

    /**
     * @ORM\OneToMany(targetEntity="CS\PlatformHandlingBundle\Entity\CoolSchoolEmployee", mappedBy="country") 
     */
    private $employees;


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
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255)
     */
    private $alt;


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
     * @return Country
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
     * Set url
     *
     * @param string $url
     *
     * @return Country
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set alt
     *
     * @param string $alt
     *
     * @return Country
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->employees = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add employee
     *
     * @param \CS\PlatformHandlingBundle\Entity\CoolSchoolEmployee $employee
     *
     * @return Country
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
     * Add school
     *
     * @param \CS\CustomersPlatformBundle\Entity\School $school
     *
     * @return Country
     */
    public function addSchool(\CS\CustomersPlatformBundle\Entity\School $school)
    {
        $this->schools[] = $school;

        return $this;
    }

    /**
     * Remove school
     *
     * @param \CS\CustomersPlatformBundle\Entity\School $school
     */
    public function removeSchool(\CS\CustomersPlatformBundle\Entity\School $school)
    {
        $this->schools->removeElement($school);
    }

    /**
     * Get schools
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSchools()
    {
        return $this->schools;
    }
}
