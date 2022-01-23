<?php

namespace CS\PlatformHandlingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;




class CustomersRoleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('service', EntityType::class, array(
            'class'        => 'CSPlatformHandlingBundle:CustomersService',
            'choice_label' => 'name',
            'multiple'     => false,
            'placeholder' => 'Sélectionnez le service concerné !'
        ))
        ->add('name', TextType::class)
        ->add('code', TextType::class)
        ->add('description', TextareaType::class)
        ->add('published', CheckboxType::class, array('required'=> false))
        ->add('foroneuser', CheckboxType::class, array('required'=> false))
        ->add('managedRoles', EntityType::class, array(
            'class'        => 'CSPlatformHandlingBundle:CustomersRole',
            'choice_label' => 'name',
            'multiple'     => true,
            'required'     => false
        ))
        ->add('save', SubmitType::class);

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CS\PlatformHandlingBundle\Entity\CustomersRole'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cs_platformhandlingbundle_customersrole';
    }


}
