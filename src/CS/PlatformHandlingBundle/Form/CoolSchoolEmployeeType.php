<?php

namespace CS\PlatformHandlingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use CS\PlatformHandlingBundle\Form\ImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CoolSchoolEmployeeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('email', EmailType::class)
        ->add('firstName', TextType::class)
        ->add('lastName', TextType::class)
        ->add('telephone', TextType::class)
        ->add('address', TextType::class)
        ->add('photo', ImageType::class, array('required'=>false))
        ->add('country', EntityType::class, array(
            'class'        => 'CSPlatformHandlingBundle:Country',
            'choice_label' => 'name',
            'placeholder' => 'Sélectionner le pays où il travaille'

        ))
        ->add('works', EntityType::class, array(
            'class'        => 'CSPlatformHandlingBundle:AdministrativeRole',
            'choice_label' => 'name',
            'multiple'     => true,
        ))
        ->add('save', SubmitType::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CS\PlatformHandlingBundle\Entity\CoolSchoolEmployee'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cs_platformhandlingbundle_coolschoolemployee';
    }


}
