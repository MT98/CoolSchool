<?php

namespace CS\CustomersPlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use CS\CustomersPlatformBundle\CustomerUser;

class CustomerUserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        /* on crée un nouveau constructeur de formulaire pour les champs customerUser */
        $customerUserForm = $builder->create('customerUser', 'Symfony\Component\Form\Extension\Core\Type\FormType', array(
            'data_class' => 'CS\CustomersPlatformBundle\Entity\CustomerUser',
        ));

          
            $data = $builder->getData()->getCustomerUser();

            switch($options['flow_step'])
            {
                case 2:
                        $customerUserForm->add('email', EmailType::class);
                        break;
                case 3:
                        /* S'il l'utilisateur désigné comme admin est déjà dans notre base de données*/               
                        if($data->getId() != null)
                        {
                            $customerUserForm
                            /* on désactive tous les champs y compris l'email qui était déjà créé */
                    
                            ->add('email', EmailType::class, array('disabled'=>true))
                            ->add('firstName', TextType::class, array('disabled'=>true))
                            ->add('lastName', TextType::class, array('disabled'=>true))
                            ->add('telephone', TextType::class, array('disabled'=>true));
                        }else
                        {
                            /* on désactive seulement l'email qui était déjà créé et on ajoute les autres champs */
                            $customerUserForm
            
                            ->add('email', TextType::class, array('disabled'=>true))
                            ->add('firstName', TextType::class)
                            ->add('lastName', TextType::class)
                            ->add('telephone', TextType::class);
                        }
                        break;
            }
            
    
        
        /* On ajoute au constructeur de départ ce nouveau constructeur */
        $builder->add($customerUserForm);
        
    }
  

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cs_customersplatformbundle_customeruser';
    }


}
