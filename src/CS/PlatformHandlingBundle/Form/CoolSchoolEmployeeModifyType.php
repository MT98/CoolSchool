<?php

namespace CS\PlatformHandlingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use CS\PlatformHandlingBundle\Form\ImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use CS\PlatformHandlingBundle\Entity\CoolSchoolEmployee;

class CoolSchoolEmployeeModifyType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('telephone', TextType::class)
        ->add('address', TextType::class)
        ->add('works', EntityType::class, array(
            'class'        => 'CSPlatformHandlingBundle:AdministrativeRole',
            'choice_label' => 'name',
            'multiple'     => true,
        ))
        ->add('photo', ImageType::class, array('required'=>false))
        ->add('save', SubmitType::class);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $employee = $event->getData();
            $form = $event->getForm();
    
            /* Si le compte de l'employee est déja activé aucun autre champs n'est requis */
            if($employee->getIsActive() == true)
            {
                $form   
                ->add('email', EmailType::class, array('disabled'=> true))
                ->add('firstName', TextType::class, array('disabled'=> true))
                ->add('lastName', TextType::class, array('disabled'=> true))
                ->add('country', EntityType::class, array(
                    'class'        => 'CSPlatformHandlingBundle:Country',
                    'choice_label' => 'name',
                    'placeholder' => 'Sélectionner le pays où il travaille',
                     'disabled'=> true
        
                ));
     
                return;
            }

            /* Sinon on peut tout modifier */
            $form   
                ->add('email', EmailType::class)
                ->add('firstName', TextType::class)
                ->add('lastName', TextType::class)
                ->add('country', EntityType::class, array(
                    'class'        => 'CSPlatformHandlingBundle:Country',
                    'choice_label' => 'name',
                    'placeholder' => 'Sélectionner le pays où il travaille'
        
                ));
        });
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
        return 'cs_platformhandlingbundle_coolschoolemployeemodify';
    }


}
