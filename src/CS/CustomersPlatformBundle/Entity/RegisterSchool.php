<?php

namespace CS\CustomersPlatformBundle\Entity;

use CS\CustomersPlatformBundle\Entity\School;
use CS\CustomersPlatformBundle\Entity\CustomerUser;
use CS\PlatformHandlingBundle\Entity\CustomersService;

/**
 * This class is used to create a flow for CS/CustomersPlatformBundle/Form/RegisterSchoolFlow.php with craueFormFlowBundle
 */
class RegisterSchool
{
    /*
    * For retrieving Data about School
    */
    private $school;

    /*
    * For retrieving Data about the School's administrator
    */
    private $customerUser;

    /*
    * For retrieving Data about School's subscriptions
    */
    private $customersServices;

    
    public function getSchool()
    {
        return $this->school;
    }
    public function setSchool(School $school)
    {
        $this->school = $school;
        return $this;
    }
    public function getCustomerUser()
    {
        return $this->customerUser;
    } 
    public function setCustomerUser(CustomerUser $customerUser)
    {
        $this->customerUser = $customerUser;
        return $this;
    }
    public function addCustomersService(CustomersService $customersService)
    {
        $this->customersServices[] = $customersService;
        return $this;
    }

    public function removeCustomersService(CustomersService $customersService)
    {
        $this->customersServices->removeElement($customersService);
    }

    /**
     * Get customersServices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCustomersServices()
    {
        return $this->customersServices;
    }


    public function __construct()
    {
        $this->customersServices =  new \Doctrine\Common\Collections\ArrayCollection();
        $this->school = new School();
        $this->customerUser = new CustomerUser();

    }
   
}